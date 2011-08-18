<?php

include_once ('/usr/share/php/simplepie/simplepie.inc');

$lugs = '../../lugs.opml';
$external = 'external.opml';
$output = 'news_linuxday.xml';

function manage_group ($filepath) {
	$goods = array ();

	$file = new DOMDocument ();
	$file->Load ($filepath);
	$xpath = new DOMXPath ($file);
	$feeds = $xpath->query ('//body/outline');

	foreach ($feeds as $feed) {
		$url = $feed->getAttribute ('xmlUrl');

		$parser = new SimplePie ();
		$parser->set_feed_url ($url);
		$parser->init ();
		$parser->handle_content_type();

		foreach ($parser->get_items () as $item) {
			$ok = false;
			$t = $item->get_title ();

			if (preg_match ('/linux *day/i', $t, &$keys)) {
				$ok = true;
			}
			else {
				$d = $item->get_description ();
				if (preg_match ('/linux *day/i', $d, &$keys))
					$ok = true;
			}

			if ($ok == true) {
				echo "Trovata una news! - " . $item->get_title () . "\n";
				$goods [] = $item;
			}
		}
	}

	return $goods;
}

function compare_dates ($first, $second) {
	$f = $first->get_date ('U');
	$s = $second->get_date ('U');

	if ($f > $s)
		return -1;
	else
		return 1;
}

echo "============= Inizio\n";
$final = array ();

echo "============= Ispeziono i LUG\n";
$final = array_merge ($final, manage_group ($lugs));
echo "============= Ispeziono i siti esterni\n";
$final = array_merge ($final, manage_group ($external));

echo "============= Trovate " . count ($final) . " news!\n";
usort ($final, "compare_dates");

$rssfeed =<<<RSS
<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
<channel>
	<title>Linux Day News</title>
	<link>http://www.linuxday.it/</link>
	<description>News dal web sul Linux Day</description>
	<language>it</language>

RSS;

for ($i = 0; $i < 20 && $i < count ($final); $i++ ) {
	$t = $final [$i];

	$rssfeed .= "	<item>\n";

	$rssfeed .= "		<title><![CDATA[" . $t->get_title () . "]]></title>\n";
	$rssfeed .= "		<link>" . $t->get_permalink () . "</link>\n";
	$rssfeed .= "		<pubDate>" . $t->get_date ('D, d M Y H:i:s O') . "</pubDate>\n";
	$rssfeed .= "		<description><![CDATA[" . $t->get_description () . "]]></description>\n";

	$rssfeed .= "	</item>\n";
}

$rssfeed .= "</channel>\n</rss>\n";

file_put_contents ($output, $rssfeed);

?>
