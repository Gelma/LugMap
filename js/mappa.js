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

	map.addLayers([mapnik]);

	map.addControl(new OpenLayers.Control.Navigation());
	map.addControl(new OpenLayers.Control.PanZoomBar() );
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.ScaleLine());

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
