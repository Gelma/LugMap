<?php

require_once ('../../varie.php');

if (array_key_exists ('html', $_GET) == true)
	$html = $_GET ['html'];
else
	$html = false;

if ($html == true)
	$endline = '';
else
	$endline = "\\";

$page =<<<PAGE
<div style="border: 3px solid #000000; font-family: Helvetica; font-size: 12px; text-align: center;"> $endline
PAGE;

/**
	REGIONE NON VALIDA
**/
if (array_key_exists ('region', $_GET) == false || in_array ($_GET ['region'], array_keys ($elenco_regioni)) == false) {
	$page .=<<<PAGE
<div style="margin: 5px; padding: 3px; background-color: #F54B4B;"> $endline
	<p>Oops, non hai specificato alcuna regione valida.</p> $endline
</div>
PAGE;
}

else {
	$lugs = file ('../../db/' . ($_GET ['region']) . '.txt', FILE_IGNORE_NEW_LINES);
	$regionname = $elenco_regioni [$_GET ['region']];

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
		$page .=<<<PAGE
<div style="font-weight: bold; background-color: #000080; color: #FFFFFF; border: 1px solid black; padding: 5px;"> $endline
	<p>Cerchi un Linux Users Group in $regionname?</p> $endline
</div> $endline
	<table style="border-collapse: collapse; margin: auto; padding: 10px; width: 100%;">
PAGE;

		$nriga = 0;

		while (list ($nriga, $lug) = each ($lugs)) {
			$data = explode ('|', $lug);

			if ($nriga % 2)
				$css = 'background-color: #EEEEEE';
			else
				$css = 'background-color: #DDDDDD';

			$city = ($html == false) ? addslashes ($data [0]) : $data [0];
			$name = ($html == false) ? addslashes ($data [1]) : $data [1];
			$link = ($html == false) ? addslashes ($data [3]) : $data [3];

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
$page .=<<<PAGE
<div style="margin-top: 5px; font-style: italic; color: #000000; font-weight: bold;"> $endline
Powered by <a style="color: #FF0000; text-decoration: none;" href="http://lugmap.it/">lugmap.it</a> $endline
</div> $endline
</div>
PAGE;

if ($html == true) {
	echo $page;
}
else {
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
