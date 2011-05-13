<?php

// Richiede SimplePie!
// http://simplepie.org/
include_once ('/usr/share/php/simplepie/simplepie.inc');

if ($argc < 2 || file_exists ($argv [1]) == false) {
	echo "Usage: " . $argv [0] . " <file>\n";
	echo "\t<file>\tOPML contenente i feeds da parsare\n";
	echo "\n";
	exit ();
}

if (file_exists ('notices.db') == false) {
	$db = sqlite_open ('notices.db');

	$query = 'CREATE TABLE items (id INTEGER PRIMARY KEY, lug TEXT, parent TEXT, link TEST, title TEXT, description TEXT, date INTEGER)';
	sqlite_query ($db, $query);
}
else {
	$db = sqlite_open ('notices.db');
}

$file = new DOMDocument ();
$file->Load ($argv [1]);
$xpath = new DOMXPath ($file);
$feeds = $xpath->query ('//body/outline');

foreach ($feeds as $feed) {
	$lug = $feed->getAttribute ('text');
	$url = $feed->getAttribute ('xmlUrl');

	$parser = new SimplePie ();
        $parser->set_feed_url ($url);
        $parser->init ();
	$parser->handle_content_type ();
	if ($parser->error ())
		continue;

	$items = $parser->get_items ();
	$parent = sqlite_escape_string ($parser->subscribe_url ());
	$link = sqlite_escape_string ($parser->get_permalink ());

	foreach ($items as $item) {
		$query = sprintf ("SELECT id FROM items WHERE parent = '%s' AND link = '%s'", $parent, $link);
		$existing = sqlite_query ($db, $query);

		if (sqlite_num_rows ($existing) == 0) {
			/*
				Il fatto di ripetere per ogni item il nome del LUG e l'indirizzo
				del feed e' immensamente ridondante, ma permette di avere una
				struttura iper-semplificata del DB che possa sopportare ogni
				genere di contenuto con poco o nessun intervento umano
			*/
			$query = sprintf ("INSERT INTO items (lug, link, title, description, date) VALUES ('%s', '%s', '%s', '%s', %d)",
						$lug,
						sqlite_escape_string ($item->get_link ()),
						sqlite_escape_string ($item->get_title ()),
						sqlite_escape_string ($item->get_description ()),
						/*
							Qui si dovrebbe mettere la data dell'item, cosi' come appare nel feed,
							ma si preferisce mettere la data corrente (ovvero la data di recupero
							della notizia) sia per comodita' e semplificazione delle successive
							operazioni che verranno effettuate sul database sia perche' spesso le
							date nei feed non sono in formato standard (ISO 8601) e diventa
							complicato ricondurla ad una data programmaticamente gestibile
						*/
						time ());
			sqlite_query ($db, $query);
		}
	}
}

sqlite_close ($db);

?>
