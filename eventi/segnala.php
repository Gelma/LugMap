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
lugheader ('Segnala Eventi');

if (isset ($_GET ['action']) == true && $_GET ['action'] == 'add') {
	$url = $_POST ['url'];
	if (strncmp ($url, 'http', 4) != 0)
		$url = 'http://' . $url;

	$test = parse_url ($url);
	if ((isset ($test ['path']) == false || $test ['path'] == '/') && (isset ($test ['query']) == false)) {
		?>

		<p class="alert alert-error">No, non sono ammessi link alla homepage del sito. Linka una pagina di presentazione dell'evento.</p>

		<?php
	}
	else {
		file_put_contents ('../data/events_alerts.txt', "$url\n", FILE_APPEND);
		?>

		<p class="alert alert-success">Evento aggiunto con successo.</p>

		<?php
	}
}

?>

<table width="70%" align="center">
	<tr>
		<td>
			<p>Il modo preferenziale per segnalare eventi sul calendario linuxaro è... non segnalarli affatto, ma limitarsi ad annunciarli sul proprio sito.</p>
			<p>La maggior parte dei contenuti qui presenti vengono semi-automaticamente estrapolati dai feed RSS dei LUG indicizzati sulla <a href="http://lugmap.linux.it/">LugMap</a>, la cui lista viene rinnovata una volta alla settimana per mezzo di <a href="https://github.com/ItalianLinuxSociety/Planet/blob/master/lug/find_feeds.php">uno script</a>. Il link al feed deve essere recuperabile per mezzo del metodo standard di auto-discovery RSS, ovvero nella &lt;head&gt; del codice HTML della homepage deve essere presente un tag &lt;link rel="alternate"&gt; con l'URL al feed. Ad esempio:</p>
			<p style="text-align: center">&lt;link rel="alternate" type="application/rss+xml" title="ExampleLUG" href="http://www.example.org/feed" /&gt;</p>
			<p>Esiste <a href="https://raw.github.com/Gelma/LugMap/lugmap.it/forge/opml-generator/eccezioni.txt">un elenco di URL</a> deliberatamente ignorati, in particolare quelli facenti riferimento alle modifiche di piattaforme wiki (che non hanno alcun significato editoriale). Qualsiasi piattaforma di authoring web minimamente evoluta, come Wordpress Drupal o Joomla, supporta nativamente l'auto-discovery RSS.</p>

			<hr />

			<p>Per chi proprio non può fare a meno di usare un wiki come propria piattaforma principale, o per qualche motivo non riesce o non vuole pubblicare un proprio feed RSS nel formato sopra indicato, è possibile segnalare i propri eventi per mezzo del piccolo form qui sotto.</p>
			<p>Nella casella di testo occorre copiare l'indirizzo della pagina web che presenta l'evento; i link diretti alle homepage vengono automaticamente scartati, vengono presi in considerazione solo quelli diretti a pagine che forniscono le informazioni essenziali per partecipare all'attività.</p>

			<p style="text-align: center">
				<form action="?action=add" method="POST">
					URL: <input type="text" name="url" size="100" /> <input type="submit" value="Invia" />
				</form>
			</p>

			<hr />

			<p>In tutti i casi, siano gli eventi pescati automaticamente o manualmente segnalati, si raccomanda di indicare sempre nelle proprie pagine tutti i dati necessari per permettere al pubblico di partecipare: luogo (indirizzo, ma anche nome della città), data, orario, e possibilmente un indirizzo mail di contatto (non una mailing list cui doversi iscrivere!) presso cui chiedere maggiori informazioni.</p>
			<p>E' inoltre fortemente consigliato annunciare i propri eventi con qualche giorno di anticipo, sia per i tempi tecnici di inclusione nel calendario eventi (e dunque di propagazione della notizia) sia ovviamente per permettere alle persone di organizzarsi un poco per prendervi parte.</p>
		</td>
	</tr>
</table>

<?php lugfooter (); ?>

