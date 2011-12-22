<?php

require_once ('varie.php');
do_head ('Eventi Linux in Italia', array ('js/calendar.js', 'http://openlayers.org/api/OpenLayers.js', 'js/mappa.js'));

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

<?php
do_foot ();
?>
