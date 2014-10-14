<?php

# l'array utilizza come chiave la richiesta in input
# (utilizzata anche per identificare il file da leggere)
# e come valore la stringa da visualizzare
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

# URL del sito di riferimento
$main_url = 'http://lugmap.it';

/***************************************************************************************************************/

function do_head ($title = null, $javascript = array (), $stylesheet = array ()) {
global $main_url;

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Mappa dei Linux Users Groups Italiani<?php if ($title != null) echo ": $title"; ?></title>

		<?php foreach ($stylesheet as $css): ?>
		<link rel="stylesheet" href="<?php echo $css; ?>" />
		<?php endforeach; ?>
		<link rel="stylesheet" href="assets/css/main.css" />

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<?php foreach ($javascript as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
	</head>

	<body>
		<?php

		/*
			I titoli delle pagine sono un tantino piu' SEO delle
			voci di menu che le rappresentano, mi tocca fare una
			conversione un po' articolata...
		*/
		switch ($title) {
			case 'Homepage':
				$select = 0;
				break;

			case 'Lista completa dei LUG':
				$select = 2;
				break;

			case 'Partecipa alla LugMap':
				$select = 3;
				break;

			case 'Progetti Collaterali':
				$select = 4;
				break;

			case 'La LugMap sul tuo Sito':
				$select = 5;
				break;

			default:
				$select = 0;
				break;
		}

		if (strncmp ($title, 'Tutti i LUG', strlen ('Tutti i LUG')) == 0) {
			$select = 6;

			/*
				http://www.webmasterworld.com/php/3683825.htm
			*/
			$pattern = '/[^ ]*$/';
			preg_match ($pattern, $title, $results);
			$menu = $results [0];
		}

		?>
<div class="divMenu">
		<ul class="verticalslider_tabs">
			<li><a<?php if ($select == 0) echo ' class="select"' ?> href="<?php echo $main_url ?>/index.php">Mappa</a></li>

			<li><a<?php if ($select == 2) echo ' class="select"' ?> href="<?php echo $main_url ?>/lista.php">Lista Completa</a></li>

			<?php if ($select == 6): ?>
			<li class="verticalslider_subtabs"><a class="select" href="http://<?php echo $_SERVER['HTTP_HOST'] ?>"><?php echo $menu ?></a>
			<?php endif; ?>

			<li><a<?php if ($select == 3) echo ' class="select"' ?> href="<?php echo $main_url ?>/partecipa.php">Partecipa</a></li>

			<li><a<?php if ($select == 4) echo ' class="select"' ?> href="<?php echo $main_url ?>/forge.php">Progetti Collaterali</a></li>
			<li class="verticalslider_subtabs"><a<?php if ($select == 5) echo ' class="select"' ?> href="<?php echo $main_url ?>/widget.php">Widget Web</a>
		</ul>
		<div class="divLogo">
			<img src="images/logo.png" alt="lugmap.it" class="introLogo"/>
		</div>
</div>
			<div class="verticalslider_contents">
	<?php
}

/***************************************************************************************************************/

function do_foot () {
?>

		</div>

		<p style="clear: both;"></p>

		<!-- Piwik -->
		<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ?
				"https://pergamena.lugbs.linux.it/" :
				"http://pergamena.lugbs.linux.it/");
			document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try {
				var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 3);
				piwikTracker.trackPageView();
				piwikTracker.enableLinkTracking();
			} catch( err ) {}
		</script>
		<noscript>
			<p>
				<img src="http://pergamena.lugbs.linux.it/piwik.php?idsite=3" style="border:0" alt="" />
			</p>
		</noscript>
		<!-- End Piwik Tracking Code -->
	</body>
</html>

<?php
}

/***************************************************************************************************************/

/*
	Scopiazzato da http://www.phpbuilder.com/board/showthread.php?t=10287962
*/
function howMany ($needle, $haystack) {
	$exists = array_search ($needle, $haystack);
	if ($exists !== FALSE)
		return 1 + howMany ($needle, array_slice ($haystack, ($exists + 1)));

	return 0;
}

/***************************************************************************************************************/

function shift_city ($city, $lon, $found_cities) {
	/*
		Questo e' per evitare che due punti si sovrappongano, quelli che vengono
		trovati nella stessa citta' (e dunque alle stesse coordinate) vengono
		arbitrariamente shiftati
	*/
	$occurrences = howMany ($city, $found_cities);
	if ($occurrences != 0)
		$lon = $lon + (3000 * $occurrences);

	return $lon;
}

function latlon_magic ($lat, $lon) {
	/*
		Formule per la conversione delle coordinate brutalmente scopiazzate da linuxday.it
	*/
	$lat = (log (tan ((90 + $lat) * pi () / 360)) / (pi () / 180)) * 20037508.34 / 180;
	$lon = $lon * 20037508.34 / 180;
	return array ($lat, $lon);
}

function init_geocache () {
	global $has_geocache;
	global $geocache;

	$has_geocache = file_exists ('../../geocache.txt');

	if ($has_geocache == true)
		$geocache = file ('../../geocache.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	else
		$geocache = array ();
}

function ask_geocache ($c) {
	global $has_geocache;
	global $geocache;

	if ($has_geocache == true) {
		foreach ($geocache as $row) {
			list ($city, $coords) = explode ('|', $row);

			if ($city == $c)
				return explode (',', $coords);
			else if (strcmp ($city, $c) > 0)
				break;
		}
	}

	return null;
}

function ask_openstreetmap ($node) {
	$osm = file_get_contents ('http://www.openstreetmap.org/api/0.6/node/' . $node);

	$doc = new DOMDocument ();
	if ($doc->loadXML ($osm, LIBXML_NOWARNING) == false)
		return null;

	$xpath = new DOMXPath ($doc);

	$results = $xpath->query ("/osm/node", $doc);
	if ($results->length <= 0)
		return null;

	$node = $results->item (0);
	$lat = $node->getAttribute ('lat');
	$lon = $node->getAttribute ('lon');
	return array ($lat, $lon);
}

function ask_nominatim ($c) {
	$location = file_get_contents ('http://nominatim.openstreetmap.org/search?format=xml&q=' . $c . ',Italia');

	$doc = new DOMDocument ();
	if ($doc->loadXML ($location, LIBXML_NOWARNING) == false)
		return null;

	$xpath = new DOMXPath ($doc);

	/*
		I risultati restituiti da Nominatim sono molteplici, e non sempre coerenti,
		qui cerchiamo il riferimento esplicito a diversi tipi (credo che li usi
		a seconda delle dimensioni del centro abitato) e se non si trova nulla
		passera' all'interrogazione di GeoNames. Attenzione: non usare i nodi di tipo
		"administrative", sono veramente troppo poco precisi
	*/

	$found = false;
	$accepted_nodes = array ('city', 'town', 'village', 'hamlet', 'suburb');

	foreach ($accepted_nodes as $accept) {
		$results = $xpath->query ("/searchresults/place[@type='$accept']", $doc);
		if ($results->length > 0) {
			$found = true;
			break;
		}
	}

	if ($found == false)
		return null;

	$node = $results->item (0);
	$lat = $node->getAttribute ('lat');
	$lon = $node->getAttribute ('lon');

	return array ($lat, $lon);
}

function ask_geonames ($c) {
	$location = file_get_contents ('http://api.geonames.org/search?username=madbob&q=' . $c . '&country=IT');

	$doc = new DOMDocument ();
	if ($doc->loadXML ($location, LIBXML_NOWARNING) == false)
		return null;

	$xpath = new DOMXPath ($doc);

	$results = $xpath->query ("/geonames/geoname/lat", $doc);
	if ($results->length < 1)
		return null;
	$lat = $results->item (0);
	$lat = $lat->nodeValue;

	$results = $xpath->query ("/geonames/geoname/lng", $doc);
	if ($results->length < 1)
		return null;
	$lon = $results->item (0);
	$lon = $lon->nodeValue;

	return array ($lat, $lon);
}

function ask_coordinates ($c) {
	global $geocache;

	$result = ask_geocache ($c);

	if ($result == null) {
		/*
			Questo e' per evitare i limiti imposti dal server OpenStreetMap
			http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy
			Non dubito che GeoNames abbia qualcosa di analogo
		*/
		sleep (1);

		$result = ask_geonames ($c);

		if ($result == null) {
			$result = ask_nominatim ($c);
			if ($result == null)
				return null;
		}

		list ($lat, $lon) = $result;
		$geocache [] = "$c|$lat,$lon";
		sort ($geocache);
	}

	return $result;
}

function save_geocache () {
	global $geocache;

	sort ($geocache);
	file_put_contents ('../../geocache.txt', join ("\n", $geocache));
}

function write_geo_file ($name, $contents) {
	if (file_put_contents ($name, $contents) === false)
		echo "Errore nel salvataggio del file\n";
	else
		echo "I dati sono stati scritti nel file '$name'\n";
}

?>
