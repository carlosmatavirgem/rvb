$(function () {
	$('.carousel').carousel({ interval: 5000 });
	$(window).scroll(function() {
		if ($(document).scrollTop() > 165) {
			$('nav').addClass('shrink');
		} else {
			$('nav').removeClass('shrink');
		}
	});
});