<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2013-2020 Italian Linux Society - http://www.linux.it

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
	function gen_uuid () {
		return sprintf ('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand (0, 0xffff), mt_rand (0, 0xffff),
			mt_rand (0, 0xffff),
			mt_rand (0, 0x0fff) | 0x4000,
			mt_rand (0, 0x3fff) | 0x8000,
			mt_rand (0, 0xffff), mt_rand (0, 0xffff), mt_rand (0, 0xffff)
		);
	}

	$msg = '';

	if (array_key_exists ('subscribe', $_GET)) {
		if (array_key_exists ('name', $_POST) == true && $_POST ['name'] != '') {
			header ('Location: http://lugmap.linux.it/');
			exit ();
		}

		if (array_key_exists ('mail', $_POST) == false ||
				$_POST ['mail'] == '' ||
				array_key_exists ('prov', $_POST) == false ||
				array_key_exists ('university', $_POST) == false ||
				($_POST ['prov'] == '-1' && $_POST ['university'] == '-1') ||
				filter_var ($_POST ['mail'], FILTER_VALIDATE_EMAIL) == false) {

			$msg = "Dati non validi! Riscrivi il tuo indirizzo mail e seleziona una provincia o una università!";
		}
		else {
			$mail = $_POST ['mail'];

			if ($_POST ['prov'] != '-1')
				$prov = $_POST ['prov'];
			else
				$prov = $_POST ['university'];

			$date = date ('d-m-Y');
			$uuid = gen_uuid ();

			$data = file ('../data/radar.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$counter = 0;

			foreach ($data as $d) {
				list ($m, $p, $u) = explode ('|', $d);

				if ($m == $mail)
					$counter++;

				unset ($m);
				unset ($p);
				unset ($u);
			}

			unset ($data);

			/*
				Controllo per evitare che qualcuno abusi del
				servizio: ci si puo' registrare al massimo tre
				volte con lo stesso indirizzo
			*/
			if ($counter < 3) {
				$f = fopen ('../data/radar_pending.txt', 'a');
				fwrite ($f, "$mail|$prov|$uuid|$date\n");
				fclose ($f);

				$headers = "From: webmaster@linux.it\r\n";

				$message =<<<TEXT
Grazie per esserti registrato sul LugRadar!

Questa e' una mail di verifica, per testare la validita' del tuo
indirizzo mail: per cortesia clicca il link seguente per completare la
procedura:

http://lugmap.linux.it/radar/confirm.php?id=$uuid


Per qualsiasi dubbio o domanda, contatta l'indirizzo webmaster@linux.it

TEXT;

				/*
					Questo e' per iniettare il nuovo utente nella newsletter ILS
				*/
				file ("http://www.linux.it/subscribe.php?name=$mail&prov=" . $_POST ['prov'] . "&auto=1");

				mail ($mail, 'Conferma iscrizione LugRadar', $message, $headers);
			}

			header ('Location: http://lugmap.linux.it/radar/confirm.php');
			exit ();
		}
	}

	require_once ('../funzioni.php');
	lugheader ('Radar', array ('http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css'), array ('http://code.jquery.com/ui/1.10.3/jquery-ui.js', 'radar.js'));

	if ($msg != '') {
		?>

		<p style="text-align: center; border: 2px solid #FF0000"><?php echo $msg ?></p>

		<?php
	}

?>

<table width="70%" align="center">
	<tr>
		<td>
			<img src="/immagini/radar.png" style="float: right" class="radar" />

			<p>I Linux Users Groups sono tantissimi, ma mai abbastanza!</p>
			<br />
			<p>Nella tua zona non c'è un LUG? O è troppo distante? Ci sono altri appassionati di software libero e tecnologia nella tua università, nella tua azienda, nel tuo quartiere, ma non sai come identificarli e coinvolgerli?</p>
			<p>Costruire un LUG non è complicato, e da oggi lo è ancora meno!</p>
			<p>Indica qui di seguito il tuo indirizzo mail e la tua provincia, quando qualcun'altro nella stessa area farà altrettanto riceverete entrambi un messaggio per mettervi reciprocamente in contatto, conoscervi, eventualmente incontrarvi, e magari presto fondare un nuovo Users Group insieme!</p>

			<br />

			<form action="?subscribe=1" method="POST">
				<p>
					<input type="text" name="mail" placeholder="Indirizzo Mail" />
				</p>
				<p>
					<?php prov_select ('') ?> o, se sei studente universitario,<br />
					<input type="text" name="universitytext" id="universitytext" size="50" placeholder="Scrivi e seleziona qui il nome della tua università" />
					<input type="hidden" name="university" id="university" value="-1" />
				</p>
				<p>
					<input type="submit" value="Invia" />
				</p>
				<p style="display: none">
					<input type="text" name="name" />
				</p>
			</form>

			<br />

			<p>Leggi il <a href="http://www.ils.org/progetti#servizi">Manuale Operativo per la Community</a> o <a href="http://www.ils.org/contatti">chiedi consulto a ILS</a> per maggiori informazioni sulla creazione di un LUG.</p>

			<p>&nbsp;</p>
		</td>
	</tr>
</table>

<?php
	lugfooter ();
?>
