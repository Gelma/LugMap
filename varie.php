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

/***************************************************************************************************************/

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

/***************************************************************************************************************/

function lugfooter () {
?>

<div id="footer">
      <p class="helpMessage">Aiutaci a mantenere la LugMap aggiornata!</p>
      <p class="helpMessage">
        Segnalaci nuovi/vecchi Lug, cos&igrave; come eventuali correzioni/errori, mandando
        una mail a <a class="generalink" href="mailto:lugmap@linux.it">lugmap@linux.it</a>, oppure telefonando direttamente ad
        <a class="generalink" href="mailto:andrea.gelmini@lugbs.linux.it">Andrea Gelmini</a> al 328/7296628.
      </p>
      <p class="helpMessage">
        Agli stessi recapiti
        puoi richiedere l'accesso in scrittura al relativo <a class="generalink" href="http://github.com/Gelma/LugMap">repository GitHub.</a>
        Te ne saremo eternamente grati!
      </p>
</div>

</body>
</html>

<?php
}

/***************************************************************************************************************/

function do_head ($title = null) {
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Mappa dei Linux User Groups Italiani<?php if ($title != null) echo ": $title"; ?></title>

		<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA1wmNYgsPLSLzBfUdqFykjxQR8KvcGyCdgVa1pp5vyItO0ej8oxRFxpi5aceT4KQUnwoDtmcRMpZ5iA"></script>
		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js"></script>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="forge/lug-o-matic/generator.js"></script>
		<link rel="stylesheet" href="assets/css/main.css" />

		<script type="text/javascript">
			<!--

			/*
				Buona parte del codice qui presente e' stato copiato da linuxday.it
			*/

			var map, layer;

			OpenLayers.Feature.prototype.createPopup = function (closeBox) {
				if (this.lonlat != null) {
					var id = this.id + "_popup";
					var anchor = this.marker ? this.marker.icon : null;

					this.popup = new (this.popupClass)(id, this.lonlat, this.data.popupSize, this.data.popupContentHTML, anchor, true);
					this.popup.autoSize = true;

					if (this.data.overflow != null)
						this.popup.contentDiv.style.overflow = 'auto';

					this.popup.feature = this;
				}

				return this.popup;
			}

			function lonLatToMercator(ll) {
				var lon = ll.lon * 20037508.34 / 180;
				var lat = Math.log (Math.tan ((90 + ll.lat) * Math.PI / 360)) / (Math.PI / 180);
				lat = lat * 20037508.34 / 180;
				return new OpenLayers.LonLat(lon, lat);
			}

			function osm_getTileURL(bounds) {
				var res = this.map.getResolution();
				var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
				var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
				var z = this.map.getZoom();
				var limit = Math.pow(2, z);

				if (y < 0 || y >= limit) {
					return OpenLayers.Util.getImagesLocation() + "404.png";
				} else {
					x = ((x % limit) + limit) % limit;
					return this.url + z + "/" + x + "/" + y + "." + this.type;
				}
			}

			function init () {
				var options = {
			  		projection: new OpenLayers.Projection("EPSG:900913"),
			  		displayProjection: new OpenLayers.Projection("EPSG:4326"),
			  		units: "m",
			  		maxResolution: 156543.0339,
			  		controls:[], numZoomLevels:20,
			  		maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508.34)
				}

				map = new OpenLayers.Map('map', options);

				var mapnik = new OpenLayers.Layer.TMS( "OSM Mapnik", "http://tile.openstreetmap.org/",
					{ type: 'png', getURL: osm_getTileURL, displayOutsideMaxExtent: true, attribution: '<a href="http://www.openstreetmap.org/">OpenStreetMap - slippy map</a>',
					isBaseLayer: true, visibility: true, numZoomLevels:20 } );

				var gmap = new OpenLayers.Layer.Google("Google Streets", {numZoomLevels: 20} );

				map.addLayers([mapnik, gmap]);

				map.addControl(new OpenLayers.Control.Navigation());
				map.addControl(new OpenLayers.Control.PanZoomBar() );
				map.addControl(new OpenLayers.Control.Permalink());
				map.addControl(new OpenLayers.Control.ScaleLine());
				map.addControl(new OpenLayers.Control.LayerSwitcher());

				var newl = new OpenLayers.Layer.Text( "LUG", {location: "./dati.txt"} );
				map.addLayer(newl);

				map.setCenter( lonLatToMercator(new OpenLayers.LonLat(12.483215,41.979911)),6);

				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition (
						function (position) {
							var lon = position.coords.longitude * 20037508.34 / 180;
							var lat = Math.log (Math.tan ((90 + position.coords.latitude) * Math.PI / 360)) / (Math.PI / 180);
							lat = lat * 20037508.34 / 180;

							var userLocation = new OpenLayers.Feature.Vector(
								new OpenLayers.Geometry.Point(lon, lat), {},
								{externalGraphic: 'http://lugmap.it/images/icon_user.png', graphicHeight: 19, graphicWidth: 16}
							);
							var vectorLayer = new OpenLayers.Layer.Vector("Overlay");
							vectorLayer.addFeatures(userLocation);
							map.addLayer(vectorLayer);
						},

						function (error) {
							/* dummy */
						},

						{
							timeout: (5 * 1000),
							maximumAge: (1000 * 60 * 15),
							enableHighAccuracy: true
						}
					);
				}
			}

			$(document).ready(function(){
				init ();
			});

			-->
		</script>
	</head>

	<body>
		<ul class="verticalslider_tabs">
			<li><a href="index.php">Mappa</a></li>
			<li><a href="regioni.php">Lista delle Regioni</a></li>
			<li><a href="lista.php">Lista Completa</a></li>
			<li><a href="partecipa.php">Partecipa</a></li>
			<li><a href="forge.php">Progetti Collaterali</a></li>

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
	</body>
</html>

<?php
}

?>
