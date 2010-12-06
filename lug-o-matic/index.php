<?php
	require_once ('utils.php');

	if (isset ($_GET ['region'])) {
		require_once ('widget.php');
		exit (0);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="it">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>lug-o-matic</title>

		<script type="text/javascript" src="jquery.js"></script>

		<script type="text/javascript">
			function calcSize () {
				document.getElementById('lugmap').height = document.getElementById('lugmap').contentWindow.document.body.scrollHeight;
			}

			function htmlentities (string, quote_style) {
				// http://kevin.vanzonneveld.net
				// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
				// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
				// +   improved by: nobbler
				// +    tweaked by: Jack
				// +   bugfixed by: Onno Marsman
				// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
				// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
				// +      input by: Ratheous
				// -    depends on: get_html_translation_table
				// *     example 1: htmlentities('Kevin & van Zonneveld');
				// *     returns 1: 'Kevin &amp; van Zonneveld'
				// *     example 2: htmlentities("foo'bar","ENT_QUOTES");
				// *     returns 2: 'foo&#039;bar'

				var hash_map = {}, symbol = '', tmp_str = '', entity = '';
				tmp_str = string.toString();

				if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
					return false;
				}
				hash_map["'"] = '&#039;';
				for (symbol in hash_map) {
					entity = hash_map[symbol];
					tmp_str = tmp_str.split(symbol).join(entity);
				}

				return tmp_str;
			}

			function get_html_translation_table (table, quote_style) {
				// http://kevin.vanzonneveld.net
				// +   original by: Philip Peterson
				// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
				// +   bugfixed by: noname
				// +   bugfixed by: Alex
				// +   bugfixed by: Marco
				// +   bugfixed by: madipta
				// +   improved by: KELAN
				// +   improved by: Brett Zamir (http://brett-zamir.me)
				// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
				// +      input by: Frank Forte
				// +   bugfixed by: T.Wild
				// +      input by: Ratheous
				// %          note: It has been decided that we're not going to add global
				// %          note: dependencies to php.js, meaning the constants are not
				// %          note: real constants, but strings instead. Integers are also supported if someone
				// %          note: chooses to create the constants themselves.
				// *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
				// *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

				var entities = {}, hash_map = {}, decimal = 0, symbol = '';
				var constMappingTable = {}, constMappingQuoteStyle = {};
				var useTable = {}, useQuoteStyle = {};

				// Translate arguments
				constMappingTable[0]      = 'HTML_SPECIALCHARS';
				constMappingTable[1]      = 'HTML_ENTITIES';
				constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
				constMappingQuoteStyle[2] = 'ENT_COMPAT';
				constMappingQuoteStyle[3] = 'ENT_QUOTES';

				useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
				useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

				if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
					throw new Error("Table: "+useTable+' not supported');
					// return false;
				}

				entities['38'] = '&amp;';
				if (useTable === 'HTML_ENTITIES') {
					entities['160'] = '&nbsp;';
					entities['161'] = '&iexcl;';
					entities['162'] = '&cent;';
					entities['163'] = '&pound;';
					entities['164'] = '&curren;';
					entities['165'] = '&yen;';
					entities['166'] = '&brvbar;';
					entities['167'] = '&sect;';
					entities['168'] = '&uml;';
					entities['169'] = '&copy;';
					entities['170'] = '&ordf;';
					entities['171'] = '&laquo;';
					entities['172'] = '&not;';
					entities['173'] = '&shy;';
					entities['174'] = '&reg;';
					entities['175'] = '&macr;';
					entities['176'] = '&deg;';
					entities['177'] = '&plusmn;';
					entities['178'] = '&sup2;';
					entities['179'] = '&sup3;';
					entities['180'] = '&acute;';
					entities['181'] = '&micro;';
					entities['182'] = '&para;';
					entities['183'] = '&middot;';
					entities['184'] = '&cedil;';
					entities['185'] = '&sup1;';
					entities['186'] = '&ordm;';
					entities['187'] = '&raquo;';
					entities['188'] = '&frac14;';
					entities['189'] = '&frac12;';
					entities['190'] = '&frac34;';
					entities['191'] = '&iquest;';
					entities['192'] = '&Agrave;';
					entities['193'] = '&Aacute;';
					entities['194'] = '&Acirc;';
					entities['195'] = '&Atilde;';
					entities['196'] = '&Auml;';
					entities['197'] = '&Aring;';
					entities['198'] = '&AElig;';
					entities['199'] = '&Ccedil;';
					entities['200'] = '&Egrave;';
					entities['201'] = '&Eacute;';
					entities['202'] = '&Ecirc;';
					entities['203'] = '&Euml;';
					entities['204'] = '&Igrave;';
					entities['205'] = '&Iacute;';
					entities['206'] = '&Icirc;';
					entities['207'] = '&Iuml;';
					entities['208'] = '&ETH;';
					entities['209'] = '&Ntilde;';
					entities['210'] = '&Ograve;';
					entities['211'] = '&Oacute;';
					entities['212'] = '&Ocirc;';
					entities['213'] = '&Otilde;';
					entities['214'] = '&Ouml;';
					entities['215'] = '&times;';
					entities['216'] = '&Oslash;';
					entities['217'] = '&Ugrave;';
					entities['218'] = '&Uacute;';
					entities['219'] = '&Ucirc;';
					entities['220'] = '&Uuml;';
					entities['221'] = '&Yacute;';
					entities['222'] = '&THORN;';
					entities['223'] = '&szlig;';
					entities['224'] = '&agrave;';
					entities['225'] = '&aacute;';
					entities['226'] = '&acirc;';
					entities['227'] = '&atilde;';
					entities['228'] = '&auml;';
					entities['229'] = '&aring;';
					entities['230'] = '&aelig;';
					entities['231'] = '&ccedil;';
					entities['232'] = '&egrave;';
					entities['233'] = '&eacute;';
					entities['234'] = '&ecirc;';
					entities['235'] = '&euml;';
					entities['236'] = '&igrave;';
					entities['237'] = '&iacute;';
					entities['238'] = '&icirc;';
					entities['239'] = '&iuml;';
					entities['240'] = '&eth;';
					entities['241'] = '&ntilde;';
					entities['242'] = '&ograve;';
					entities['243'] = '&oacute;';
					entities['244'] = '&ocirc;';
					entities['245'] = '&otilde;';
					entities['246'] = '&ouml;';
					entities['247'] = '&divide;';
					entities['248'] = '&oslash;';
					entities['249'] = '&ugrave;';
					entities['250'] = '&uacute;';
					entities['251'] = '&ucirc;';
					entities['252'] = '&uuml;';
					entities['253'] = '&yacute;';
					entities['254'] = '&thorn;';
					entities['255'] = '&yuml;';
				}

				if (useQuoteStyle !== 'ENT_NOQUOTES') {
					entities['34'] = '&quot;';
				}
				if (useQuoteStyle === 'ENT_QUOTES') {
					entities['39'] = '&#39;';
				}
				entities['60'] = '&lt;';
				entities['62'] = '&gt;';


				// ascii decimals to real symbols
				for (decimal in entities) {
					symbol = String.fromCharCode(decimal);
					hash_map[symbol] = entities[decimal];
				}

				return hash_map;
			}

			$(document).ready (function () {
				var js_code = htmlentities ('<script language="JavaScript"><!--\nfunction calcSize () { document.getElementById(\'lugmap\').height = document.getElementById(\'lugmap\').contentWindow.document.body.scrollHeight; }\n\/\/--><\/script>', 'ENT_NOQUOTES');

				$('select[name=region]').change (function (event) {
					var region = $('select[name=region] option:selected').val ();
					var previewcode = $('.preview').html ().replace (/region=[a-z]*"/, 'region=' + region +  '"');
					var previewcode = previewcode.replace (/ height="[0-9]*"/, '');
					$('.preview').empty ().append (previewcode);
					$('.code').empty ().append (js_code + "\n" + htmlentities (previewcode, 'ENT_NOQUOTES'));
				});
			});
		</script>

		<style type="text/css">
		<!--
			body {
				padding: 50px;
				font-family: Helvetica;
				font-size: 12px;
			}

			a {
				color: #FF0000;
				text-decoration: none;
			}

			.notes {
				width: 50%;
				text-align: right;
			}

			.questions {
				font-size: 12px;
			}

			.important {
				font-weight: bold;
				font-size: 15px;
			}

			.generator {
				float: right;
				width: 45%;
				text-align: center;
			}

			.code {
				width: 100%;
				height: 200px;
			}

			.preview {
				width: 100%;
			}
		-->
		</style>
	</head>

	<body>
		<div class="generator">
			<p>
				Regione <select name="region">
					<?php
						foreach ($elenco_regioni as $simple => $name) {
							?>

							<option value="<?php echo $simple; ?>"><?php echo $name; ?></option>

							<?php
						}
					?>
				</select>
			</p>

			<div class="preview"><iframe id="lugmap" src="<?php echo $app_url; ?>?region=abruzzo" onLoad="calcSize();" width="200px" scrolling="no" frameborder="0"></iframe></div>

			<br />

			<textarea class="code"><?php echo htmlentities (
'<script language="JavaScript"><!--
function calcSize () { document.getElementById(\'lugmap\').height = document.getElementById(\'lugmap\').contentWindow.document.body.scrollHeight;
}
//--></script>
<iframe id="lugmap" src="' . $app_url . '?region=abruzzo"
onLoad="calcSize();" width="200px" scrolling="no" frameborder="0"></iframe>'); ?>
			</textarea>
		</div>

		<div class="notes">
			<div class="questions">
				<p>
					Sei un sostenitore di Linux e del software libero?
				</p>

				<p>
					Vuoi dare una mano nel promuovere le attività pro-freesoftware della tua zona?
				</p>

				<p>
					Hai un sito web?
				</p>
			</div>

			<div class="important">
				Usa lug-o-matic!
			</div>

			<div>
				<p>
					Usando il generatore qui accanto puoi ottenere un semplice widget web da copiare ed incollare sul tuo sito, con l'elenco aggiornato dei Linux User Group della regione selezionata.
				</p>

				<p>
					Le informazioni saranno sempre allineate con quelle della <a href="http://www.linux.it/LUG/">LUGMap</a> di <a href="http://www.linux.it/">Italian Linux Society</a>, reperibili anche in formato raw sull'<a href="https://github.com/Gelma/LugMap/">apposito repository</a>.
				</p>
			</div>
		</div>
	</body>
</html>
