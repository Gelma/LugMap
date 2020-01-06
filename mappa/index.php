<?php
/*
  Codice della mappa dei LUG italiani
  Copyright (C) 2010-2020  Italian Linux Society - http://www.linux.it/

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once ('../funzioni.php');
lugheader ('Mappa',
		array ('http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css'),
		array ('http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js', 'mappa.js'));

$transformed = false;

if (array_key_exists ('zoom', $_GET)) {
	$found = false;
	$lat = $lon = 0;
	$contents = file_get_contents ('../data/geo.txt');
	$contents = json_decode ($contents, true);

	foreach ($contents ['features'] as $row) {
		if ($row ['properties']['name']== $_GET ['zoom']) {
			$found = true;
			$lat = $row ['geometry']['coordinates'][1];
			$lon = $row ['geometry']['coordinates'][0];
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
	<input type="hidden" name="default_zoom" value="5" />
	<?php
}

?>

<input type="hidden" name="coords_file" value="/data/geo.txt" />
<div id="map"></div>

<!-- Qui il footer non c'e' di proposito, la pagina e' interamente occupata dalla mappa -->

