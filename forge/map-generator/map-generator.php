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

				$results = $xpath->query ("/searchresults/place[@type='administrative']", $doc);
				if ($results->length < 1)
					continue;

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
				if (in_array ($city, $found_cities) == true) {
					$lat = $lat + rand (-20000, 20000);
					$lon = $lon + rand (-20000, 20000);
				}
				else {
					$found_cities [] = $city;
				}

				$rows [] = "$lat\t$lon\t$name\t<a href=\"$site\">$site</a>\t16,19\t-8,-19\thttp://lugmap.it/forge/map-generator/icon.png";
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
