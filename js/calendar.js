function switch_tab_view (selected) {
	var calendar_tabs_names = ['calendar_map', 'calendar_table', 'calendar_widget'];

	for (i = 0; i < calendar_tabs_names.length; i++) {
		n = calendar_tabs_names [i];

		if (n == selected) {
			$('#' + n + '_toggle').addClass ('calendar_style_toggle_selected');
			$('.' + n + '_tab').show ();
		}
		else {
			$('#' + n + '_toggle').removeClass ('calendar_style_toggle_selected');
			$('.' + n + '_tab').hide ();
		}
	}
}

$(document).ready (function () {
	$('.calendar_table_tab').hide ();
	$('.calendar_widget_tab').hide ();

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
		switch_tab_view ('calendar_table');
	});

	$('#calendar_map_toggle').click (function () {
		switch_tab_view ('calendar_map');
	});

	$('#calendar_widget_toggle').click (function () {
		switch_tab_view ('calendar_widget');
	});
});

