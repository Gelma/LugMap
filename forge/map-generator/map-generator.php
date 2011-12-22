<?php

require_once ('../../varie.php');

init_geocache ();
global $geocache;

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
				$c = str_replace (' ', '%20', $city);

				$result = ask_coordinates ($c);
				if ($result == null)
					continue;

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

save_geocache ();

?>
