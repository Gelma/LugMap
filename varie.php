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

function lugheader ($title, $extracss = null, $extrajs = null) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="italian" />

  <link href="/assets/css/main.css" rel="stylesheet" type="text/css" />

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

  <title><?php echo $title; ?></title>

  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-190627-10']);
    _gaq.push(['_setDomainName', '.lugmap.it']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
</head>
<body>

<div id="header">
  <h2 id="title"><?php echo $title; ?></h2>
</div>

<?php
}

function lugfooter () {
?>

<div id="footer">
  <table width="70%" align="center">
	<tr><td>
		<p class="helpMessage">Aiutaci a mantenere la LugMap aggiornata!</p>
		<p class="helpMessage">
		Segnalaci nuovi gruppi, cos&igrave; come errori ed omissioni, scrivendo
		alla <a class="generalink" href="http://lists.linux.it/listinfo/lugmap">mailing list pubblica</a>,
		oppure contattando direttamente Andrea Gelmini (telefonicamente al
		<a class="generalink" href="tel:328-72-96-628">328-72-96-628</a>, via
		<a class="generalink" href="mailto:andrea.gelmini@lugbs.linux.it">mail</a>, o attraverso
		<a class="generalink" href="http://www.facebook.com/andrea.gelmini">Facebook</a>.)<br>
		Puoi partecipare direttamente, sia alla stesura del codice che del database, sfruttando il
		<a class="generalink" href="http://github.com/Gelma/LugMap">repository GitHub</a>.
		Per saperne di pi&ugrave; &egrave; disponibile la
		<a class="generalink" href="http://github.com/Gelma/LugMap/raw/docs/Guida_Intergalattica_Alla_LugMap.pdf">
		Guida Intergalattica alla LugMap</a>.
		<p class="helpMessage">
		Te ne saremo eternamente grati!
	  </td></tr>
  </table>
</div>

</body>
</html>

<?php
}

?>
