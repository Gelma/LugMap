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

function lugheader ($title, $keywords, $extracss = null, $extrajs = null) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="italian" />
  <meta name="keywords" content="Linux, GNU/Linux, software libero, freesoftware, LUG, Linux User Group, <?php echo $keywords; ?>" />

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
</head>
<body>

<div id="header">
  <img src="/ilsheader-wide.png" alt="Italian Linux Society" />
  <h2 id="title"><?php echo $title; ?></h2>
</div>

<?php
}

function lugfooter () {
?>
<div id="footer">
		<p class="helpMessage">Aiutaci a mantenere la LugMap aggiornata!</p>
		<p class="helpMessage">
		Segnalaci nuovi e vecchi Lug, cos&igrave; come eventuali correzioni o errori, scrivendo
		alla <a href="http://lists.linux.it/listinfo/lugmap">mailing list pubblica</a>,
		oppure telefonando direttamente ad <a class="generalink" href="mailto:andrea.gelmini@lugbs.linux.it">Andrea Gelmini</a> al 328/7296628.
		</p>
		<p class="helpMessage">
		Partecipa utilizzando il
		<a class="generalink" href="http://github.com/Gelma/LugMap">repository GitHub</a>
		e consultando la
		<a class="generalink" href="https://github.com/Gelma/LugMap/tree/docs">Guida Intergalattica alla LugMap</a>.
		<p class="helpMessage">
		Te ne saremo eternamente grati!
		</p>
</div>
</body>
</html>

<?php
}

?>
