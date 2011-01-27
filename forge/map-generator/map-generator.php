<?php

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
				*/
				sleep (1);

				/*
					Specifico nella query "Italia", per evitare collisioni con comuni omonimi nel resto del mondo
				*/
				$c = str_replace (' ', '%20', $city);
				$location = file_get_contents ('http://nominatim.openstreetmap.org/search?format=xml&q=' . $c . ',Italia');

				$doc = new DOMDocument ();
				if ($doc->loadXML ($location, LIBXML_NOWARNING) == false)
					continue;

				$xpath = new DOMXPath ($doc);

				/*
					I risultati restituiti da Nominatim sono molteplici, e non sempre coerenti,
					qui cerchiamo il riferimento esplicito alla citta' e alla peggio ci
					accontentiamo di un riferimento ai confini amministrativi (non precisi, ma
					meglio di niente)
				*/
				$results = $xpath->query ("/searchresults/place[@type='city']", $doc);
				if ($results->length < 1) {
					$results = $xpath->query ("/searchresults/place[@type='administrative']", $doc);
					if ($results->length < 1)
						continue;
				}

				/*
					Formule per la conversione delle coordinate brutalmente scopiazzate da linuxday.it
				*/
				$node = $results->item (0);
				$lat = $node->getAttribute ('lat');
				$lat = (log (tan ((90 + $lat) * pi () / 360)) / (pi () / 180)) * 20037508.34 / 180;
				$lon = $node->getAttribute ('lon');
				$lon = $lon * 20037508.34 / 180;

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

				unset ($node);
				break;
			}
		}

		if ($found == false)
			echo "Impossibile gestire la zona '$zone', si consiglia l'analisi manuale\n";
	}
}

if (file_put_contents ('dati.txt', join ("\n", $rows)) === false)
	echo "Errore nel salvataggio del file\n";
else
	echo "I dati sono stati scritti nel file 'dati.txt'\n";

?>
