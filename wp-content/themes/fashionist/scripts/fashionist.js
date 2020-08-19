// Header submenu fix
//window.onload = menufix;

function menufix() {
	"use strict";
	var nav = document.getElementById('main-nav');
	var submenu = document.getElementsByClassName('submenu big-submenu');

	var navoffset = nav.getBoundingClientRect();

	for(var i=0;i<submenu.length;i++) {
		var smoffset = submenu[i].getBoundingClientRect();
		var fixedoffset = smoffset.left - navoffset.left;
		submenu[i].style.marginLeft = '-' + fixedoffset + 'px';
	}
}

// Mini Slider JS code (01_homepage)
window.addEventListener('load', function () {
    ms = slider.new('ms');
    if(jQuery('div').hasClass('ms-slide')){
    	ms.load('mini-slider','ms-slide');
    }
});


var slider = {
	idn: '',
	cl: '',
	a: '',
	a_nav: '',
	aname: '',
	auton: '',
	k: 0,
	id: function(id){
		return document.getElementById(id);
	},
	new: function(aname){
		this.aname = aname;
		return slider;
	},
	load: function(id,cl){
	  	this.idn = id;
	    this.cl = cl;
	    var a = this.id(id).getElementsByClassName(cl);
	    this.a = a;
	    a[0].style.display = 'block';
	    var htm = '';
	    for(var i=0;i<a.length;i++){
	    	var i2 = i+1;
	    	htm += '<a class="ms-bullet" href="javascript://" onclick="'+this.aname+'.navigate('+i+');"></a> ';
	    }
	    this.id('ms-bullets').innerHTML = htm;
	    var a_nav = this.id('ms-bullets').getElementsByTagName('a');
	    this.a_nav = a_nav;
		this.a_nav[0].className = 'ms-bullet active';
	    this.autonavigate();
  	},
  	autonavigate: function(){
	  	if(this.k+1<this.a.length){
	    	k2 = this.k+1;
	    }else{
	    	k2 = 0;
	    }
	    var that = this;
		this.auton = setTimeout(function(){that.navigate(k2);},6000);
  	},

	navigate: function(e){
	  	if(e==undefined){e==this.k2;}
		for(var i=0;i<this.a.length;i++){
			if(i==e){
				this.slidere(i);
				this.k = i;
				clearInterval(this.auton);
				this.autonavigate();
			}else{
				this.sliderk(i);
			}
	    }
  	},
	slidere: function(i){
	    this.a[i].style.display = 'block';
	    this.a_nav[i].className = 'ms-bullet active';
	    var elem = this.a[i];
	    var that = this;
		setTimeout(function(){elem.className = that.cl+' active'},400);
	},
  	sliderk: function(i){
	   	var elem = this.a[i];
	    this.a_nav[i].className = 'ms-bullet';
		setTimeout(function(){elem.style.display = 'none'},1000);
		this.a[i].className = this.cl;
	}

}

// Currency dropdown

function open_close_dropdown(e) {
	"use strict";
	var dropdown = document.getElementById(e);
	if(dropdown.className == 'opened') {
		dropdown.className = 'closed';
	} else {
		dropdown.className = 'opened';
	}
}


// Show/Hide Shop Grid or Shop List view
function grid_list(show, hide) {
	"use strict";
	document.getElementById(show).style.display = 'block';
	document.getElementById(hide).style.display = 'none';
}

// Show/Hide the "+" icon and the actions menu on product cards
function show_actions(product_id) {
	"use strict";
	var product = document.getElementById(product_id);
	var actions = product.getElementsByClassName('actions')[0];
	var add_to_cart = product.getElementsByClassName('add-to-cart')[0];
	var close = product.getElementsByClassName('close')[0];

	actions.className = 'actions show';
	add_to_cart.className = 'add-to-cart hide';
	close.className = 'add-to-cart close show';
}

function hide_actions(product_id) {
	"use strict";
	var product = document.getElementById(product_id);
	var actions = product.getElementsByClassName('actions')[0];
	var add_to_cart = product.getElementsByClassName('add-to-cart')[0];
	var close = product.getElementsByClassName('close')[0];

	actions.className = 'actions';
	add_to_cart.className = 'add-to-cart show';
	close.className = 'add-to-cart close';
}

function show_submenu(id) {
	"use strict";
	var submenu = document.getElementById(id);
	if(submenu.style.height == 'auto') {
		submenu.style.height = '0';
	} else {
		submenu.style.height = 'auto';
	}
}

function open_cat(id) {
	"use strict";
	var cat = document.getElementById(id);
	if(cat.style.height == 'auto') {
		cat.style.height = '0';
	} else {
		cat.style.height = 'auto';
	}
}

// Payement table, payement description show animation (checkout page)

function show_payment(payment_id) {
	"use strict";
	var hide_class = document.getElementsByClassName('payment-type');

	for (var i = 0; i < hide_class.length; i++){
        hide_class[i].className = 'payment-type';
    }

	document.getElementById(payment_id).className = 'payment-type show';
}

/*<!-- LightBox JS -->*/
lightbox.option({
	'fadeDuration': 400,
	'resizeDuration': 400
});

// Equal height For product's
equalheight = function(container){
	var currentTallest = 0,
    currentRowStart = 0,
    rowDivs = new Array(),
    $el,
    topPosition = 0;
	jQuery(container).each(function() {
	   $el = jQuery(this);
	   jQuery($el).height('auto')
	   topPostion = $el.position().top;

	    if (currentRowStart != topPostion)
	   	{
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++)
			{
			rowDivs[currentDiv].height(currentTallest);
			}
			rowDivs.length = 0; // empty the array
			currentRowStart = topPostion;
			currentTallest = $el.height();
			rowDivs.push($el);
	   	} else {
			rowDivs.push($el);
			currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
	  	}
		for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
			rowDivs[currentDiv].height(currentTallest);
		}
	});
}


/*---- Revolution Slider ----*/
jQuery(window ).load(function($) {
	"use strict";
	setTimeout(function(){
		var counter = 0;
 		jQuery('.rev_slider .tp-revslider-mainul li').each(function() {
 			counter++;
 			jQuery(this).append('<div id="rs-current-page">'+counter+'</div>');
 		});
 		jQuery('.rev_slider').after('<div id="rs-pages"><span>'+counter+'</span></div>');
		var bottommargin = jQuery('.tp-bullets').height() + 50 + 'px';
		jQuery('.rev_slider .tp-leftarrow.tparrows.simplebullets').css('margin-bottom', bottommargin);

		jQuery('#rev_slider_1_1_forcefullwidth').bind("revolution.rev_slider.onloaded",function (e) { });
	}, 1000);

	equalheight('.product-listing-view .row .col-xs-12 .product-img');
});

jQuery(window).resize(function(){
	"use strict";
	equalheight('.product-listing-view .row .col-xs-12 .product-img');
});

// Open big search
jQuery(document).ready(function($){
	"use strict";
	jQuery('.search-open').click(function() {
		var search_container = jQuery('#bigsearch');
		search_container.addClass('show');
	});

	jQuery('#bigsearch .close-search').click(function() {
		var search_container = jQuery('#bigsearch');
		search_container.removeClass('show');
	});

	// Mobile navigation
	jQuery('.nav-open').click(function() {
		var nav = jQuery('#mobile-nav');
		var logo = jQuery('#mobile-header #logo');
		var nav_open = jQuery('.nav-open');
		nav.toggleClass('opened');

		if(nav.hasClass('opened')) {
			nav_open.css('left', nav.css('width'));
		} else {
			nav_open.css('left', '0');
		}
	});

	jQuery('#mobile-header #mobile-nav .menu li').each(function() {
		if(jQuery(this).hasClass('menu-item-has-children')) {
			var temName = jQuery(this).find('a').first().text();
			jQuery(this).find('a').first().replaceWith('<a href="#" onclick="show_submenu('+"'"+temName.toLowerCase()+"'"+')">'+temName+' <span class="icon-down"></span></a>');
			jQuery(this).find('.sub-menu').first().attr('id', temName.toLowerCase()).addClass('submenu-list');
		}
	});

	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

    $('.sizes.value .size > input').click(function(event) {
    	$(this).parent().parent().find('.size').each(function() {
    		$(this).removeClass('active');
    	});
    	$(this).parent().addClass('active');
    });

    $('.sizes.value .color > input').click(function(event) {
    	$(this).parent().parent().find('.color').each(function() {
    		$(this).removeClass('active');
    	});
    	$(this).parent().addClass('active');
    });

    /*-------- JS For products------*/
    // Color Variation for products
    $('.variation_product .product .colors .circle').on('click',function() {
		$(this).parent().parent().find('.circle').removeClass('active');
		$(this).addClass('active');
		//Set image as per butoon selection
		var globalSlug = $(this);
		var $temVar = $.parseJSON($(this).parent().attr('data-product_variations'));

		$.each($temVar, function(index, el) {

			if($temVar[index].attributes.attribute_pa_color == globalSlug.attr('data-id')){

				var temImgpath = $temVar[index].image['full_src'];
				var temId = $temVar[index].variation_id;
				var temColor = $temVar[index].attributes.attribute_pa_color;
				var temProId = globalSlug.parent().parent().data('id');

				globalSlug.parent().parent().find('.ajax_add_to_cart').each(function() {
				   $(this).attr('href', '?add-to-cart='+temProId+'&variation_id='+temId+'&attribute_pa_color='+temColor);
				});

				if(temImgpath !== ''){
					globalSlug.parent().parent().find('a > img').attr('src',temImgpath);
				}
			}

		});
	});

    function addToCart(p_id) {
    	"use strict";
		$.get( p_id , function() {

			var $fragment_refresh = {
			    url: wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ),
			    type: 'POST',
			    success: function( data ) {
			        if ( data && data.fragments ) {
			            $.each( data.fragments, function( key, value ) {
			                $( key ).replaceWith( value );
			            });

			            $( document.body ).trigger( 'wc_fragments_refreshed' );
			        }
			    }
			};
			jQuery.ajax( $fragment_refresh );
		});
	}

    /*------ Addto cart using ajax-----*/
    $('.variation_product .product .add_to_cart_button').on('click',function(e) {
		e.preventDefault();
		var href = $(this).attr('href');
		if(href == '?add-to-cart=#'){
			alert('You must meed to select colour first!!!');
			return false;
		}
		addToCart(href);
	});
});