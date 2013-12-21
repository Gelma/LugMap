<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2010-2014  Italian Linux Society - http://www.linux.it

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

require_once ('utils.php');
require_once ('../funzioni.php');

$format = 'javascript';
$head = 'true';
$head_color = 'FFA200';
$head_text_color = 'FFFFFF';
$foot = 'true';
$width = '200';

if (array_key_exists ('format', $_GET) == true)
	$format = $_GET ['format'];

if (array_key_exists ('head', $_GET) == true)
	$head = $_GET ['head'];

if (array_key_exists ('head_color', $_GET) == true)
	$head_color = $_GET ['head_color'];

if (array_key_exists ('head_text_color', $_GET) == true)
	$head_text_color = $_GET ['head_text_color'];

if (array_key_exists ('foot', $_GET) == true)
	$foot = $_GET ['foot'];

if (array_key_exists ('width', $_GET) == true && is_numeric ($_GET ['width']))
	$width = $_GET ['width'];

if ($format == 'image') {
	$region = $_GET ['region'];
	$dir = 'tmp/lugmap_widget_cache/';

	if (file_exists ($dir) == false)
		mkdir ($dir);

	$path = "${dir}/${region}-${width}-${head}-${head_color}-${head_text_color}-${foot}.png";

	if (file_exists ($path) == false)
		$attr = false;
	else
		$attr = stat ($path);

	if ($attr === false || ($attr ['mtime'] < (time () - 86400))) {
		/*
			Dalla larghezza dichiarata sottraggo 6 pixel, che e' la
			larghezza del bordo scuro incluso nell'immagine finale.
			Alla fine, l'immagine sara' larga esattamente quanto
			richiesto
		*/
		$correct_width = $width - 6;

		exec ("/usr/local/bin/wkhtmltoimage-i386 --width $width \"$app_url/widget.php?region=$region&format=html&head=$head&foot=$foot&head_color=$head_color&head_text_color=$head_text_color&width=$correct_width\" $path");
	}

	header ("Content-Type: image/png");
	$im = imagecreatefrompng ($path);
	imagepng ($im);
	imagedestroy ($im);
	exit ();
}

if ($format == 'html')
	$endline = '';
else if ($format == 'javascript')
	$endline = "\\";

$page =<<<PAGE
<div style="margin: 0px; border: 3px solid #000000; font-family: Helvetica; font-size: 12px; text-align: center; width: ${width}px;"> $endline
PAGE;

/**
	REGIONE NON VALIDA
**/
if (array_key_exists ('region', $_GET) == false || (in_array ($_GET ['region'], array_keys ($elenco_regioni)) == false) && $_GET ['region'] != 'all') {
	$page .=<<<PAGE
<div style="margin: 5px; padding: 3px; background-color: #F54B4B;"> $endline
	<p>Oops, non hai specificato alcuna regione valida.</p> $endline
</div>
PAGE;
}

else {
	if ($_GET ['region'] == 'all') {
		$lugs = array ();

		foreach (glob ('../db/*.txt') as $db_file) {
			$lugs = array_merge ($lugs, file ($db_file));
			sort ($lugs);
		}

		$regionname = 'tutta Italia';
	}
	else {
		$lugs = file ('../db/' . ($_GET ['region']) . '.txt', FILE_IGNORE_NEW_LINES);
		$regionname = $elenco_regioni [$_GET ['region']];
	}

	/**
		REGIONE SENZA LUG
	**/
	if ($lugs == false || count ($lugs) == 0) {
		$page .=<<<PAGE
<div style="margin: 5px; padding: 3px; background-color: #F54B4B;"> $endline
	<p>Non sembrano esserci LUG in $regionname.</p> $endline
	<p><a style="text-decoration: none;" href="http://www.badpenguin.org/italian-lug-howto" target="_blank">Creane uno!</a></p> $endline
</div>
PAGE;

	}

	/**
		ELENCO VALIDO
	**/
	else {
		if ($head == 'true') {
			$page .=<<<PAGE
			<div style="font-weight: bold; background-color: #$head_color; color: #$head_text_color; border: 1px solid black; padding: 5px;"> $endline
				<p>Cerchi un Linux Users Group in $regionname?</p> $endline
			</div> $endline
PAGE;
		}

		$page .=<<<PAGE
	<table style="border-collapse: collapse; margin: auto; padding: 10px; width: 100%;">
PAGE;

		$nriga = 0;

		while (list ($nriga, $lug) = each ($lugs)) {
			$data = explode ('|', $lug);

			if ($nriga % 2)
				$css = 'background-color: #EEEEEE';
			else
				$css = 'background-color: #DDDDDD';

			$city = ($format == 'javascript') ? addslashes ($data [0]) : $data [0];
			$name = ($format == 'javascript') ? addslashes ($data [1]) : $data [1];
			$link = ($format == 'javascript') ? addslashes ($data [3]) : $data [3];

			$page .=<<<PAGE
			<tr style="font-family: Helvetica; font-size: 12px; text-align: center; $css;"> $endline
				<td style="padding: 5px; border: 1px solid black; font-weight: bold;">$city</td> $endline
				<td style="padding: 5px; border: 1px solid black;"><a style="text-decoration: none;" href="$link" target="_blank">$name</a></td> $endline
			</tr>
PAGE;
		}

$page .=<<<PAGE
</table>
PAGE;

	}
}

/**
	FOOTER COMUNE
**/
if ($foot == 'true') {
	$page .=<<<PAGE
	<div style="margin-top: 5px; font-style: italic; color: #000000; font-weight: bold;"> $endline
	Powered by <a style="color: #FF0000; text-decoration: none;" href="http://lugmap.linux.it/">lugmap.linux.it</a> $endline
	</div> $endline
	</div>
PAGE;
}

if ($format == 'html') {
	echo '<html><body style="margin: 0px; border: 0px">' . $page . '</body></html>';
}
else if ($format == 'javascript') {
	header ('Content-Type: application/javascript');

	?>
function renderLugMap () {
	var holder = document.getElementById('lugmap');
	map = document.createElement('div');
	map.innerHTML = '<?php echo $page ?>';
	holder.parentNode.insertBefore(map, holder);
	holder.parentNode.removeChild(holder);
	map.setAttribute('id', 'lugmap');
}

	<?php
}
?>
