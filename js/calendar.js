$(document).ready (function () {
	$('.marked').mouseenter (function () {
		var c = $(this).attr ('class').split (' ');

		var height = $(this).height();
		var top = $(this).offset().top + height + 2;
		var left = $(this).offset().left;

		$('#' + c).show();
		$('#' + c).css({'top':top, 'left':left});

		$(this).addClass ('markedhover');
	});

	$('.marked').mouseleave (function () {
		c = $(this).attr ('class').split (' ');
		$('#' + c [0]).hide ();
		$(this).removeClass ('markedhover');
	});
});

