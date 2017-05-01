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

		if (preg_match ('/<META HTTP-EQUIV="Refresh" CONTENT="(.*); ?URL=(.[^"]*)">/i', $open, $keys)) {
			if (strncmp ('http://', $keys [2], 7) == 0 || strncmp ('https://', $keys [2], 8) == 0)
				$url = $keys [2];
			else
				$url = $url . $keys [2];
		}
	}

	return $url;
}

/**
 * Download a web page using curl. Some pages return 403 forbidden using loadHTMLFile,
 * whereas they are loaded with no trouble using cURL (probably the matter is related 
 * with HTTP headers in the post request.
 * 
 * @return the page as string, FALSE if some error occurred
 * @author Cristiano Longo
 */
function downloadPage($url){
	$s = curl_init();
	
	curl_setopt($s,CURLOPT_URL,$url);
	curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
	$page=curl_exec($s);
	curl_close($s);	
	return $page;
}

libxml_use_internal_errors(true);

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
	$lugs = file ('https://raw.github.com/Gelma/LugMap/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);

	foreach ($lugs as $lug) {
		list ($prov, $name, $zone, $site) = explode ('|', $lug);
		$site = check_url ($site);
		$page = downloadPage($site);
		if (!$page)
			continue;
		
		$doc = new DOMDocument();
		$doc->loadHTML($page);
		
		$xpath = new DOMXpath($doc);
		if ($xpath == null)
			continue;

		$discovered = $xpath->query("//link[@rel='alternate']");
		foreach($discovered as $f) {
			$type = $f->getAttribute('type');
			if ($type == null || (strpos($type, 'rss') === false && strpos($type, 'atom') === false))
				continue;

			$url = $f->getAttribute('href');

			if (strncmp($url, 'http', 4) != 0) {
				if (substr($url, 0, 1) == '/') {
					$parts = parse_url($site);
					$url = sprintf('%s://%s%s', $parts['scheme'], $parts['host'], $url);
				}
			}

			$skip = false;
			$url = str_replace ('&', '&amp;', str_replace ('&amp;', '&', $url));

			foreach ($exceptions as $exception) {
				if ($url == $exception) {
					$skip = true;
					break;
				}
			}

			if ($skip == true)
				continue;

			$obj = new stdClass ();
			$obj->name = $name;
			$obj->feed = $url;
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
