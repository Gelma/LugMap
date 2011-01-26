<?php

require_once ('varie.php');

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Mappa dei Linux User Groups Italiani</title>

		<style type="text/css">
			<!--

			.olPopup {
				padding: 5px;
			}

			.olPopup h2 {
				font-size: 15px;
			}

			.intro {
				font-size: 10px;
				color: #000000;
				margin: 10px;
				vertical-align: bottom;
				position: absolute;
				bottom: 0px;
				width: 18%;
				text-align: center;
			}

			.center_frame {
				display: table;
				overflow: hidden;
				height: 100%;
				width: 100%;
				margin: auto;
			}

			.duallist {
				display: table-cell;
				vertical-align: middle;
				width: 70%;
				margin: auto;
				text-align: center;
			}

			.duallist ul:first-child {
				float: left;
				width: 45%;
			}

			.duallist ul:last-child {
				float: right;
				width: 45%;
			}

			.duallist li {
				list-style-type: none;
				display: block;
				font-size: 40px;
			}

			.duallist li a {
				color: #000000;
				text-decoration: none;
			}

			.textual {
				display: table-cell;
				vertical-align: middle;
				width: 100%;
				margin: auto;
				text-align: center;
				padding: 50px;
			}

			.description {
				margin: auto;
				text-align: center;
				padding: 50px;
			}

			.verticalslider {margin: 0 auto;}

			/* Tabs */
			.verticalslider_tabs {float: left;width: 20%;}
			.verticalslider_tabs, .verticalslider_tabs li{margin: 0px; padding: 0px;}
			.verticalslider_tabs li{list-style-type: none;}
			.verticalslider_tabs a:link, .verticalslider_tabs a:visited{display: block; height: 29px; padding: 14px 10px 6px 10px; font-family: Georgia, "Times New Roman", Times, serif; font-size: 20px; font-weight: bold;color: #333333;text-decoration: none;}
			.verticalslider_tabs a:hover, .verticalslider_tabs a:active{ background: url(images/tabHoverBG.jpg) bottom repeat-x; background-color: #fcfcfc;}
			.verticalslider_tabs li:first-child a:link, .verticalslider_tabs li:first-child a:visited{border-top: none; height: 30px;}
			.verticalslider_tabs .activeTab a:link, .verticalslider_tabs .activeTab a:visited{background: #ffffff; border-right: 1px solid #ffffff;}
			.verticalslider_tabs .activeTab a:hover, .verticalslider_tabs .activeTab a:active{background: #ffffff; border-right: 1px solid #ffffff;}
			.verticalslider .arrow {background: url(images/arrow.png); width: 27px; height: 60px; position: absolute; z-index: 1000; margin-left: 250px; margin-top: -55px; }

			/* Contents */
			.verticalslider_contents li{margin: 0px; padding: 0px;padding: 0px; width: 100%; height: 100%;}
			.verticalslider_contents li h2{font-family: Georgia, "Times New Roman", Times, serif, font-size: 15px; color: #333333;margin: 5px 20px; padding: 0px;}
			.verticalslider_contents li div{color: #333333;font-family: Tahoma, Geneva, sans-serif; font-size: 13px; }
			.verticalslider_contents .page{display: none;list-style-type: none;}
			.verticalslider_contents{float: left;width: 80%;display: inline; margin: 0px; padding: 0px; height: 100%;}
			.verticalslider_contents .activeContent{display: inline;}

			-->
		</style>

		<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA1wmNYgsPLSLzBfUdqFykjxQR8KvcGyCdgVa1pp5vyItO0ej8oxRFxpi5aceT4KQUnwoDtmcRMpZ5iA"></script>
		<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js"></script>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/verticaltabs.pack.js"></script>
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
			}

			$(document).ready(function(){
				$("#pager").verticaltabs({slideShow: false, activeIndex: 0});
				init ();
			});

			-->
		</script>
	</head>

	<body>
		<div class="verticalslider" id="pager">
			<ul class="verticalslider_tabs">
				<li><a href="#">Mappa</a></li>
				<li><a href="#">Lista delle Regioni</a></li>
				<li><a href="#">Lista Completa</a></li>
				<li><a href="#">Partecipa</a></li>
				<li><a href="#">Progetti Collaterali</a></li>

				<li>
					<p class="intro">
						<img src="images/logo.png" alt="lugmap.it" />
					</p>
				</li>
			</ul>

			<ul class="verticalslider_contents">
				<li>
					<div id="map" class="smallmap"></div>
				</li>

				<li class="page">
					<div class="center_frame">
						<div class="duallist">
							<ul>
								<li><a href="http://abruzzo.lugmap.it/">Abruzzo</a></li>
								<li><a href="http://basilicata.lugmap.it/">Basilicata</a></li>
								<li><a href="http://calabria.lugmap.it/">Calabria</a></li>
								<li><a href="http://campania.lugmap.it/">Campania</a></li>
								<li><a href="http://emilia.lugmap.it/">Emilia Romagna</a></li>
								<li><a href="http://friuli.lugmap.it/">Friuli Venezia-Giulia</a></li>
								<li><a href="http://lazio.lugmap.it/">Lazio</a></li>
								<li><a href="http://liguria.lugmap.it/">Liguria</a></li>
								<li><a href="http://lombardia.lugmap.it/">Lombardia</a></li>
								<li><a href="http://marche.lugmap.it/">Marche</a></li>
								<li><a href="http://molise.lugmap.it/">Molise</a></li>
							</ul>
							<ul>
								<li><a href="http://piemonte.lugmap.it/">Piemonte</a></li>
								<li><a href="http://puglia.lugmap.it/">Puglia</a></li>
								<li><a href="http://sardegna.lugmap.it/">Sardegna</a></li>
								<li><a href="http://sicilia.lugmap.it/">Sicilia</a></li>
								<li><a href="http://toscana.lugmap.it/">Toscana</a></li>
								<li><a href="http://trentino.lugmap.it/">Trentino Alto Adige</a></li>
								<li><a href="http://umbria.lugmap.it/">Umbria</a></li>
								<li><a href="http://valle.lugmap.it/">Valle d'Aosta</a></li>
								<li><a href="http://veneto.lugmap.it/">Veneto</a></li>
								<li>&nbsp;</li>
								<li><a href="http://italia.lugmap.it/">Italia</a></li>
							</ul>
						</div>
					</div>
				</li>

				<li class="page">
					<div>
						<?php

						$db_regione = array ();

						foreach (glob ('./db/*.txt') as $db_file) {
							$db_regione = array_merge ($db_regione, file ($db_file));
							sort ($db_regione);
						}

						?>

						<div class="description">
							<p>
								Ci sono <?php echo count ($db_regione); ?> Linux User Groups in Italia.
							</p>

							<p>
								Probabilmente, almeno uno di questi è vicino a casa tua.
							</p>
						</div>

						<table id="lugListTable">
							<thead>
								<tr>
									<th>Provincia</th>
									<th>Denominazione</th>
									<th>Zona</th>
									<th>Contatti</th>
								</tr>
							</thead>

							<tfoot>
								<tr>
									<td colspan="4"></td>
								</tr>
							</tfoot>

							<tbody>
								<?php

								while (list ($nriga, $linea) = each ($db_regione)):
									$campi = explode("|",$linea);
									$provincia    = $campi[0];
									$denominazione  = $campi[1];
									$zona     = $campi[2];
									$contatti   = $campi[3];
									?>

									<tr class="row_<?php echo ($nriga % 2); ?>">
									<td class="province"><?php echo $provincia ?></td>
									<td><?php echo $denominazione ?></td>
									<td><?php echo $zona ?></td>
									<td class="contactUrl"><a href="<?php echo $contatti?>"><?php echo $contatti ?></a></td>
									</tr>
								<?php endwhile;?>
							</tbody>
						</table>
					</div>
				</li>

				<li class="page">
					<div class="center_frame">
						<div class="textual">
							<p>
								Questa LugMap è frutto di un lavoro collettivo e collaborativo, cui tutti sono invitati a partecipare con suggerimenti e segnalazioni.
							</p>
							<p>
								Il sito viene periodicamente aggiornato sulla base di un <a href="https://github.com/Gelma/LugMap">repository GitHub</a> pubblicamente leggibile, in cui le informazioni sui LUG sono organizzate in files direttamente accessibili (ad esempio: http://github.com/Gelma/LugMap/blob/master/db/nome_regione.txt) e facilmente parsabili da applicazioni esterne.
							</p>
							<p>
								Segnalaci nuovi/vecchi LUG, così come eventuali correzioni ed errori, mandando una mail a <a href="mailto:lugmap@linux.it">lugmap@linux.it</a>.
							</p>
						</div>
					</div>
				</li>

				<li class="page">
					<div class="center_frame">
						<div class="textual">
							<p>
								In virtù del semplice formato con cui le informazioni sui LUG italiani sono salvate e rese accessibili, è possibile implementare rapidamente nuove applicazioni web (ma non solo!) per la promozione, l'organizzazione e la connessione dei gruppi stessi.
							</p>
							<p>
								Qui di seguito alcuni dei progetti collaterali nati intorno a tali dati, e spunti per ulteriori creazioni.
							</p>
							<p>
								Se hai una nuova idea e vuoi condividerla con tutti gli altri su questo sito, mandaci una segnalazione all'indirizzo mail <a href="lugmap@linux.it">lugmap@linux.it</a>.
							</p>

							<ul>
								<li>Widget web con la lista dei LUG di una regione, da includere nel tuo sito</li>
								<li>Generatore di OPML coi feeds dei LUG</li>
								<li>Generatore di mappa OpenStreetMaps con le locazioni dei singoli LUG</li>
							</ul>

							<p>
								TODO
							</p>

							<ul>
								<li>Plugin Wordpress con la lista dei LUG di una regione</li>
								<li>Plugin Drupal con la lista dei LUG di una regione</li>
							</ul>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</body>
</html>
