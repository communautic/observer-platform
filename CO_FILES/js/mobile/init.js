// JavaScript Document

$(document).ready(function() { 
	
	// prevent entire body to move
	/*$('body').bind('touchmove',function(e){
      	e.preventDefault();
	});*/
	
	// Desktop Postits Edit
	$('#desktop div.postit-text').livequery(function() {
		$(this).addSwipeEvents().live('doubletap', function(e, touch) {
			$(this).trigger('dblclick');
		});
	});
	
	
	
	
	$('.ui-datepicker-trigger-action').datepicker().on('show', function (e) {
        $(this).trigger('blur');
    })
	

    /*$('span').each(function() {

        var clicked = false;

        $(this).bind('click', function() {

            if(!clicked) return !(clicked = true);
        });
    });*/


	//$(".sortable").('disable');
	
});