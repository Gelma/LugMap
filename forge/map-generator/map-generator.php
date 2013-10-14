<?php

require_once ('../../varie.php');

init_geocache ();
global $geocache;

$output = new stdClass ();
$output->type = "FeatureCollection";
$output->features = array ();

foreach ($elenco_regioni as $region => $name) {
	/*
		I gruppi di carattere nazionale non possono essere messi sulla
		cartina (a meno di piazzare un grosso marker di traverso su
		tutta la nazione, ma non mi sembra il caso...), dunque li salto
	*/
	if ($name == "Italia")
		continue;

        $lugs = file ('http://github.com/Gelma/LugMap/raw/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$cities = file ('liste_comuni/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$found_cities = array ();

        foreach ($lugs as $lug) {
		$result = null;
		$doshift = false;

		$attr = explode ('|', $lug);
		$mail = '';
		$osmnode = '';

		switch (count ($attr)) {
			case 6:
				$osmnode = $attr [5];

				/*
					Qui il break manca di proposito
				*/

			case 5:
				$mail = $attr [4];

				/*
					Qui il break manca di proposito
				*/

			case 4:
			default:
				$site = $attr [3];
				$zone = $attr [2];
				$name = $attr [1];
				$prov = $attr [0];
				break;
		}

		if ($osmnode != '') {
			$result = ask_openstreetmap ($osmnode);
		}

		if ($result == null) {
			$doshift = true;

			foreach ($cities as $city) {
				if (stristr ($zone, $city) != false) {
					$c = str_replace (' ', '%20', $city) . ',' . str_replace (' ', '%20', $prov);

					$result = ask_coordinates ($c);
					if ($result != null)
						break;

					$c = str_replace (' ', '%20', $city);

					$result = ask_coordinates ($c);
					if ($result != null)
						break;
				}
			}
		}

		if ($result != null) {
			list ($lat, $lon) = $result;

			if ($doshift == true) {
				$lon = shift_city ($city, $lon, $found_cities);
				$found_cities [] = $city;
			}

			$point = new stdClass ();
			$point->type = "Feature";
			$point->properties = new stdClass ();
			$point->properties->name = $name;
			$point->properties->website = $site;
			$point->geometry = new stdClass ();
			$point->geometry->type = "Point";
			$point->geometry->coordinates = array ($lon, $lat);

			array_push ($output->features, $point);
		}
		else {
			echo "Impossibile gestire la zona '$zone', si consiglia l'analisi manuale\n";
		}
	}
}

write_geo_file ('dati.txt', json_encode ($output));
save_geocache ();

?>
