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
	"Italia"     => "Italia"
);

$feeds = array ();

foreach ($elenco_regioni as $region => $name) {
	$lugs = file ('http://github.com/Gelma/LugMap/raw/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);

	foreach ($lugs as $lug) {
		list ($prov, $name, $zone, $site) = explode ('|', $lug);
		$contents = file_get_contents ($site);
		if ($contents == false)
			continue;

		$doc = new DOMDocument ();
		$doc->recover = true;
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML ($contents);

		$links = $doc->getElementsByTagName ('link');
		$feed = null;

		for ($i = 0; $i < $links->length; $i++) {
			$node = $links->item ($i);

			if ($node->hasAttribute ('rel') && $node->getAttribute ('rel') == 'alternate' && $node->hasAttribute ('href')) {
				$feed = $node->getAttribute ('href');
				if ($feed [0] == '/')
					$feed = $site . $feed;

				break;
			}
		}

		if ($feed != null) {
			$obj = new stdClass ();
			$obj->name = $name;
			$obj->feed = $feed;
			$feeds [] = $obj;
		}
	}
}

?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<opml version="1.0">
	<head>
	</head>
	<body>

	<?php foreach ($feeds as $f) { ?>
	<outline text="<?php echo $f->name; ?>" type="rss" xmlUrl="<?php echo $f->feed; ?>" />
	<?php } ?>

	</body>
</opml>
