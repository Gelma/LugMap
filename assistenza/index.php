<?php
/*
  Codice della mappa dei LUG italiani
  Copyright (C) 2010-2021  Italian Linux Society - http://www.linux.it/

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
lugheader ('Assistenza',
		array ('https://unpkg.com/leaflet@0.6.4/dist/leaflet.css'),
		array ('https://unpkg.com/leaflet@0.6.4/dist/leaflet.js', 'mappa.js'));

?>

<input type="hidden" name="coords_file" value="geoeventswrap.php" />
<input type="hidden" name="support_file" value="support.json" />

<div class="mapoverlay" style="left: 10px; padding: 10px;">
	<p>
		In questa mappa sono indicizzati gli incontri periodici di assistenza su Linux: dai una occhiata alla tua zona per sapere se è previsto un appuntamento!
	</p>
</div>

<div id="map"></div>

<!-- Qui il footer non c'e' di proposito, la pagina e' interamente occupata dalla mappa -->

