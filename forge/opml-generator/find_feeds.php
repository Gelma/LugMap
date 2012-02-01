<?php

/*
	SimplePie non gestisce i redirect, dunque questa funzione viene usata
	come filtro per identificare il vero URL di ogni pagina. Prima fa un
	controllo sui redirect HTTP, se non ne trova prova ad identificare dei
	tag META REFRESH nel codice HTML

	Fonti:
	http://stackoverflow.com/questions/427203/how-can-i-determine-if-a-url-redirects-in-php
	http://www.phpclasses.org/package/6317-PHP-Check-and-retrieve-the-redirection-URL-of-a-page.html
*/
function check_url ($url) {
	$ch = curl_init ($url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec ($ch);
	$code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);

	if (($code == 301) || ($code == 302)) {
		$ch2 = curl_init ($url);
		curl_setopt ($ch2, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_exec ($ch2);
		$url = curl_getinfo ($ch2, CURLINFO_EFFECTIVE_URL);
		curl_close ($ch2);
	}
	else {
		$keys = array ();
		$open = @file_get_contents ($url); 

		if (preg_match ('/<META HTTP-EQUIV="Refresh" CONTENT="(.*);URL=(.[^"]*)">/i', $open, &$keys)) {
			if (strncmp ('http://', $keys [2], 7) == 0 || strncmp ('https://', $keys [2], 8) == 0)
				$url = $keys [2];
			else
				$url = $url . $keys [2];
		}
	} 

	return $url;
}

// Richiede SimplePie!
// http://simplepie.org/
include_once ('/usr/share/php/simplepie/simplepie.inc');

$elenco_regioni = array (
	"abruzzo"    => "Abruzzo",
	"basilicata" => "Basilicata",
	"calabria"   => "Calabria",
	"campania"   => "Campania",
	"emilia"     => "Emilia Romagna",
	"friuli"     => "Friuli Venezia Giulia",
	"lazio"      => "Lazio",
	"liguria"    => "Liguria",
	"lombardia"  => "Lombardia",
	"marche"     => "Marche",
	"molise"     => "Molise",
	"piemonte"   => "Piemonte",
	"puglia"     => "Puglia",
	"sardegna"   => "Sardegna",
	"sicilia"    => "Sicilia",
	"toscana"    => "Toscana",
	"trentino"   => "Trentino Alto Adige",
	"umbria"     => "Umbria",
	"valle"      => "Valle d'Aosta",
	"veneto"     => "Veneto",
	"Italia"     => "Italia"
);

$exceptions = array ();

$exceptions_file = file ('eccezioni.txt', FILE_IGNORE_NEW_LINES);
if ($exceptions_file != false) {
	foreach ($exceptions_file as $ex) {
		if ($ex [0] == '#')
			continue;
		$exceptions [] = $ex;
	}
}

$feeds = array ();

foreach ($elenco_regioni as $region => $name) {
	$lugs = file ('http://github.com/Gelma/LugMap/raw/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);

	foreach ($lugs as $lug) {
		list ($prov, $name, $zone, $site) = explode ('|', $lug);
		$site = check_url ($site);

		$parser = new SimplePie ();
		$parser->set_feed_url ($site);
		$parser->set_autodiscovery_level (SIMPLEPIE_LOCATOR_AUTODISCOVERY);
		$parser->init ();
		$parser->handle_content_type ();
		if ($parser->error ())
			continue;

		$discovered = $parser->get_all_discovered_feeds ();

		foreach ($discovered as $f) {
			$skip = false;

			foreach ($exceptions as $exception) {
				if ($f == $exception) {
					$skip = true;
					break;
				}
			}

			if ($skip == true)
				continue;

			$obj = new stdClass ();
			$obj->name = $name;
			$obj->feed = $f;
			$feeds [] = $obj;
			break;
		}
	}
}

echo '<?xml version="1.0" encoding="ISO-8859-1"?>';

?>
<opml version="1.0">
	<head>
	</head>
	<body>

	<?php foreach ($feeds as $f) { ?>
	<outline text="<?php echo $f->name; ?>" type="rss" xmlUrl="<?php echo $f->feed; ?>" />
	<?php } ?>

	</body>
</opml>
