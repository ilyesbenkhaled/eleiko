$scrybs = jQuery.noConflict();    

var mouse_in = false;

jQuery(document).ready(function($scrybs){
	
	$scrybs('#scrybs_switcher').hover(function(){ 
	     mouse_in=true; 
	}, function(){ 
	     mouse_in=false; 
	});
	
	$scrybs("body").mouseup(function(){ 
		if(! mouse_in){ if(!$scrybs('.country-selector').hasClass('closed')){$scrybs('.country-selector').addClass('closed');} }
	});
	
	$scrybs('#scrybs_switcher').click(function(){ 
		if(!$scrybs(this).hasClass('closed')){ $scrybs(this).addClass('closed'); }else{ $scrybs(this).removeClass('closed'); }
	});
});   