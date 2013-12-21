<?php

/*
  Codice della mappa dei LUG italiani
  Copyright (C) 2010-2014  Italian Linux Society - http://www.linux.it/

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once ('../funzioni.php');

function notify_mail ($message) {
	mail ('webmaster@linux.it', 'notifica script mappa LugMap', $message . "\n", 'From: linux.it <webmaster@linux.it>' . "\r\n");
}

/*
	Scopiazzato da http://www.phpbuilder.com/board/showthread.php?t=10287962
*/
function howMany ($needle, $haystack) {
	$exists = array_search ($needle, $haystack);
	if ($exists !== FALSE)
		return 1 + howMany ($needle, array_slice ($haystack, ($exists + 1)));

	return 0;
}

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

function init_geocache () {
	global $has_geocache;
	global $geocache;

	$has_geocache = file_exists ('../data/geocache.txt');

	if ($has_geocache == true)
		$geocache = file ('../data/geocache.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
	$osm = @file_get_contents ('http://www.openstreetmap.org/api/0.6/node/' . $node);
	if ($osm === false)
		return null;

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
	file_put_contents ('../data/geocache.txt', join ("\n", $geocache));
}

function write_geo_file ($name, $contents) {
	if (file_put_contents ($name, $contents) === false)
		notify_mail ("Errore nel salvataggio del file geografico per la LugMap");
}

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

	/*
		I nomi col trattino nel nome sono solo "alias" di altre regioni,
		dunque li salto
	*/
	if (strpos ($region, '-') !== false)
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
			notify_mail ("Impossibile gestire la zona '$zone', si consiglia l'analisi manuale");
		}
	}
}

write_geo_file ('../data/geo.txt', json_encode ($output));
save_geocache ();

?>
