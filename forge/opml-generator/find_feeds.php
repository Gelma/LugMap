<?php

// Richiede SimplePie!
// http://simplepie.org/
include_once ('/usr/share/php/simplepie/simplepie.inc');

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

$exceptions = array ();

$exceptions_file = file ('eccezioni.txt');
if ($exceptions_file != false) {
	foreach ($exceptions_file as $ex) {
		if ($ex [0] == '#')
			continue;
		$exceptions [] = $ex;
	}
}

$feeds = array ();

foreach ($elenco_regioni as $region => $name) {
	$lugs = file ('http://github.com/Gelma/LugMap/raw/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);

	foreach ($lugs as $lug) {
		list ($prov, $name, $zone, $site) = explode ('|', $lug);

		$parser = new SimplePie ();
		$parser->set_feed_url ($site);
		$parser->init ();
		$parser->handle_content_type ();
		if ($parser->error ())
			continue;

		$f = $parser->subscribe_url ();

		$skip = false;

		foreach ($exceptions as $exception) {
			if ($f == $exception) {
				$skip = true;
				break;
			}
		}

		if ($skip == true)
			continue;

		$obj = new stdClass ();
		$obj->name = $name;
		$obj->feed = $f;
		$feeds [] = $obj;
	}
}

echo '<?xml version="1.0" encoding="ISO-8859-1"?>';

?>
<opml version="1.0">
	<head>
	</head>
	<body>

	<?php foreach ($feeds as $f) { ?>
	<outline text="<?php echo $f->name; ?>" type="rss" xmlUrl="<?php echo $f->feed; ?>" />
	<?php } ?>

	</body>
</opml>
