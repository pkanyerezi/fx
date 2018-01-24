/**
 * Author: Namanya Hillary
 * Email -> namanyahillary@gmail.com
**/
var lock=0;
$(document).ready(function(){
	//Pagination
	$('.paging span a').addClass('btn').addClass('btn-small');
	
	//Edit Links
	prepare_ajax_links();
	
	//icons
	$('.action-view').html('<i class="icon icon-globe"></i> View');
	$('.action-edit').html('<i class="icon icon-edit"></i> Edit');
	$('.action-delete').html('<i class="icon icon-trash"></i> Delete');
	
	//Fetch data for clicked links
	$('.dynamic-content a, .use-ajax').click(function (){	
		if(!($(this).hasClass('no-ajax'))){
			if(!(confirmRequest($(this))))	return false;showLoading();
			_obj=$(this);
			
			if(lock==0)lock=1;
			else return;
			
			var data = {};
			if(lock==1){
				$.ajax({
					url: $(this).attr('data-target'),
					data: data,
					success: function(data) {lock==0;afterFetch(_obj,data);},
					error: function() {lock==0;}
				});
				//$.get($(this).attr('data-target'), function(data) {afterFetch(_obj,data);});
			}
		}
	});
	
	//submit Form data
	$(".dynamic-content form, .modal-body form").submit(function(e){
		e.preventDefault();
		var $form = $( this ),
		my_url = $form.attr( 'action' );
		dataString = $( this ).serialize();
		
		if(!(confirmRequest($(this))))	return false;showLoading();
		_obj=$(this);
		$.ajax({type: "POST",url: my_url,data: dataString,dataType: "html",
			success: function(data) {lock==0;afterFetch(_obj,data);} ,
			error: function() {lock==0;}
		}); 
	});
	
	//Fade out Flash Message
	setTimeout(function(){
		$('.flash-message').fadeOut('slow');
	},4000);
	
});

//Show that data is being sent/fetched from the server
function showLoading(){
	var img="<img class='loading-animation' src='/forexbureau_admin/img/spinner.gif' style='position:fixed;bottom:120px;left:220px;width:25px;height:25px'>";
	$('.dynamic-content').prepend(img);
}

//Remove the loading animation 
function removeLoading(){
	$('.loading-animation').remove();
}

function prepare_ajax_links(){
	//remove all the hrefs(hyperlinks)
	$('.dynamic-content a, .use-ajax').each(function(){
		if(!($(this).hasClass('no-ajax')) && ($(this).attr('href')!='#')){
			var reference_link=$(this).attr('href');
			$(this).attr('href','#');
			$(this).attr('data-target',reference_link);
			$(this).attr('onclick','return false;');
		}
		
			
		
	});
}

//called before a request is sent to confirm user

function confirmRequest(obj){
	var bool=1;
	var attr = obj.attr('data-confirm-text');
	if(obj.hasClass('confirm-first') && (typeof attr !== 'undefined' && attr !== false)){
		if(!(confirm(obj.attr('data-confirm-text')))){
			bool=0;
		}	
	}
	return bool;
}


//function called after the data has been fetched and the request is successfull
function afterFetch(obj,data){
	if(obj.hasClass('for-modal')){
		removeLoading();
		$('.modal-body').html(data);
		$("#view-modal").modal('show');
	}else{
		$('.dynamic-content').html(data);
	}
}



