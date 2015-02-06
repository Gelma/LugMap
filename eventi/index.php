<?php
/*
  Codice della mappa dei LUG italiani
  Copyright (C) 2010-2014  Italian Linux Society - http://www.linux.it/

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
lugheader ('Eventi',
		array ('http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css'),
		array ('http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js', 'mappa.js'));

?>

<input type="hidden" name="default_zoom" value="5" />
<input type="hidden" name="coords_file" value="geoeventswrap.php" />

<div class="mapoverlay" style="left: 10px; padding: 10px;">
	<p>
		Quasi ogni giorno da qualche parte in Italia si svolge un incontro tra appassionati Linux: tutte occasioni per approfondire il tema, incontrare utenti esperti cui porre qualche domanda, e condividere il proprio interesse con altri.
	</p>
	<p>
		In questa mappa sono indicizzati gli eventi per il prossimo futuro: dai una occhiata alla tua zona per sapere se è previsto un appuntamento, oppure immetti qui il tuo indirizzo mail e la provincia di residenza per essere notificato in occasione dei prossimi.
	</p>

	<div class="events_subscribe">
		<?php if (isset ($_GET ['subscribed']) == false): ?>
		<form style="webform-client-form" action="http://www.linux.it/subscribe.php" method="GET">
			<input type="text" name="mail" class="trap" placeholder="Indirizzo Mail" /><br/>
			<input type="email" name="name" class="form-text" placeholder="Indirizzo Mail" /><br/>
			<?php echo prov_select ('form-select') ?><br/>
			<input type="submit" class="form-submit" value="Iscriviti" />
		</form>
		<?php else: ?>
		<p class="alert alert-success">Sei stato iscritto alle notifiche</p>
		<?php endif; ?>
	</div>
</div>

<div class="mapoverlay" style="bottom: 20px; right: 10px;">
	<iframe id="newsframe" src="http://www.linux.it/calendar.php?months=1" frameBorder="0" width="100%" height="100%"></iframe>
</div>

<div id="map"></div>

<!-- Qui il footer non c'e' di proposito, la pagina e' interamente occupata dalla mappa -->

