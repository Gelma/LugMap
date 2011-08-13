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

$file = new DOMDocument ();
$file->Load ($argv [1]);
$xpath = new DOMXPath ($file);
$feeds = $xpath->query ('//body/outline');

$output =<<<PLANET
[Planet]
name = Planet LugMap
link = http://planet.lugmap.it/
owner_name = Manovali della LugMap
owner_email = lugmap@lists.linux.it

cache_directory = tmp/planet_cache
new_feed_items = 2
log_level = INFO
feed_timeout = 20

template_files = /var/www/lugmap/LugMap/forge/opml-to-planet/index.html.tmpl /var/www/lugmap/LugMap/forge/opml-to-planet/atom.xml.tmpl /var/www/lugmap/LugMap/forge/opml-to-planet/rss20.xml.tmpl /var/www/lugmap/LugMap/forge/opml-to-planet/rss10.xml.tmpl /var/www/lugmap/LugMap/forge/opml-to-planet/opml.xml.tmpl /var/www/lugmap/LugMap/forge/opml-to-planet/foafroll.xml.tmpl

output_dir = /var/www/lugmap/LugMap/forge/opml-to-planet/
items_per_page = 60
days_per_page = 0
date_format = %B %d, %Y %I:%M %p
new_date_format = %B %d, %Y
encoding = utf-8

[/var/www/lugmap/LugMap/forge/opml-to-planet/index.html.tmpl]
days_per_page = 7
activity_threshold = 0

[DEFAULT]
facewidth = 65
faceheight = 85

PLANET;

foreach ($feeds as $feed) {
	$lug = $feed->getAttribute ('text');
	$url = $feed->getAttribute ('xmlUrl');

	$output .=<<<PLANET

[$url]
name = $lug

PLANET;
}

echo $output . "\n";

?>
