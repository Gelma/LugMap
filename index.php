<?php
if ($_SERVER ['HTTP_HOST'] != 'lugmap.it' && $_SERVER ['HTTP_HOST'] != 'www.lugmap.it') {
	$domain = explode ('.', $_SERVER ['HTTP_HOST']);
	$host = $domain [0];

	switch ($host) {
		case 'widget':
			include ('widget.php');
			break;

		case 'calendar':
			header ('Location: http://www.linux.it/eventi');
			die ();
			break;

		case 'planet':
			header ('Location: http://planet.linux.it/');
			die ();
			break;

		default:
			include ('visualizza-regione.php');
			break;
	}

	exit (0);
}

require_once ('varie.php');
do_head ('Homepage', array ('http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js', 'js/mappa.js'));

$transformed = false;

if (array_key_exists ('zoom', $_GET)) {
	$found = false;
	

	$string = file_get_contents("lug.geojson");
	$contents=json_decode($string,true);
	
	$lat=0;
	$lon=0;
	foreach ($contents['features'] as $row) {
	
		if($row['properties']['name']== $_GET ['zoom']) {
			$found = true;
			$lat=$row['geometry']['coordinates'][1];
			$lon=$row['geometry']['coordinates'][0];
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

<input type="hidden" name="coords_file" value="lug.geojson" />
<div id="map" class="smallmap"></div>

<?php do_foot (); ?>
