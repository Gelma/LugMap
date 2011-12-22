<?php

if ($_SERVER ['HTTP_HOST'] != 'lugmap.it' && $_SERVER ['HTTP_HOST'] != 'www.lugmap.it') {
	$domain = explode ('.', $_SERVER ['HTTP_HOST']);
	$host = $domain [0];

	switch ($host) {
		case 'planet':
			include ('planet.php');
			break;

		case 'widget':
			include ('widget.php');
			break;

		case 'calendar':
			include ('calendar.php');
			break;

		default:
			include ('visualizza-regione.php');
			break;
	}

	exit (0);
}

require_once ('varie.php');
do_head ('Homepage', array ('http://openlayers.org/api/OpenLayers.js', 'js/mappa.js'));

$transformed = false;

if (array_key_exists ('zoom', $_GET)) {
	$found = false;
	$contents = file ('dati.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($contents as $row) {
		list ($lat, $lon, $lug, $useless) = explode ("\t", $row, 4);
		if ($lug == $_GET ['zoom']) {
			$found = true;
			break;
		}
	}

	if ($found == true) {
		$transformed = true;

		?>

		<input type="hidden" name="zooming_lat" value="<?php echo $lat ?>" />
		<input type="hidden" name="zooming_lon" value="<?php echo $lon ?>" />
		<input type="hidden" name="default_zoom" value="12" />

		<?php
	}
}

if ($transformed == false) {
	?>
	<input type="hidden" name="default_zoom" value="6" />
	<?php
}

?>

<input type="hidden" name="coords_file" value="dati.txt" />
<div id="map" class="smallmap"></div>

<?php do_foot (); ?>
