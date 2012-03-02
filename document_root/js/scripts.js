jQuery.fn.anchorAnimate = function(settings) {

 	settings = jQuery.extend({
		speed : 400
	}, settings);

	return this.each(function(){
		var caller = this
		$(caller).click(function (event) {
			event.preventDefault()
			var locationHref = window.location.href
			var elementClick = $(caller).attr("href")

			var destination = $(elementClick).offset().top;
			$("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, settings.speed, function() {
				window.location.hash = elementClick
			});
		  	return false;
		})
	})
}

$(document).ready(function() {
    baseline = 18;

    $('.lightbox a').lightBox({
	imageBtnClose: '/images/close.png',
	imageBtnPrev: '/images/previous.png',
	imageBtnNext: '/images/next.png',
	txtImage: 'Obrázek',
	txtOf: 'z'
    });

    
    $('.menu .items ul').hide();
    $('.menu .items').hover(function() {
	    $(this).find('ul').slideDown('fast');
    }, function() {
	    $(this).find('ul').slideUp('fast');
    });

    $('.showAbstract').click(function() {
	    $(this).fadeOut('fast');


	  
	    $('#abstract').show('fast');
	  

	    return false;
    })
    $('.addComment.folded a.buttonLink').click(function() {
	$(this).next('form').slideDown('fast');
	return false;
    });
    
    $('a[href=#discussion]').anchorAnimate('fast');
	
    $('a[href=#factCheck]').click(function() {
	$('.showAbstract').hide();
	$('#abstract').show('fast', function() {	    
	    $('#abstract form').slideDown('fast', function() {
		$("html:not(:animated),body:not(:animated)").animate({scrollTop: $('#factCheck').offset().top}, 'fast', function() {
		    window.location.hash = '#factCheck';
		});
	    });
	});
	return false;
	
	
    });
    
    $('.comment .header .fp-2').toggle(function() {
	$('.comment').css({opacity: '.3'});
	$(this).parent().parent('.comment').css({opacity: '1'});
    }, function() {
	$('.comment').css({opacity: '1'});
    })














	$('#header .services a.rss').mouseenter(function() {
	$('#header .services .rss-box').fadeIn('fast');
    })
    $('#header .services .rss-box').mouseleave(function() {
	$(this).fadeOut('fast');
    })

    $('.flashMessage').delay(3000).fadeOut('fast');

    $(window).scroll(function() {
	if ($(window).scrollTop() > 130) {
	    $('#sharebox:hidden').fadeIn('fast');
	}
	if ($(window).scrollTop() < 130) {
	    $('#sharebox:visible').fadeOut('fast');
	}
    })
})