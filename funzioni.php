<?php

# l'array utilizza come chiave la richiesta in input
# (utilizzata anche per identificare il file da leggere)
# e come valore la stringa da visualizzare
$elenco_regioni = array (
  "abruzzo"               => "Abruzzo",
  "basilicata"            => "Basilicata",
  "calabria"              => "Calabria",
  "campania"              => "Campania",
  "emilia"                => "Emilia Romagna",
  "emilia-romagna"        => "Emilia Romagna",
  "friuli"                => "Friuli Venezia Giulia",
  "friuli-venezia-giulia" => "Friuli Venezia Giulia",
  "lazio"                 => "Lazio",
  "liguria"               => "Liguria",
  "lombardia"             => "Lombardia",
  "marche"                => "Marche",
  "molise"                => "Molise",
  "piemonte"              => "Piemonte",
  "puglia"                => "Puglia",
  "sardegna"              => "Sardegna",
  "sicilia"               => "Sicilia",
  "toscana"               => "Toscana",
  "trentino"              => "Trentino Alto Adige",
  "trentino-alto-adige"   => "Trentino Alto Adige",
  "umbria"                => "Umbria",
  "valle"                 => "Valle d'Aosta",
  "valle-daosta"          => "Valle d'Aosta",
  "veneto"                => "Veneto",
  "Italia"                => "Italia"
);

function lugheader ($title, $extracss = null, $extrajs = null) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="italian" />
  <meta name="robots" content="noarchive" />
  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans|Nobile|Nobile:b" />
  <link href="/css/main.css" rel="stylesheet" type="text/css" />

  <?php
    if ($extracss != null)
      foreach ($extracss as $e) {
        ?>
        <link href="<?php echo $e; ?>" rel="stylesheet" type="text/css" />
        <?php
      }

    if ($extrajs != null)
      foreach ($extrajs as $e) {
        ?>
        <script type="text/javascript" src="<?php echo $e; ?>"></script>
        <?php
      }
  ?>

  <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
    {lang: 'it'}
  </script>

  <title><?php echo $title; ?></title>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-3626063-13']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<!-- <a href="http://lugmap.linux.it/css/contact.php">select</a> -->
<body>

<div id="header">
  <img src="immagini/logo.png" width="66" height="79" alt="" />
	<div id="maintitle">LugMap</div>
	<div id="payoff">La mappa dei Linux Users Group italiani</div>

	<div class="menu">
		<a class="generalink" href="http://lugmap.linux.it/">LugMap</a> |
		<a class="generalink" href="http://lugmap.linux.it/partecipa/">Partecipa</a> |
		<a class="generalink" href="http://lugmap.linux.it/propaganda/">Propaganda</a> |
		<a class="generalink" href="http://lugmap.linux.it/guida/">Guida</a> |
		<a class="generalink" href="http://lugmap.linux.it/contatti/">Contatti</a>
	</div>
</div>

<?php
}

function lugfooter () {
?>
<div id="footer">
</div>
<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://pergamena.lugbs.linux.it/" : "http://pergamena.lugbs.linux.it/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://pergamena.lugbs.linux.it/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
</body>
</html>

<?php
}

function ultimo_aggiornamento () {
?>
   <a id="csvLink" href="http://github.com/Gelma/LugMap/commits/lugmap.linux.it">&raquo; Aggiornato al <?php print file_get_contents('../.ultimo_commit') ?></a>
   <a id="csvLink" href="mailto:andrea.gelmini@lugbs.linux.it,bob4job@gmail.com?subject=LugMap: segnalazione aggiornamento/errore/refuso">&raquo; Segnala</a>

<?php
}

?>
