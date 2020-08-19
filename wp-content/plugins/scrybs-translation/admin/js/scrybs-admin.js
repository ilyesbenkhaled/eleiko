var mouse_in = false;
jQuery(document).ready(function($){

	generatePreviewCode();

	$('.js-scrybs-navigation-links a').on('click', function(e) { // prevent default scrolling for navigation links
        e.preventDefault();

        var $target = $( $(this).attr('href') );

        if ( $target.length !== 0 ) {
            var offset = 0;
            var wpAdminBar = jQuery('#wpadminbar');
            if ( wpAdminBar.length !== 0 ) {
                offset = wpAdminBar.height();
            }

            $('html, body').animate({
                scrollTop: $target.offset().top - offset
             }, 300, function() {
                var $header = $target.find('.scrybs-section-header h3');
                $header.addClass('active');
                console.log($header);
                setTimeout(function(){
                    $header.removeClass('active');
                }, 700);
             });
        }

        return false;
    });
    
    var sourcedom = $('select#scrybs-source').val();
    $("input[value='"+sourcedom+"']").parent().parent().hide();
    
    $('select#scrybs-source').change(function() {
    	$(".tlang").show();  
	    var source = $(this);
	    $("input[value='"+source.val()+"']").parent().parent().fadeOut('slow');   
	});
	
	$('#scrybs-source[readonly="readonly"]').on('focus mousedown', function(e) {
		 if ($.browser.webkit||$.browser.msie) {
		 	e.preventDefault();
		 }else{
		    this.blur();
		    window.focus();
		 }
	});
	
	$('.scrybsclose-btn').click(function() {
		$('.scrybsbox-blur').hide();
	});
	
	$('.is-api-key p.submit input#submit').click(function(e) {
           var checked = $(':checkbox:checked').length;
           var offset = 0;
           var wpAdminBar = jQuery('#wpadminbar');
           if ( wpAdminBar.length !== 0 ) {
                offset = wpAdminBar.height();
           }
           if (checked == 0) {
                $('html, body').animate({
			        scrollTop: $("#lang-sec-1").offset().top - offset
			    }, 300);
				e.preventDefault();
		   }
    });
    
    $('.tlang input[type="checkbox"]').bind('change', function(){
    	generatePreviewCode();
    });
    
    $('select[name="scrybs[language_names]"]').change(function() {
		generatePreviewCode();
	});
	
	$('select[name="scrybs[icons]"]').change(function() {
		generatePreviewCode();
		if( $(this).val() == 'flags' ){ $('#flags-style, #flags-countries').show(); }else{ $('#flags-style, #flags-countries').hide(); }
	});
	
	$( 'select[name="scrybs[en_flag]"], select[name="scrybs[es_flag]"], select[name="scrybs[pt_flag]"], select[name="scrybs[fr_flag]"], select[name="scrybs[de_flag]"]' ).change(function() {
		generatePreviewCode();
	});
	
	$('input[name="scrybs[is_dropdown]"]').change(function() {
		generatePreviewCode();
	});
	
	$('input[name="scrybs[is_dropdown]"]').change(function() {
		generatePreviewCode();
	});
	
	$('input[name="scrybs[arrow_style]"], input[name="scrybs[flag_style]"]').change(function() {
		generatePreviewCode();
	});
	
	function returnFlagCode(code) {
		var flcode = '';
		switch(code) {
		 case 'en':
		 	flcode = 'gb';
		 break;
		 case 'sq':
		 	flcode = 'al';
		 break;
		 case 'da':
		 	flcode = 'dk';
		 break;
		 case 'ms':
		 	flcode = 'my';
		 break;
		 case 'vi':
		 	flcode = 'vn';
		 break;
		 case 'sr':
		 	flcode = 'rs';
		 break;
		 case 'fa':
		 	flcode = 'ir';
		 break;
		 case 'bs':
		 	flcode = 'ba';
		 break;
		 case 'eu':
		 	flcode = 'iq';
		 break;
		 case 'sl':
		 	flcode = 'si';
		 break;
		 case 'zu':
		 	flcode = 'za';
		 break;
		 case 'el':
		 	flcode = 'gr';
		 break;
		 case 'hy':
		 	flcode = 'am';
		 break;
		 case 'hi':
		 	flcode = 'in';
		 break;
		 case 'et':
		 	flcode = 'ee';
		 break;
		 case 'sv':
		 	flcode = 'se';
		 break;
		 case 'uk':
		 	flcode = 'ua';
		 break;
		 case 'ur':
		 	flcode = 'pk';
		 break;
		 case 'ko':
		 	flcode = 'kr';
		 break;
		 case 'ne':
		 	flcode = 'np';
		 break;
		 case 'ta':
		 	flcode = 'lk';
		 break;
		 case 'ja':
		 	flcode = 'jp';
		 break;
		 case 'ga':
		 	flcode = 'ie';
		 break;
		 case 'af':
		 	flcode = 'za';
		 break;
		 case 'ar':
		 	flcode = 'sa';
		 break;
		 case 'zh-CN':
		 	flcode = 'cn';
		 break;
		 case 'zh-TW':
		 	flcode = 'tw';
		 break;
		 case 'pt-pt':
		 	flcode = 'pt';
		 break;
		 case 'pt-br':
		 	flcode = 'br';
		 break;
		}
		if(flcode == ''){
			return code;
		}else{
			return flcode;
		}
	}
    
    function generatePreviewCode() {
		var source = $( 'select[name="scrybs[source]"] option:selected' ).val();
		var sourcename = $( 'select[name="scrybs[source]"] option:selected' ).text().replace(''+source+'', '').replace(' ()', '').replace(' (auto detect)', '');
		var list='';
		
		var targets = [];
		var targetnames = [];
	    $('.tlang :checked').each(function() {
	       targets.push($(this).val());
	       targetnames.push($(this).parent().find('strong').text());
	    });
	    	    	    
	    var lnames_option = $( 'select[name="scrybs[language_names]"]' ).val();
	    
	    var icons_option = $( 'select[name="scrybs[icons]"]' ).val();
	    
	    var arrowstyle = ' '+$( 'input[name="scrybs[arrow_style]"]:checked' ).val();
	    
	    if( icons_option == 'flags' ){ 
	    	$('#flags-style, #flags-countries').show();
	    	var flagstyle = ' '+$( 'input[name="scrybs[flag_style]"]:checked' ).val();
	    }else{ 
	    	$('#flags-style, #flags-countries').hide(); 
	    	var flagstyle = '';
	    }
	    
	    if(icons_option == 'flags'){
			var flags = ' sc-flags'+flagstyle;
			var globe = '';
			var en_flag = $( 'select[name="scrybs[en_flag]"]' ).val();
			var es_flag = $( 'select[name="scrybs[es_flag]"]' ).val();
			var pt_flag = $( 'select[name="scrybs[pt_flag]"]' ).val();
			var fr_flag = $( 'select[name="scrybs[fr_flag]"]' ).val();
			var de_flag = $( 'select[name="scrybs[de_flag]"]' ).val();
			$('#flags-countries select').prop('disabled', false);
		}else if(icons_option == 'globe'){
			var flags = '';
			var globe = '<i class="fa fa-globe" aria-hidden="true"></i> ';
			$('#flags-countries select').prop('disabled', true);
		}else if(icons_option == 'noicon'){
			var flags = '';
			var globe = '';
			$('#flags-countries select').prop('disabled', true);
		}
		
		if(typeof en_flag != 'undefined' && source == 'en'){
			source = en_flag;
		}else if(source == 'en'){
			source = 'uk';
		}
		if(typeof es_flag != 'undefined' && source == 'es'){
			source = es_flag;
		}
		if(typeof pt_flag != 'undefined' && source == 'pt'){
			source = pt_flag;
		}else if(source == 'pt'){
			source = 'br';
		}
		if(typeof fr_flag != 'undefined' && source == 'fr'){
			source = fr_flag;
		}else if(source == 'fr'){
			source = 'fr';
		}
		if(typeof de_flag != 'undefined' && source == 'de'){
			source = de_flag;
		}else if(source == 'de'){
			source = 'de';
		}
		
		source = returnFlagCode(source);
		
		$('.esflag, .ptflag, .frflag, .deflag').hide();
		$('.esflag select, .ptflag select, .frflag select, .deflag select').prop('disabled', true);
		
		$('.'+source+'flag').show();
		$('.'+source+'flag select').prop('disabled', false);
				
		if(targets.length>0){
			list +='<ul>';
			for(var i=0;i<targets.length;i++) { 
					var code = targets[i];
					var name = targetnames[i];
				    flcode = returnFlagCode(code);
				    
				    $('.'+code+'flag').show();
					$('.'+code+'flag select').prop('disabled', false);
			
					if(typeof en_flag != 'undefined' && code == 'en'){
						flcode = en_flag;
					}else if(code == 'en'){
						flcode = 'gb';
					}
					if(typeof es_flag != 'undefined' && code == 'es'){
						flcode = es_flag;
					}else if(code == 'es'){
						flcode = 'es';
					}
					if(typeof pt_flag != 'undefined' && code == 'pt'){
						flcode = pt_flag;
					}else if(code == 'pt'){
						flcode = 'br';
					}
					if(typeof fr_flag != 'undefined' && code == 'fr'){
						flcode = fr_flag;
					}else if(code == 'fr'){
						flcode = 'fr';
					}
					if(typeof de_flag != 'undefined' && code == 'de'){
						flcode = de_flag;
					}else if(code == 'de'){
						flcode = 'de';
					}
					if(lnames_option == 'full_names'){
						list += '<li class="sc-li '+flcode+flags+'"><a href="#">'+name+'</a></li>';
					}else if(lnames_option == 'code_names'){
						list += '<li class="sc-li '+flcode+flags+'"><a href="#">'+code.toUpperCase()+'</a></li>';
					}else if(lnames_option == 'no_names'){
						list += '<li class="sc-li '+flcode+flags+'"><a href="#">&nbsp;</a></li>';
					}
			}
			list +='</ul>';
		}
		if(lnames_option == 'full_names'){
			var source_lang = '<div class="sc-current sc-li '+source+flags+arrowstyle+'"><a href="javascript:void(0);">'+globe+sourcename+'</a></div>';
		}else if(lnames_option == 'code_names'){
			var source_lang = '<div class="sc-current sc-li '+source+flags+arrowstyle+'"><a href="javascript:void(0);">'+globe+source.toUpperCase()+'</a></div>';
		}else if(lnames_option == 'no_names'){
			var source_lang = '<div class="sc-current sc-li '+source+flags+arrowstyle+'"><a href="javascript:void(0);">'+globe+'&nbsp;</a></div>';
		}
		
		if($('input[name="scrybs[is_dropdown]"]').is(':checked')) {
			var btn_type = "sc-drop";
			$('.scrybs-switcher-preview').css('width', 'auto').css('text-align', 'center');
		}else { 
			var btn_type = "sc-list";
			$('.scrybs-switcher-preview').css('text-align', 'unset');
		}
				
					
		var button = '<aside id="scrybs_switcher" notranslate class="'+btn_type+' country-selector closed" onclick="openClose(this);">'+source_lang+list+'</aside>';
		$(".scrybs-switcher-preview").html(button);
		}
		
		 $('#scrybs_switcher').hover(function(){ 
	        mouse_in=true; 
	    }, function(){ 
	        mouse_in=false; 
	    });
	
	    $("body").mouseup(function(){ 
	        if(! mouse_in){ if(!$('.country-selector').hasClass('closed')){$('.country-selector').addClass('closed');} }
	    });
});
	
	/* This function was forked from Weglot */
	function openClose(link) {
		link.className = (link.className.indexOf("country-selector closed") < 0 ) ? link.className.replace("country-selector","country-selector closed"):link.className.replace("country-selector closed","country-selector");
		return false;
	}
	
	/* This function was forked from Weglot */
	function getOffset (element) {
		var top = 0, left = 0;
		do {
			top += element.offsetTop  || 0;
			left += element.offsetLeft || 0;
			element = element.offsetParent;
		} while(element);
	
		return {
			top: top,
			left: left
		};
	}