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

		default:
			include ('visualizza-regione.php');
			break;
	}

	exit (0);
}

require_once ('varie.php');
do_head ('Homepage', array ('http://openlayers.org/api/OpenLayers.js', 'js/mappa.js'));

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
		?>

		<input type="hidden" name="zooming_lat" value="<?php echo $lat ?>" />
		<input type="hidden" name="zooming_lon" value="<?php echo $lon ?>" />

		<?php
	}
}

?>

<div id="map" class="smallmap"></div>

<?php do_foot (); ?>
