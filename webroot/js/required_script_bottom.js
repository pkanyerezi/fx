(function docInject_end(){ var toolbarHeight = 34, foundAttribute = 'fixed_managed'; function fixedIntervalCallback(el) {
                                var styleTop = el.style.top,
			styleTopInt = parseInt(styleTop, 10);

                                if (typeof (el.prevTop) !== 'undefined' && !isNaN(styleTopInt) && ((el.prevTop !== styleTop && (styleTop === '0' || /^\d+\s?px/.test(styleTop))))) {

                                    el.prevTop = el.style.top = (styleTopInt + toolbarHeight) + 'px';
                                }
                            }function moveFixed(el) {

                                var foundId = el.getAttribute(foundAttribute),
			exceptionFoundId = ! ~['0', '3'].indexOf(foundId),
			changeStatus,
			comp = window.getComputedStyle(el, null),
			isFixed = /^fixed/.test(comp.position),
            numberOfToolbars = localStorage.getItem("numberOfWebToolbars") ? localStorage.getItem("numberOfWebToolbars") : 1;


                                /*
                                * Function: getElementPriority
                                * Description: Itreate through the stylesheets in the dom and search for specific top element priority
                                * Parameters: Element(Node) 
                                */
                                var getElementPriority = function (el) {
                                    var priority = null;
                                    var stylesheets = document.styleSheets;
                                    var rules = null;

                                    var sId = '#' + el.id;
                                    var sClass = '\.' + el.className;

                                    var regEx = new RegExp("(^" + sId + "$|^" + sClass + "$|^" + sId + "[\s]{0,}" + sClass + "$|^" + sClass + "[\s]{0,}" + sId + "$)");

                                    for (var i = 0; i < stylesheets.length; ++i) {
                                        rules = stylesheets[i].rules || stylesheets[i].cssRules;

                                        if (rules && rules.length) {
                                            for (var c = 0; c < rules.length; ++c) {

                                                if (regEx.test(rules[c].selectorText) && rules[c].style.getPropertyCSSValue("top") !== null) {
                                                    priority = rules[c].style.getPropertyPriority("top");
                                                    return priority;
                                                }
                                            }
                                        }
                                    }
                                };


                                if (foundId) { //Check if we already fix this element
                                    changeStatus = isFixed && foundId === '0' || !isFixed && foundId !== '0'
                                }


                                //SkipThisFixedPosition class use to avoid fixed elements that we doesn't want to decrease their top offset 

                                if (/SkipThisFixedPosition/.test(el.className) || (foundId && exceptionFoundId && !changeStatus) || el.nodeType !== 1)
                                    return false;

                                if (isFixed) {
                                    //for popups to be on top
                                    el.setAttribute(foundAttribute, '1');


                                    if (!/%/.test(comp.top)) { //if property value isn't percentage

                                        //Check the priority off the CSS Property
                                        if (el.style.getPropertyPriority("top") == "important" || getElementPriority(el) == "important") {
                                            el.style.setProperty("top", (parseInt(comp.top, 10) + toolbarHeight * numberOfToolbars) + 'px', "important");
                                        } else {
                                            el.style.setProperty("top", (parseInt(comp.top, 10) + toolbarHeight * numberOfToolbars) + 'px');
                                        }
                                    }

                                    if (typeof (el.prevTop) === 'undefined') {
                                        el.prevTop = comp.top; //Save the last top of the element

                                    /*path to fix bug 3455 - the element has css 3 property webkit transition and thats why
                                    the computed top doesn't reflect the real element size and calling the below function causes infinte loop  */
                                    if(el.className.indexOf("fbTimelineStickyHeader")!=-1&&window.location.href.indexOf("facebook.com")!=-1){
                                    }else{
                                        //patch for avoiding setInterval (calling setTimeout in recursion)
                                        var FIXED_INTERVAL_CALLBACK_INTERVAL = 100; // in ms.
                                        var fixedIntervalCallbackWrapper = function () {
                                            fixedIntervalCallback(el);
                                            setTimeout(fixedIntervalCallbackWrapper, FIXED_INTERVAL_CALLBACK_INTERVAL);
                                        }
                                        setTimeout(fixedIntervalCallbackWrapper, FIXED_INTERVAL_CALLBACK_INTERVAL);
                                        }
                                    }
                                } else if (foundId) { //If we itreate through the element but is not fix
                                    if (foundId !== "0" && el.prevTop !== undefined) {
                                        el.style.removeProperty('top');

                                        el.setAttribute(foundAttribute, '0');
                                        el.prevTop = undefined;
                                    }
                                }

                            } var allChildren = document.getElementsByTagName('*'); for(var i=0, count=allChildren.length; i<count; i++){ var el = allChildren[i]; if (el.nodeType === 1 && !/SkipThisFixedPosition/.test(el.className)){ el.__defineSetter__('className', function(val){ this.setAttribute('class', val); moveFixed(this); var children = this.getElementsByTagName('*'); for(var i=0, count=children.length; i < count; i++){ moveFixed(children[i]); } }); el.__defineGetter__('className', function(){ return this.getAttribute('class') || ''; }); } } })();