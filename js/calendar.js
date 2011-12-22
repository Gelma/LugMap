$(document).ready (function () {
	$('.calendar_table_tab').hide ();

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

	$('#calendar_table_toggle').click (function () {
		$('#calendar_map_toggle').removeClass ('calendar_style_toggle_selected');
		$('.calendar_map_tab').hide ();
		$('#calendar_table_toggle').addClass ('calendar_style_toggle_selected');
		$('.calendar_table_tab').show ();
	});

	$('#calendar_map_toggle').click (function () {
		$('#calendar_table_toggle').removeClass ('calendar_style_toggle_selected');
		$('.calendar_table_tab').hide ();
		$('#calendar_map_toggle').addClass ('calendar_style_toggle_selected');
		$('.calendar_map_tab').show ();
	});
});

