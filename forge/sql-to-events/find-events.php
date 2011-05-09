<?php

/*
	Path del database popolato da feeds-aggregator.php
*/
$dbpath = '../opml-to-sql/notices.db';

/*
	Per comodita' metto tutte le parole in un'unica stringa facilmente e
	rapidamente editabile, sotto provvedo poi a tokenizzarla in modo automatico
*/
$words = 'corso, corsi, dibattito, conferenza, conferenze, incontro, seminario, workshop, sessione, lip, installation party, install party,
		lunedi, martedi, mercoledi, giovedi, venerdi, sabato, domenica';

$tmp = explode (',', $words);
$words = array ();

foreach ($tmp as $word)
	$words [] = trim ($word);

$db = sqlite_open ($dbpath);

$query = 'SELECT * FROM items ORDER BY id DESC LIMIT 10';
$existing = sqlite_query ($db, $query);

$managed = array ();

while ($row = sqlite_fetch_array ($existing)) {
	foreach ($words as $word) {
		if (stristr ($row ['description'], $word) != false) {
			$managed [] = $row;
			break;
		}
	}
}

$rss = <<<RSS
<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">
<channel>
	<title>Eventi dai LUG da verificare</title>
</channel>

<items>
RSS;

foreach ($managed as $item)
	$rss .= '<item><title>' . stripslashes ($item ['title']) . '</title><link>' . stripslashes ($item ['link']) . '</link><description>' . stripslashes ($item ['description']) . '</description></item>' . "\n";

$rss .= <<<RSS
</items>
</rdf:RDF>

RSS;

echo $rss;

?>
