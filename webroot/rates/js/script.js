var scroll_speed_time = 20000;
$(document).ready(function(){
	updateTime();
	setInterval(function(){
		updateTime();
	},1000);
});


var firstTime = true;
/*function scrollDown(el) {
	firstTime = false;
	el.animate({
        scrollTop: el[0].scrollHeight
    }, 10000, function() {
        scrollUp(el)
    });
}*/

function scrollDown(el) {
	firstTime = false;
	el.animate({
        scrollTop: $(document).height()
    }, 10000, function() {
        scrollUp(el)
    });
};

function scrollUp(el) {
    el.animate({
        scrollTop: 0
    }, 10000, function() {
    	if(firstTime==false){
    		setTimeout(function(){
    			scrollDown(el);
    		},10000);
    	}else{
    		scrollDown(el);
    	}
    });
}

//scrollUp($("html, body"));

  /*$("html, body").animate({ scrollTop: $(document).height() }, scroll_speed_time*2);
  setTimeout(function() {
       $('html, body').animate({scrollTop:0}, scroll_speed_time*2); 
  },scroll_speed_time);
	
  var scrolltopbottom =  setInterval(function(){
         // 4000 - it will take 4 secound in total from the top of the page to the bottom
    $("html, body").animate({ scrollTop: $(document).height() }, scroll_speed_time);
    setTimeout(function() {
       $('html, body').animate({scrollTop:0}, scroll_speed_time*2); 
    },scroll_speed_time);

},scroll_speed_time*2);
*/
function updateTime()
{
	var currentdate = new Date(); 
	var min = currentdate.getMinutes();
	var hr24 = currentdate.getHours();
	var hr12 = hr24%12;
	var datetime = ((Number(hr12)>=10)?hr12:"0"+hr12) 
					+ ":"  
					+ ((Number(min)>=10)?min:"0"+min) 
					+ " "  
					+ ((hr24>=12)?"PM":"AM");
	$('.rateTime').html(datetime);
}
var lock=0;