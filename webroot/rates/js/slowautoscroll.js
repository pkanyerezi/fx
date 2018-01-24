/* Slow Auto Scroll - Javascript Plugin Settings */
$.fn.slowautoscroll = function(options) {

  var scrollAmount = document.body['clientHeight'];
  var scrollDown = true;
  var scrollSpeedMultiplier = 40;
  var scrollSpeed = scrollAmount * scrollSpeedMultiplier;
  
  // function to animate speed
  function autoScroll() {
	if(scrollDown){
		stopScroll();
		$('body,html').animate({scrollTop: scrollAmount}, scrollSpeed,function(){
			stopScroll();
			scrollDown=false;
			autoScroll();
		});
	}else{
		stopScroll();
		$('body,html').animate({scrollTop: 0}, scrollSpeed,function(){
			stopScroll();
			scrollDown=true;
			autoScroll();
		});	
	}
  }
  
  setInterval(function(){
	if($(window).scrollTop() + $(window).height() == $(document).height()) {
       stopScroll();
	   scrollDown = false;
	   autoScroll();
    }
  },5000);


  function stopScroll() {
    $('body,html').stop();
  }
  // default speed settings, edit these to set your own speeds!
  var settings = {
    slowSpeed: 75000,
    mediumSpeed: 40000, 
    fastSpeed: 20000
  };
  // make user amounts happen 
  $.extend(settings, options);
  //console.log(settings);
  
  setTimeout(function() {
        autoScroll(settings.fastSpeed);
  },5000);
  
  // scroll speeds made to work
  // slow speed (turtle)
  $('.slow').on('click', function(){
    autoScroll(settings.slowSpeed);
    $('.scroll').text('Stop Scrolling!');
    $('.speeds').toggle();
  });
  // medium speed (rabbit)
  $('.med').on('click', function(){
    autoScroll(settings.mediumSpeed);
    $('.scroll').text('Stop Scrolling!');
    $('.speeds').toggle();
  });
  // fast speed (cheetah)
  $('.fast').on('click', function(){
    autoScroll(settings.fastSpeed);
    $('.scroll').text('Stop Scrolling!');
    $('.speeds').toggle();
  });  
  // stops whichever speed was picked
  $('.scroll').on('click', function(){
    stopScroll();
    $('.scroll').text('Pick Scroll Speed');
    $('.speeds').toggle();
  });
  return this;
};

$(function(){
  $('body,html').slowautoscroll({
  });
});