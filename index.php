<?php

if ($_SERVER['HTTP_HOST'] != 'lugmap.it' AND $_SERVER['HTTP_HOST'] != 'www.lugmap.it') {
	include('visualizza-regione.php');
	exit (0);
}

require_once ('varie.php');
do_head ('Homepage', array ('http://openlayers.org/api/OpenLayers.js', 'js/mappa.js'));

?>

<div id="map" class="smallmap"></div>

<?php do_foot (); ?>
