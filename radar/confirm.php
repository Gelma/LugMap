<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2013  Italian Linux Society - http://www.linux.it

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.*/
?>
<?php
	require_once ('../funzioni.php');
	lugheader ('Radar');

?>

<table width="70%" align="center">
	<tr>
		<td>

		<?php

			if (array_key_exists ('id', $_GET)) {
				$mail = null;
				$prov = null;

				$now = time ();
				$pending = array ();
				$data = file ('../data/radar_pending.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

				foreach ($data as $d) {
					list ($m, $p, $u, $d) = explode ('|', $d);

					if ($u == $_GET ['id']) {
						$mail = $m;
						$prov = $p;
						$valid_row = $d;
					}
					else {
						/*
							Le registrazioni piu' vecchie di (circa...)
							10 giorni vengono eliminate
						*/
						if ($now - strtotime ($d) < 864000)
							$pending [] = $d;
					}

					unset ($m);
					unset ($p);
					unset ($u);
				}

				file_put_contents ('../data/radar_pending.txt', join ("\n", $pending));
				unset ($data);
				unset ($pending);

				if ($mail != null) {
					$valid = array ();
					$data = file ('../data/radar.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

					$f = fopen ('../data/radar.txt', 'a');
					fwrite ($f, "$valid_row\n");
					fclose ($f);

					foreach ($data as $d) {
						list ($m, $p, $u) = explode ('|', $d);

						if ($p == $prov)
							$valid [] = $m;

						unset ($m);
						unset ($p);
						unset ($u);
					}

					unset ($data);

					$count = count ($valid);

					if ($count != 0) {
						$to = join (', ', $valid);
						$headers = "From: webmaster@linux.it\r\n";
						$headers .= "To: webmaster@linux.it\r\n";
						$headers .= "Bcc: $to\r\n";

						$message =<<<TEXT
Per mezzo del LugRadar ( http://lugmap.linux.it/radar ), e' stato
intercettato un nuovo interessato a Linux ed al software libero nella
tua zona!

Mandagli una mail all'indirizzo
$mail
presentandoti, esplicitando come ti chiami, in che citta' abiti, ed
altre informazioni utili per rompere il ghiaccio.

Qualora vi trovaste in sintonia e decideste di fondare un nuovo Linux
Users Group, ricordate del supporto che Italian Linux Society puo'
fornirvi per iniziare a orientarvi e per condurre le vostre attivita':
http://www.ils.org/progetti#servizi


Persone cui e' stata inviata questa mail: $count

TEXT;

						mail ($to, 'LugRadar: individuati linuxari nella tua zona!', $message, $headers);
					}

					?>

					<p>
						Grazie per la tua conferma!
					</p>

					<p>
						Riceverai una mail di notifica quando altri appassionati, curiosi ed interessati
						a Linux verranno intercettati nella tua zona, in modo che possiate reciprocamente
						mettervi in contatto e, chissà, magari fondare un nuovo Linux Users Group e
						portare il software libero là dove nessuno lo ha ancora portato!
					</p>

					<p>
						Il tuo indirizzo mail verrà inoltre iscritto al
						<a href="http://www.linux.it/eventi">LugCalendar, l'indice degli eventi linuxari in Italia</a>,
						per essere notificato in caso di attività e appuntamenti vicini a casa tua organizzati
						da altre realtà locali e per ricevere la periodica newsletter informativa di
						<a href="http://www.ils.org/">Italian Linux Society</a>.
					</p>

					<p>
						In bocca al lupo per la tua ricerca di nuovi compagni e compagne di avventure!
						E <a href="/contatti">tienici aggiornati</a>!
					</p>

					<p>&nbsp;</p>

				<?php
			}
		}
		else { ?>

			<p>
				Grazie per esserti registrato sul Radar LugMap!
			</p>

			<p>
				Dovresti ora ricevere una mail di conferma, utile per evitare lo spam e l'abuso di questo servizio.
				Consulta la tua casella di posta e conferma la tua sottoscrizione cliccando il link!
			</p>

			<p>
				Qualora non ricevessi tale mail nelle prossime due ore, invia una segnalazione a
				<a href="mailto:webmaster@linux.it">webmaster@linux.it</a>
			</p>

			<p>&nbsp;</p>

		<?php } ?>

		</td>
	</tr>
</table>

<?php
	lugfooter ();
?>
