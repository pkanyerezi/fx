(function docInject_start() {
        var toolbarHeight = 35,
			windowHeightMinusToolbar = document.documentElement.clientHeight - toolbarHeight,
			measurer;

        window.addEventListener('load', function () {
            window.toolbarLoad = true;

            // measurer is used to calculate the height of the page's contents. The height is used to detect scrolling:
            measurer = document.createElement('div');
            measurer.id = "toolbar_measurer";
            measurer.className = "SkipThisFixedPosition";
            measurer.style.position = 'fixed';
            measurer.style.bottom = '0';
            document.body.appendChild(measurer);



        }, false);
        window.addEventListener('resize', function () {
            // The HTML element's height should be the window height minus the toolbars' height:
            document.documentElement.style.pixelHeight = getHeight();
        }, false);

        function getHeight() {

            if (measurer) {
                measurer.style.display = "block";
                return Math.max(measurer.getClientRects()[0].bottom, 0);
            }

            return windowHeightMinusToolbar;
        }




        // If the HTML's height is queried by the page's scripts, return it without the toolbars' height



        document.documentElement.__defineGetter__('clientHeight', function () {
            return getHeight();
        });
        window.__defineGetter__('innerHeight', function () {
            return getHeight();
        });


        // When client rects are requested by the page's scripts, they should be modified to reflect the toolbar(s):
        var gc = HTMLElement.prototype.getClientRects;
        HTMLElement.prototype.getClientRects = function () {
            var res = gc.apply(this, arguments);
            if (res && res.length)
                res = res[0];
            else
                return null;


            return [{
                top: res.top - toolbarHeight,
                left: res.left,
                bottom: res.bottom - toolbarHeight,
                right: res.right,
                height: res.height,
                width: res.width
            }]
        };
        var gbc = HTMLElement.prototype.getBoundingClientRect;
        HTMLElement.prototype.getBoundingClientRect = function () {
            var res = gbc.apply(this, arguments);
            res.topVal = res.top - toolbarHeight;
            res.bottomVal = res.bottom - toolbarHeight;
            res.__defineGetter__('top', function () {
                return this.topVal;
            });
            res.__defineGetter__('bottom', function () {
                return this.bottomVal;
            });
            return res;
        };

        var wrappedMouseMoveHandlers = [];

        // Mouse move events are wrapped, so the pageY takes into account the toolbars' height (for mouseenter/mouseleave events, for example):
        function wrapMouseMove(owner) {
            var origAddEvent = owner.addEventListener,
				origRemoveEvent = owner.removeEventListener;

            owner.addEventListener = function (eventName, handler, useCapture) {
                if (eventName == 'mousemove') {
                    var wrapper = function (e) {
                        e.stopPropagation();

                        var ee = document.createEvent('MouseEvents');
                        ee.initMouseEvent(e.type, e.cancelBubble, e.cancelable, e.view, e.detail, e.screenX, e.screenY, e.clientX, e.clientY - toolbarHeight, e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, e.button, e.relatedTarget);
                        ee.__defineGetter__("target", function () { return e.target });
                        ee.__defineGetter__("srcElement", function () { return e.srcElement; });
                        ee.__defineGetter__("toElement", function () { return e.toElement });
                        handler.call(e.target, ee);
                    };

                    wrappedMouseMoveHandlers.push({ orig: handler, wrapped: wrapper });
                    return origAddEvent.call(this, eventName, wrapper, useCapture);
                } else origAddEvent.apply(this, arguments);
            }

            owner.removeEventListner = function (eventName, listener, useCapture) {
                if (eventName === "mousemove") {
                    for (var i = 0, count = wrappedMouseMoveHandlers.length; i < count; i++) {
                        var handlerPair = wrappedMouseMoveHandlers[i];
                        if (handlerPair.orig === listener) {
                            wrappedMouseMoveHandlers.splice(i, 1);
                            return origRemoveEvent.call(this, eventName, handlerPair.wrapped, useCapture);
                        }
                    }

                    return origRemoveEvent.apply(this, arguments);
                } else origRemoveEvent.apply(this, arguments);
            }
        }

        if (window.location.href.indexOf("docs.google.com") === -1 && window.location.href.indexOf("f-i.com/broadway/iPad/") === -1 && window.location.href.indexOf("facebook.com") === -1) {
            wrapMouseMove(Node.prototype);
            wrapMouseMove(window.constructor.prototype);
        }
    })()