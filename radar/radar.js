/*
	Preso da
	http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
*/
function hashCode (string) {
	var hash = 0;

	if (string.length == 0)
		return hash;

	for (i = 0; i < string.length; i++) {
		char = string.charCodeAt (i);
		hash = ((hash << 5) - hash) + char;
		hash = hash & hash;
	}

	return hash;
}

var universities = [
	{
		"label": "Università degli Studi \"Gabriele D'Annunzio\"",
		"value": "uni:gdannunzio"
	},
	{
		"label": "Università degli Studi di L'Aquila",
		"value": "uni:aquila"
	},
	{
		"label": "Università degli Studi di Teramo",
		"value": "uni:teramo"
	},
	{
		"label": "Università degli Studi della Basilicata",
		"value": "uni:basilicata"
	},
	{
		"label": "Università degli Studi \"Magna Graecia\" di Catanzaro",
		"value": "uni:catanzaro"
	},
	{
		"label": "Università degli Studi Mediterranea di Reggio Calabria",
		"value": "uni:rcalabria"
	},
	{
		"label": "Università della Calabria",
		"value": "uni:calabria"
	},
	{
		"label": "Istituto Universitario \"Suor Orsola Benincasa\"",
		"value": "uni:benincasa"
	},
	{
		"label": "Seconda Università degli Studi di Napoli",
		"value": "uni:napoli2"
	},
	{
		"label": "Università degli Studi del Sannio",
		"value": "uni:sannio"
	},
	{
		"label": "Università degli Studi di Napoli \"Federico II\"",
		"value": "uni:napoli"
	},
	{
		"label": "Università degli Studi di Napoli \"L'Orientale\"",
		"value": "uni:napoli3"
	},
	{
		"label": "Università degli Studi di Napoli \"Partenophe\"",
		"value": "uni:napoli4"
	},
	{
		"label": "Università degli Studi di Salerno",
		"value": "uni:salerno"
	},
	{
		"label": "Università degli Studi di Bologna",
		"value": "uni:bologna"
	},
	{
		"label": "Università degli Studi di Ferrara",
		"value": "uni:ferrara"
	},
	{
		"label": "Università degli Studi di Modena e Reggio Emilia",
		"value": "uni:remilia"
	},
	{
		"label": "Università degli Studi di Parma",
		"value": "uni:parma"
	},
	{
		"label": "SISSA - Scuola Internazionale Superiore di Studi Avanzati",
		"value": "uni:sissa"
	},
	{
		"label": "Università degli Studi di Trieste",
		"value": "uni:trieste"
	},
	{
		"label": "Università degli Studi di Udine",
		"value": "uni:udine"
	},
	{
		"label": "IUSM - Università degli Studi di Roma \"Foro Italico\" ",
		"value": "uni:iusm"
	},
	{
		"label": "Libera Università degli Studi \"San Pio V\"",
		"value": "uni:sanpio"
	},
	{
		"label": "LUISS - Libera Università Internazionale degli Studi Sociali Guido Carli",
		"value": "uni:luiss"
	},
	{
		"label": "LUMSA - Libera Università \"Maria Ss. Assunta\"",
		"value": "uni:lumsa"
	},
	{
		"label": "Università \"Campus Bio-Medico\" di Roma",
		"value": "uni:biomedico"
	},
	{
		"label": "Università degli Studi della Tuscia",
		"value": "uni:tuscia"
	},
	{
		"label": "Università degli Studi di Cassino",
		"value": "uni:cassino"
	},
	{
		"label": "Università degli Studi di Roma \"La Sapienza\"",
		"value": "uni:sapienza"
	},
	{
		"label": "Università degli Studi di Roma \"Tor Vergata\"",
		"value": "uni:torvergata"
	},
	{
		"label": "Università degli Studi Europea di Roma",
		"value": "uni:europea"
	},
	{
		"label": "Università degli Studi \"Roma Tre\"",
		"value": "uni:roma3"
	},
	{
		"label": "Università degli Studi di Genova",
		"value": "uni:genova"
	},
	{
		"label": "IULM - Libera Università di Lingue e Comunicazione",
		"value": "uni:iulm"
	},
	{
		"label": "Politecnico di Milano",
		"value": "uni:milano"
	},
	{
		"label": "Università Carlo Cattaneo - LIUC",
		"value": "uni:liuc"
	},
	{
		"label": "Università Cattolica del Sacro Cuore",
		"value": "uni:sacrocuore"
	},
	{
		"label": "Università Commerciale Luigi Bocconi",
		"value": "uni:bocconi"
	},
	{
		"label": "Università degli Studi dell'Insubria Varese-Como",
		"value": "uni:insubria"
	},
	{
		"label": "Università degli Studi di Bergamo",
		"value": "uni:bergamo"
	},
	{
		"label": "Università degli Studi di Brescia",
		"value": "uni:brescia"
	},
	{
		"label": "Università degli Studi di Milano",
		"value": "uni:milano"
	},
	{
		"label": "Università degli Studi di Milano-Bicocca",
		"value": "uni:bicocca"
	},
	{
		"label": "Università degli Studi di Pavia",
		"value": "uni:pavia"
	},
	{
		"label": "Università Vita-Salute San Raffaele",
		"value": "uni:sanraffaele"
	},
	{
		"label": "Università Politecnica delle Marche",
		"value": "uni:marche"
	},
	{
		"label": "Università degli Studi di Camerino",
		"value": "uni:camerino"
	},
	{
		"label": "Università degli Studi di Macerata",
		"value": "uni:macerata"
	},
	{
		"label": "Università degli Studi di Urbino Carlo Bo",
		"value": "uni:urbino"
	},
	{
		"label": "Università degli Studi del Molise",
		"value": "uni:molise"
	},
	{
		"label": "Politecnico di Torino",
		"value": "uni:politorino"
	},
	{
		"label": "Università degli Studi del Piemonte Orientale \"Amedeo Avogadro\"",
		"value": "uni:avogadro"
	},
	{
		"label": "Università degli Studi di Torino",
		"value": "uni:torino"
	},
	{
		"label": "Università di Scienze Gastronomiche",
		"value": "uni:gastronomiche"
	},
	{
		"label": "LUM - Libera Università Mediterranea \"Jean Monnet\"",
		"value": "uni:lum"
	},
	{
		"label": "Politecnico di Bari",
		"value": "uni:polibari"
	},
	{
		"label": "Università degli Studi di Bari",
		"value": "uni:bari"
	},
	{
		"label": "Università degli Studi di Foggia",
		"value": "uni:foggia"
	},
	{
		"label": "Università del Salento - Lecce",
		"value": "uni:salento"
	},
	{
		"label": "Università degli Studi di Cagliari",
		"value": "uni:cagliari"
	},
	{
		"label": "Università degli Studi di Sassari",
		"value": "uni:sassari"
	},
	{
		"label": "Università degli Studi di Catania",
		"value": "uni:catania"
	},
	{
		"label": "Università degli Studi di Messina",
		"value": "uni:messina"
	},
	{
		"label": "Università degli Studi di Palermo",
		"value": "uni:palermo"
	},
	{
		"label": "Scuola Normale Superiore - Pisa",
		"value": "uni:normale"
	},
	{
		"label": "Scuola Superiore di Studi Universitari e di Perfezionamento \"Sant'Anna\" - Pisa",
		"value": "uni:santanna"
	},
	{
		"label": "Università degli Studi di Firenze",
		"value": "uni:firenze"
	},
	{
		"label": "Università degli Studi di Pisa",
		"value": "uni:pisa"
	},
	{
		"label": "Università degli Studi di Siena",
		"value": "uni:siena"
	},
	{
		"label": "Università per Stranieri di Siena",
		"value": "uni:stranierisiena"
	},
	{
		"label": "Libera Università di Bolzano",
		"value": "uni:bolzano"
	},
	{
		"label": "Università degli Studi di Trento",
		"value": "uni:trento"
	},
	{
		"label": "Università degli Studi di Perugia",
		"value": "uni:perugia"
	},
	{
		"label": "Università per Stranieri di Perugia",
		"value": "uni:stranieriperugia"
	},
	{
		"label": "Università della Valle d'Aosta - Université de la Vallée D'Aoste",
		"value": "uni:aosta"
	},
	{
		"label": "Università Iuav di Venezia",
		"value": "uni:venezia"
	},
	{
		"label": "Università \"Ca' Foscari\" di Venezia",
		"value": "uni:foscari"
	},
	{
		"label": "Università degli Studi di Padova",
		"value": "uni:padova"
	},
	{
		"label": "Università degli Studi di Verona",
		"value": "uni:verona"
	},
];

$(document).ready (function () {
	$('#universitytext').autocomplete ({
		minLength: 2,
		source: universities,

		select: function (event, ui) {
			$('#universitytext').val (ui.item.label).attr ('disabled', 'disabled');
			$('#university').val (ui.item.value);
			return false;
		}
	});

	/*

	// Per ora ignoro la facolta', eventualmente la faro' selezionare in un secondo momento

	$('#faculty').hide ();

	$('#university').change (function () {
		index = $('#university option:selected').val ();

		if (index == -1) {
			$('#faculty').hide ();
		}
		else {
			$('#faculty').show ().empty ();
			fac = universities [index].faculty;

			for (a = 0; a < fac.length; a++)
				$('#faculty').append ('<option value="' + hashCode (fac[a]) + '">' + fac[a] + '</option>');
		}
	});

	*/
});

