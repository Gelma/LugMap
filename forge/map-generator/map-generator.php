<?php

function latlon_magic ($lat, $lon) {
	/*
		Formule per la conversione delle coordinate brutalmente scopiazzate da linuxday.it
	*/
	$lat = (log (tan ((90 + $lat) * pi () / 360)) / (pi () / 180)) * 20037508.34 / 180;
	$lon = $lon * 20037508.34 / 180;
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
		qui cerchiamo il riferimento esplicito a "city" o "town" (credo che li usi
		a seconda delle dimensioni del centro abitato) e se non si trova nulla
		passera' all'interrogazione di GeoNames. Attenzione: non usare i nodi di tipo
		"administrative", sono veramente troppo poco precisi
	*/
	$results = $xpath->query ("/searchresults/place[@type='city']", $doc);
	if ($results->length < 1) {
		$results = $xpath->query ("/searchresults/place[@type='town']", $doc);
		if ($results->length < 1) {
			$results = $xpath->query ("/searchresults/place[@type='hamlet']", $doc);
			if ($results->length < 1)
				return null;
		}
	}

	$node = $results->item (0);
	$lat = $node->getAttribute ('lat');
	$lon = $node->getAttribute ('lon');

	return latlon_magic ($lat, $lon);
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

	return latlon_magic ($lat, $lon);
}

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
);

/*
	Scopiazzato da http://www.phpbuilder.com/board/showthread.php?t=10287962
*/
function howMany ($needle, $haystack) {
	$exists = array_search ($needle, $haystack);
	if ($exists !== FALSE)
		return 1 + howMany ($needle, array_slice ($haystack, ($exists + 1)));

	return 0;
}

/*
	Per dettagli sul formato del file accettato da OpenLayer.Layer.Text
	http://dev.openlayers.org/apidocs/files/OpenLayers/Layer/Text-js.html
*/
$rows = array ("lat\tlon\ttitle\tdescription\ticonSize\ticonOffset\ticon");

foreach ($elenco_regioni as $region => $name) {
        $lugs = file ('http://github.com/Gelma/LugMap/raw/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$cities = file ('liste_comuni/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$found_cities = array ();

        foreach ($lugs as $lug) {
		$found = false;
		list ($prov, $name, $zone, $site) = explode ('|', $lug);

		foreach ($cities as $city) {
			if (stristr ($zone, $city) != false) {
				/*
					Questo e' per evitare i limiti imposti dal server OpenStreetMap
					http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy
					Non dubito che GeoNames abbia qualcosa di analogo
				*/
				sleep (1);

				$c = str_replace (' ', '%20', $city);

				$result = ask_nominatim ($c);
				if ($result == null) {
					$result = ask_geonames ($c);
					if ($result == null)
						continue;
				}

				list ($lat, $lon) = $result;

				/*
					Questo e' per evitare che due punti si sovrappongano, quelli che vengono
					trovati nella stessa citta' (e dunque alle stesse coordinate) vengono
					arbitrariamente shiftati
				*/
				$occurrences = howMany ($city, $found_cities);
				if ($occurrences != 0)
					$lon = $lon + (3000 * $occurrences);

				$found_cities [] = $city;

				$rows [] = "$lat\t$lon\t$name\t<a href=\"$site\">$site</a>\t16,19\t-8,-19\thttp://lugmap.it/images/icon.png";
				$found = true;
				break;
			}
		}

		if ($found == false)
			echo "Impossibile gestire la zona '$zone', si consiglia l'analisi manuale\n";
	}
}

/*
	Attenzione: e' necessario mettere un newline anche al fondo dell'ultima
	riga del file, la quale viene altrimenti ignorata da OpenLayer
*/
if (file_put_contents ('dati.txt', join ("\n", $rows) . "\n") === false)
	echo "Errore nel salvataggio del file\n";
else
	echo "I dati sono stati scritti nel file 'dati.txt'\n";

?>
