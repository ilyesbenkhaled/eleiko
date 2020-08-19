$(document).ready(function(){

    if(typeof location.hash !== 'undefined' && location.hash.length > 0) {
        $(".docs-menu li.active").removeClass('active');
        $('.docs-menu li a[href="'+location.hash+'"]').parent().addClass('active');
        $('.docs-content .section-content.docs_show').removeClass('docs_show');
        $('.docs-content .section-content' + location.hash).addClass('docs_show');
    }

    $('.docs-menu').on('click', 'li a[href^="#"]', function (e) {
        e.preventDefault();
        $(this).parents('.docs-menu').find("li.active").removeClass('active');
        $(this).parent().addClass('active');
        var _id = $(this).attr('href');
        window.location.replace(_id);
        $('body').animate({'scrollTop': 200}, 300);
        $('.docs-content .section-content.docs_show').removeClass('docs_show');
        $('.docs-content .section-content' + _id).addClass('docs_show');
    })

    $(window).on('scroll', function (e) {
        if($(this).scrollTop() > 900) {
            $('footer .go-top-buton').show(200);
        } else {
            $('footer .go-top-buton').hide(200);
        }
    });

    $('footer .go-top-buton').on('click', function (e) {
        $('body').animate({'scrollTop': 200}, 300);
    });
});