<?php

require_once ('../../varie.php');

$format = 'javascript';
$head = 'true';
$head_color = '000080';
$head_text_color = 'FFFFFF';
$foot = 'true';
$width = '200';
$border = '3';

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

if (array_key_exists ('border', $_GET) == true && is_numeric ($_GET ['border']))
	$border = $_GET ['border'];

if ($format == 'image') {
	$path = "cache/$width-$border-$head-$head_color-$head_text_color-$foot.png";

	if (file_exists ($path) == false) {
		/*
			Dalla larghezza dichiarata sottraggo 6 pixel, che e' la
			larghezza del bordo scuro incluso nell'immagine finale.
			Alla fine, l'immagine sara' larga esattamente quanto
			richiesto
		*/
		$correct_width = $width - 6;

		exec ("/usr/local/bin/wkhtmltoimage-i386 --width $width \"$main_url/forge/events/widget.php?format=html&head=$head&foot=$foot&head_color=$head_color&head_text_color=$head_text_color&width=$correct_width\" $path");
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
<div style="margin: 0px; border: ${border}px solid #000000; font-family: Helvetica; font-size: 12px; text-align: center; width: ${width}px;"> $endline
PAGE;

$current_day = date ('d');
$current_month = date ('m');
$year = date ('Y');

$events = file ("$year.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

for ($events_index = 0; $events_index < count ($events); $events_index++) {
	$row = $events [$events_index];
	list ($date, $useless) = explode ('|', $row);
	list ($d, $m) = explode ('/', $date);

	if ($m < $current_month || ($m == $current_month && $d < $current_day)) {
		$events_index--;
		break;
	}
}

$final_events = array ();

while (true) {
	for (; $events_index >= 0; $events_index--) {
		$row = $events [$events_index];
		$final_events [] = $row;
	}

	unset ($events);
	$year++;

	if (file_exists ("$year.txt")) {
		$events = file ("$year.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$events_index = count ($events) - 1;
	}
	else {
		break;
	}
}

/**
	NESSUN EVENTO
**/
if (count ($final_events) == 0) {
	$page .=<<<PAGE
<div style="margin: 5px; padding: 3px; background-color: #F54B4B;"> $endline
	<p>Non sembrano esserci eventi imminenti.</p> $endline
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
			<p>Prossimi eventi Linux in Italia</p> $endline
		</div> $endline
PAGE;
		}

		$page .=<<<PAGE
	<table style="border-collapse: collapse; margin: auto; padding: 10px; width: 100%;">
PAGE;

	$nriga = 0;

	while (list ($nriga, $ev) = each ($final_events)) {
		$data = explode ('|', $ev);

		if ($nriga % 2)
			$css = 'background-color: #EEEEEE';
		else
			$css = 'background-color: #DDDDDD';

		$date = ($format == 'javascript') ? addslashes ($data [0]) : $data [0];
		$city = ($format == 'javascript') ? addslashes ($data [2]) : $data [2];
		$name = ($format == 'javascript') ? addslashes ($data [3]) : $data [3];
		$link = ($format == 'javascript') ? addslashes ($data [4]) : $data [4];

		$page .=<<<PAGE
		<tr style="font-family: Helvetica; font-size: 10px; text-align: center; $css;"> $endline
			<td style="padding: 3px; border: 1px solid black; font-weight: bold;">$date</td> $endline
			<td style="padding: 3px; border: 1px solid black; font-weight: bold;">$city</td> $endline
			<td style="padding: 3px; border: 1px solid black;"><a style="text-decoration: none;" href="$link" target="_blank">$name</a></td> $endline
		</tr>
PAGE;
	}

	$page .=<<<PAGE
	</table>
PAGE;

}

/**
	FOOTER COMUNE
**/
if ($foot == 'true') {
	$page .=<<<PAGE
	<div style="margin-top: 5px; font-style: italic; color: #000000; font-weight: bold;"> $endline
	Powered by <a style="color: #FF0000; text-decoration: none;" href="http://calendar.lugmap.it/">lugmap.it</a> $endline
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
	var holder = document.getElementById('calendarlugmap');
	map = document.createElement('div');
	map.innerHTML = '<?php echo $page ?>';
	holder.parentNode.insertBefore(map, holder);
	holder.parentNode.removeChild(holder);
	map.setAttribute('id', 'calendarlugmap');
}

	<?php
}
?>
