<?php

require_once ('varie.php');
do_head ('Eventi Linux in Italia', array ('js/calendar.js', 'http://openlayers.org/api/OpenLayers.js', 'js/mappa.js', 'http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js', 'forge/events/generator.js'));

?>

<div>
	<div class="description">
		<p>
			Qui un calendario di eventi a tema linuxaro passati, presenti e futuri, prelevati direttamente da <a href="http://planet.lugmap.it">Planet LugMap</a>.
		</p>
		<p>
			Per maggiori informazioni su questa pagina, <a href="<?php echo $main_url ?>/calendar_info.php">guarda qui</a>.
		</p>
	</div>
</div>

<div class="calendar_toggler">
	<p id="calendar_map_toggle" class="calendar_style_toggle calendar_style_toggle_selected">Mappa</p>
	<p id="calendar_table_toggle" class="calendar_style_toggle">Calendario</p>
	<p id="calendar_widget_toggle" class="calendar_style_toggle">Widget</p>
</div>

<div class="calendar_map_tab">
	<input type="hidden" name="default_zoom" value="5" />
	<input type="hidden" name="coords_file" value="forge/events/geoevents.txt" />
	<div id="map" class="smallmap"></div>

	<p class="calendar_map_legend">
		<img alt="Evento passati" src="http://lugmap.it/images/past_events.png" />&nbsp;Evento occorso nell'ultimo mese <img alt="Prossimo evento" src="http://lugmap.it/images/icon.png" />&nbsp;Prossimo evento
	</p>
</div>

<div class="calendar_table_tab">
	<?php

	$month_names = array ('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
			'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');

	$current_month = date ('m');
	$current_day = date ('d');
	$year = date ('Y');
	$month = date ('m', strtotime ('-2 month'));

	if ($month > $current_month)
		$year = $year - 1;

	$events = file ("forge/events/$year.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	for ($events_index = 0; $events_index < count ($events); $events_index++) {
		$row = $events [$events_index];
		list ($date, $useless) = explode ('|', $row);
		list ($d, $m) = explode ('/', $date);

		if ($m < $month) {
			$events_index--;
			break;
		}
	}

	if ($events_index == count ($events))
		$events_index = count ($events) - 1;

	$row = $events [$events_index];
	list ($date, $useless, $location, $what, $url) = explode ('|', $row);
	list ($d, $m) = explode ('/', $date);
	$events_index--;

	/*
		Data all'americana: mese/giorno/anno
		Non ho voglia di gestire le locale...
	*/
	$start_day = date ('N', strtotime ("$month/1/$year"));

	$eof = false;

	?>

	<table class="calendar_top">
		<tr>

		<?php

		for ($i = 0; $i < 4; $i++) {
			if ($i % 2 == 0) {
				?>

				</tr><tr>

				<?php
			}

			?>

			<td>
				<table class="calendar_month">
					<caption><?php echo $month_names [$month - 1] ?></caption>

					<tr>
						<?php

						for ($a = 1; $a < $start_day; $a++) {
							?>
							<td>&nbsp;</td>
							<?php
						}

						for ($e = 0; $e < cal_days_in_month (CAL_GREGORIAN, $month, $year); $e++, $a++) {
							if ($a == 8) {
								$a = 1;

								?></tr><tr><?php
							}

							$this_day = $e + 1;

							$td_class = array ('day_cell');
							$contents = '';

							if ($this_day == $current_day && $month == $current_month)
								$td_class [] = 'today';

							if ($eof == false && $this_day == $d && $m == $month) {
								$day_id = "${this_day}_${month}_" . rand ();
								$td_class [] = 'marked';
								array_unshift ($td_class, $day_id);
								$contents = '<div class="day_dialog" id="' . $day_id . '">';

								do {
									$contents .= "<p>$location: <a href=\"$url\">$what</a></p>";

									if ($events_index == -1) {
										$eof = true;
										break;
									}

									$row = $events [$events_index];
									list ($date, $useless, $location, $what, $url) = explode ('|', $row);
									list ($d, $m) = explode ('/', $date);
									$events_index--;
								} while ($this_day == $d && $m == $month);

								$contents .= '</div>';
							}

							?>

							<td class="<?php echo join (' ', $td_class) ?>"><?php echo $this_day ?><?php echo $contents ?></td>

							<?php
						}

						?>
				</table>

				<?php

				$month += 1;
				$start_day = $a;

				if ($month == 13) {
					$month = 1;
					$year++;

					unset ($events);
					$events = file ("forge/events/$year.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					$events_index = count ($events) - 1;

					$row = $events [$events_index];
					list ($date, $useless) = explode ('|', $row);
					list ($d, $m) = explode ('/', $date);

					$eof = false;
				}
			}

			?>

			</td>

		</tr>
	</table>
</div>

<div class="calendar_widget_tab">
	<fieldset class="widget_options">
		<legend>Opzioni</legend>

		<p>
			<label for="width">Larghezza</label>
			<input name="width" type="text" value="200" size="4" />px
		</p>

		<p>
			<label for="border">Bordo</label>
			<input name="border" type="text" value="3" size="4" />px
		</p>

		<p>
			<label for="head">Mostra Header</label>
			<input name="head" type="checkbox" checked="yes" />
		</p>

		<p class="depends_on_header">
			<label for="head_color">Colore Header</label>
			<input name="head_color" type="color" value="#000080" data-text="hidden" data-hex="true" style="height: 15px; width: 20px;" />
		</p>

		<p class="depends_on_header">
			<label for="head_text_color">Colore Testo Header</label>
			<input name="head_text_color" type="color" value="#FFFFFF" data-text="hidden" data-hex="true" style="height: 15px; width: 20px;" />
		</p>

		<p>
			<label for="foot">Mostra Footer</label>
			<input name="foot" type="checkbox" checked="yes" />
		</p>

		<br />

		<p>
			<p>Questa opzione puo' essere selezionata per ottenere una immagina statica anziche' un blocco di JavaScript: utile per embeddare il widget all'interno di siti che non consentono l'inclusione di codice HTML complesso, tipo Wordpress.com</p>

			<label for="image">Genera Immagine</label>
			<input name="image" type="checkbox" />
		</p>
	</fieldset>

	<div style="width: 45%; float: right; text-align: center;">
		<div class="preview">
			<!-- Non usare l'URL con l'host, qui, altrimenti usando widget.lugmap.it o lugmap.it non tornano i conti con la same-origin policy -->
			<iframe id="calendarlugmap" src="forge/events/widget.php?format=html" onLoad="calcSize();" width="210px" scrolling="no" frameborder="0"></iframe>
		</div>

		<br />
		<br />
		<br />

		<p>Copia e incolla questo codice nella tua pagina web!</p>

		<textarea class="code" cols="45" rows="10"><?php echo htmlentities (
		'<script type="text/javascript" src="' . $main_url . '/forge/events/widget.php"></script>
		<img id="calendarlugmap" src="' . $main_url . '/forge/lug-o-matic/placeholder.png" onload="renderLugMap();" />') ?>
		</textarea>
	</div>
</div>

<?php
do_foot ();
?>
