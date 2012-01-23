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
		$found = false;
		list ($prov, $name, $zone, $site) = explode ('|', $lug);

		foreach ($cities as $city) {
			if (stristr ($zone, $city) != false) {
				$c = str_replace (' ', '%20', $city) . ',' . str_replace (' ', '%20', $prov);

				$result = ask_coordinates ($c);
				if ($result == null)
					continue;

				list ($lat, $lon) = $result;
				$lon = shift_city ($city, $lon, $found_cities);
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

write_geo_file ('dati.txt', $rows);
save_geocache ();

?>
