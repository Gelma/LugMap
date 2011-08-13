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

?>

<div id="map" class="smallmap"></div>

<?php do_foot (); ?>
