/*
	Buona parte del codice qui presente e' stato copiato da linuxday.it
*/

var map, layer;
var layerurl = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';
var attr = 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';

var MapIcon = L.Icon.extend({
	options:{
		iconSize: [16, 19],
		popupAnchor: [-8, 0],
		iconAnchor: [16, 19],
		iconUrl: '/immagini/icon.png'
	}
});

//https://code.google.com/p/microajax/
function microAjax (B, A) {
	this.bindFunction = function (E,D) {
		return function () {
			return E.apply (D, [D])
		}
	};

	this.stateChange = function (D) {
		if (this.request.readyState == 4) {
			this.callbackFunction (this.request.responseText)
		}
	};

	this.getRequest = function () {
		if (window.ActiveXObject) {
			return new ActiveXObject ("Microsoft.XMLHTTP")
		}
		else {
			if (window.XMLHttpRequest) {
				return new XMLHttpRequest ()
			}
		}

		return false
	};

	this.postBody = (arguments [2] || "");
	this.callbackFunction = A;
	this.url=B;
	this.request = this.getRequest();

	if (this.request) {
		var C = this.request;
		C.onreadystatechange = this.bindFunction (this.stateChange, this);
		if (this.postBody !== "") {
			C.open ("POST", B, true);
			C.setRequestHeader ("X-Requested-With", "XMLHttpRequest");
			C.setRequestHeader ("Content-type", "application/x-www-form-urlencoded");
			C.setRequestHeader ("Connection", "close")
		}
		else {
			C.open ("GET", B, true)
		}

		C.send (this.postBody)
	}
};

function init () {
	var tile = new L.TileLayer(layerurl, {maxZoom: 18, attribution: attr});

	map = new L.Map('map', {zoomControl: false});

	lon = 12.483215;
	lat = 41.979911;

	map.setView (new L.LatLng (lat, lon), 5);
	map.addLayer (tile);

	var f = $('input[name=coords_file]').val ();

	microAjax (f, function (res) { 
		var feat = JSON.parse (res);
		loadLayer (feat);
	});
}

function loadLayer(url) {
	var myLayer = L.geoJson(url,{
		onEachFeature: function onEachFeature(feature, layer) {
			if (feature.properties && feature.properties.name) {
				layer.bindPopup (feature.properties.name + "<br/><a href='" + feature.properties.website + "'>Sito web</a>");
			}
		},
		pointToLayer: function (feature, latlng) {
			var marker = L.marker (latlng, {icon: new MapIcon()});
			return marker;
		}
	}).addTo(map);
}

$(document).ready(function(){
	init ();
});

