<?php

# l'array utilizza come chiave la richiesta in input
# (utilizzata anche per identificare il file da leggere)
# e come valore la stringa da visualizzare
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

# URL del sito di riferimento
$main_url = 'http://lugmap.it';

/***************************************************************************************************************/

function do_head ($title = null, $javascript = array ()) {
global $main_url;

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Mappa dei Linux Users Groups Italiani<?php if ($title != null) echo ": $title"; ?></title>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<?php foreach ($javascript as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>

		<link rel="stylesheet" href="assets/css/main.css" />
	</head>

	<body>
		<?php

		/*
			I titoli delle pagine sono un tantino piu' SEO delle
			voci di menu che le rappresentano, mi tocca fare una
			conversione un po' articolata...
		*/
		switch ($title) {
			case 'Homepage':
				$select = 0;
				break;

			case 'Lista completa dei LUG':
				$select = 2;
				break;

			case 'Partecipa alla LugMap':
				$select = 3;
				break;

			case 'Progetti Collaterali':
				$select = 4;
				break;

			case 'La LugMap sul tuo Sito':
				$select = 5;
				break;

			case 'Notizie dai LUG Italiani':
			case 'Informazioni su Planet LugMap':
				$select = 7;
				break;

			case 'Eventi Linux in Italia':
			case 'Informazioni su Calendar LugMap':
				$select = 8;
				break;

			default:
				$select = 0;
				break;
		}

		if (strncmp ($title, 'Tutti i LUG', strlen ('Tutti i LUG')) == 0) {
			$select = 6;

			/*
				http://www.webmasterworld.com/php/3683825.htm
			*/
			$pattern = '/[^ ]*$/';
			preg_match ($pattern, $title, $results);
			$menu = $results [0];
		}

		?>

		<ul class="verticalslider_tabs">
			<li><a<?php if ($select == 0) echo ' class="select"' ?> href="<?php echo $main_url ?>/index.php">Mappa</a></li>

			<li><a<?php if ($select == 2) echo ' class="select"' ?> href="<?php echo $main_url ?>/lista.php">Lista Completa</a></li>

			<?php if ($select == 6): ?>
			<li class="verticalslider_subtabs"><a class="select" href="http://<?php echo $_SERVER['HTTP_HOST'] ?>"><?php echo $menu ?></a>
			<?php endif; ?>

			<li><a<?php if ($select == 3) echo ' class="select"' ?> href="<?php echo $main_url ?>/partecipa.php">Partecipa</a></li>

			<li><a<?php if ($select == 4) echo ' class="select"' ?> href="<?php echo $main_url ?>/forge.php">Progetti Collaterali</a></li>
			<li class="verticalslider_subtabs"><a<?php if ($select == 8) echo ' class="select"' ?> href="http://calendar.lugmap.it/">Calendario Eventi</a>
			<li class="verticalslider_subtabs"><a<?php if ($select == 5) echo ' class="select"' ?> href="<?php echo $main_url ?>/widget.php">Widget Web</a>
			<li class="verticalslider_subtabs"><a<?php if ($select == 7) echo ' class="select"' ?> href="http://planet.lugmap.it/">Planet LugMap</a>

			<li>
				<p class="intro">
					<img src="images/logo.png" alt="lugmap.it" />
				</p>
			</li>
		</ul>

		<div class="verticalslider_contents">

	<?php
}

/***************************************************************************************************************/

function do_foot () {
?>

		</div>

		<p style="clear: both;"></p>

		<!-- Piwik -->
		<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ?
				"https://pergamena.lugbs.linux.it/" :
				"http://pergamena.lugbs.linux.it/");
			document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try {
				var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 3);
				piwikTracker.trackPageView();
				piwikTracker.enableLinkTracking();
			} catch( err ) {}
		</script>
		<noscript>
			<p>
				<img src="http://pergamena.lugbs.linux.it/piwik.php?idsite=3" style="border:0" alt="" />
			</p>
		</noscript>
		<!-- End Piwik Tracking Code -->
	</body>
</html>

<?php
}

?>
