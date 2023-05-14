<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2010-2022  Italian Linux Society - http://www.linux.it

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.*/
?>
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
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Nobile|Nobile:b" />


  <script type="text/javascript" src="https://www.linux.it/shared/index.php?f=jquery.js"></script>
  <!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script> -->
  <script type="text/javascript" src="https://www.linux.it/shared/index.php?f=bootstrap.js"></script>
  <link href="https://www.linux.it/shared/?f=bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="https://www.linux.it/shared/?f=main.css" rel="stylesheet" type="text/css" />

  <meta name="dcterms.creator" content="Italian Linux Society" />
  <meta name="dcterms.type" content="Text" />
  <link rel="publisher" href="http://www.ils.org/" />

  <meta name="twitter:title" content="LugMap" />
  <meta name="twitter:creator" content="@ItaLinuxSociety" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="http://lugmap.linux.it/" />
  <meta name="twitter:image" content="https://lugmap.linux.it/immagini/tw.png" />

  <meta property="og:site_name" content="LugMap" />
  <meta property="og:title" content="LugMap" />
  <meta property="og:url" content="http://lugmap.linux.it/" />
  <meta property="og:image" content="https://lugmap.linux.it/immagini/fb.png" />
  <meta property="og:type" content="website" />
  <meta property="og:country-name" content="Italy" />
  <meta property="og:email" content="webmaster@linux.it" />
  <meta property="og:locale" content="it_IT" />
  <meta property="og:description" content="La mappa dei Linux Users Groups italiani" />

  


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
<!-- <a href="http://lugmap.linux.it/css/contact.php">select</a> -->
<body>

<div id="header">
	<img src="/immagini/logo.png" alt="Logo LugMap">
	<div id="maintitle">LugMap</div>
	<div id="payoff">La mappa dei Linux Users Groups italiani</div>

	<div class="menu">
		<nav class="navbar navbar-expand-md navbar-light">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="main-menu">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item"><a class="nav-link" href="/">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="/mappa/">Mappa</a></li>
					<li class="nav-item"><a class="nav-link" href="/assistenza/">Assistenza</a></li>
					<li class="nav-item"><a class="nav-link" href="/contatti">Contatti</a></li>
				</ul>
			</div>
		</nav>

		<p class="social mt-2">
			<a href="https://twitter.com/ItaLinuxSociety"><img src="https://www.linux.it/shared/?f=immagini/twitter.svg" alt="ILS su Twitter"></a>
			<a href="https://www.facebook.com/ItaLinuxSociety/"><img src="https://www.linux.it/shared/?f=immagini/facebook.svg" alt="ILS su Facebook"></a>
			<a href="https://www.instagram.com/ItaLinuxSociety"><img src="https://www.linux.it/shared/?f=immagini/instagram.svg" alt="ILS su Instagram"></a>
			<a href="https://mastodon.uno/@ItaLinuxSociety/"><img src="https://www.linux.it/shared/?f=immagini/mastodon.svg" alt="ILS su Mastodon"></a>
			<a href="https://github.com/Gelma/LugMap"><img src="https://www.linux.it/shared/?f=immagini/github.svg" alt="La LugMap su GitLab"></a>
		</p>
	</div>
</div>

<?php
}

function lugfooter () {
?>

<div id="ils_footer" class="mt-5">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="cell">
					<span style="text-align: center; display: block">
						<a rel="nofollow" href="https://www.gnu.org/licenses/agpl-3.0-standalone.html">
							<img src="https://www.linux.it/shared/index.php?f=immagini/agpl3.svg" style="border-width:0" loading="lazy" alt="AGPLv3 License">
						</a>

						<a rel="nofollow" href="https://creativecommons.org/publicdomain/zero/1.0/deed.en_US">
							<img src="https://www.linux.it/shared/index.php?f=immagini/cczero.png" style="border-width:0" loading="lazy" alt="Creative Commons License">
						</a>
					</span>
				</div>

				<div class="cell mt-3 text-center">
					<a href="https://www.ils.org/privacy">Informativa Privacy</a>
				</div>
			</div>

			<div class="col-md-3">
				<div class="cell">
					<h2>RESTA AGGIORNATO!</h2>
					<iframe title="Newsletter ILS" src="https://crm.linux.it/form/1" width="100%" height="420" frameborder="0"><p>Your browser does not support iframes.</p></iframe>
				</div>
			</div>

			<div class="col-md-3">
				<div class="cell">
					<h2>Amici</h2>
					<p style="text-align: center">
						<a href="https://www.ils.org/info#aderenti">
							<img src="https://www.ils.org/external/getrandlogo.php" border="0" loading="lazy" alt="Aderenti a Italian Linux Society"><br>
							Scopri tutte le associazioni e le aziende che hanno aderito a Italian Linux Society.
						</a>
					</p>
				</div>
			</div>

			<div class="col-md-3">
				<div class="cell">
					<h2>Network</h2>
					<script type="text/javascript" src="https://www.ils.org/external/widgetils.js" defer=""></script>
					<div id="widgetils"></div>
				</div>
			</div>
		</div>
	</div>

	<div style="clear: both"></div>
</div>

<!-- Matomo -->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(["setDoNotTrack", true]);
  _paq.push(["disableCookies"]);
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//stats.linux.it/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '6']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->

</body>
</html>

<?php
}

function prov_select ($class) {
	?>

	<select class="<?php echo $class ?>" name="prov">
		<option value="-1" selected="selected">Seleziona una Provincia</option>
		<option value="AG">Agrigento</option>
		<option value="AL">Alessandria</option>
		<option value="AN">Ancona</option>
		<option value="AO">Aosta</option>
		<option value="AR">Arezzo</option>
		<option value="AP">Ascoli Piceno</option>
		<option value="AT">Asti</option>
		<option value="AV">Avellino</option>
		<option value="BA">Bari</option>
		<option value="BT">Barletta-Andria-Trani</option>
		<option value="BL">Belluno</option>
		<option value="BN">Benevento</option>
		<option value="BG">Bergamo</option>
		<option value="BI">Biella</option>
		<option value="BO">Bologna</option>
		<option value="BZ">Bolzano</option>
		<option value="BS">Brescia</option>
		<option value="BR">Brindisi</option>
		<option value="CA">Cagliari</option>
		<option value="CL">Caltanissetta</option>
		<option value="CB">Campobasso</option>
		<option value="CI">Carbonia-Iglesias</option>
		<option value="CE">Caserta</option>
		<option value="CT">Catania</option>
		<option value="CZ">Catanzaro</option>
		<option value="CH">Chieti</option>
		<option value="CO">Como</option>
		<option value="CS">Cosenza</option>
		<option value="CR">Cremona</option>
		<option value="KR">Crotone</option>
		<option value="CN">Cuneo</option>
		<option value="EN">Enna</option>
		<option value="FM">Fermo</option>
		<option value="FE">Ferrara</option>
		<option value="FI">Firenze</option>
		<option value="FG">Foggia</option>
		<option value="FC">Forl&igrave;-Cesena</option>
		<option value="FR">Frosinone</option>
		<option value="GE">Genova</option>
		<option value="GO">Gorizia</option>
		<option value="GR">Grosseto</option>
		<option value="IM">Imperia</option>
		<option value="IS">Isernia</option>
		<option value="SP">La Spezia</option>
		<option value="AQ">L'Aquila</option>
		<option value="LT">Latina</option>
		<option value="LE">Lecce</option>
		<option value="LC">Lecco</option>
		<option value="LI">Livorno</option>
		<option value="LO">Lodi</option>
		<option value="LU">Lucca</option>
		<option value="MC">Macerata</option>
		<option value="MN">Mantova</option>
		<option value="MS">Massa-Carrara</option>
		<option value="MT">Matera</option>
		<option value="ME">Messina</option>
		<option value="MI">Milano</option>
		<option value="MO">Modena</option>
		<option value="MB">Monza e della Brianza</option>
		<option value="NA">Napoli</option>
		<option value="NO">Novara</option>
		<option value="NU">Nuoro</option>
		<option value="OT">Olbia-Tempio</option>
		<option value="OR">Oristano</option>
		<option value="PD">Padova</option>
		<option value="PA">Palermo</option>
		<option value="PR">Parma</option>
		<option value="PV">Pavia</option>
		<option value="PG">Perugia</option>
		<option value="PU">Pesaro e Urbino</option>
		<option value="PE">Pescara</option>
		<option value="PC">Piacenza</option>
		<option value="PI">Pisa</option>
		<option value="PT">Pistoia</option>
		<option value="PN">Pordenone</option>
		<option value="PZ">Potenza</option>
		<option value="PO">Prato</option>
		<option value="RG">Ragusa</option>
		<option value="RA">Ravenna</option>
		<option value="RC">Reggio Calabria</option>
		<option value="RE">Reggio Emilia</option>
		<option value="RI">Rieti</option>
		<option value="RN">Rimini</option>
		<option value="RM">Roma</option>
		<option value="RO">Rovigo</option>
		<option value="SA">Salerno</option>
		<option value="VS">Medio Campidano</option>
		<option value="SS">Sassari</option>
		<option value="SV">Savona</option>
		<option value="SI">Siena</option>
		<option value="SR">Siracusa</option>
		<option value="SO">Sondrio</option>
		<option value="TA">Taranto</option>
		<option value="TE">Teramo</option>
		<option value="TR">Terni</option>
		<option value="TO">Torino</option>
		<option value="OG">Ogliastra</option>
		<option value="TP">Trapani</option>
		<option value="TN">Trento</option>
		<option value="TV">Treviso</option>
		<option value="TS">Trieste</option>
		<option value="UD">Udine</option>
		<option value="VA">Varese</option>
		<option value="VE">Venezia</option>
		<option value="VB">Verbano-Cusio-Ossola</option>
		<option value="VC">Vercelli</option>
		<option value="VR">Verona</option>
		<option value="VV">Vibo Valentia</option>
		<option value="VI">Vicenza</option>
		<option value="VT">Viterbo</option>
	</select>

	<?php
}

function ultimo_aggiornamento () {
?>
   <a href="http://github.com/Gelma/LugMap/commits/lugmap.linux.it">&raquo; Aggiornato al <?php print file_get_contents('../.ultimo_commit') ?>&nbsp;</a><br />
   <a href="mailto:andrea.gelmini@lugbs.linux.it,bob@linux.it?subject=LugMap: segnalazione aggiornamento/errore/refuso">&raquo; Segnala&nbsp;</a>

<?php
}

?>
