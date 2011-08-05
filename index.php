<?php

require_once ('funzioni.php');
lugheader ('LugMap');

?>

<table id="introTabel" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 35%;">
      <div>
        Il progetto <b>LugMap</b> ha l'ambizione di offrire un elenco, quanto
        pi&ugrave; aggiornato, delle realt&agrave; che ruotano attorno al perno del
        <a class="generalink" href="http://www.gnu.org/philosophy/free-sw.it.html">Software Libero</a>, come
        <a class="generalink" href="http://it.wikipedia.org/wiki/Linux_User_Group">Linux Users Group</a>,
        <a class="generalink" href="http://it.wikipedia.org/wiki/Hacklab">Hacklab</a> et similia.
        <br />
        Le voci, ordinate per regione e provincia, sono accessibili direttamente dalla cartina e
        dall'elenco a lato, anche in versione
        <a class="generalink" href="http://lugmap.it/">georeferenziata</a>.
      </div>

      <div>
        Il comun denominatore dei gruppi riportati &egrave; la diffusione di
        <a class="generalink" href="http://it.wikipedia.org/wiki/Linux">Linux</a>
        quale punta di diamante del
        <a class="generalink" href="http://www.gnu.org/philosophy/free-sw.it.html">Software Libero</a>.
        <br />
        Questi sono quindi a disposizione di chiunque voglia avvicinarsi a dette tematiche, sia in forma
        telematica (siti Internet, mailing list, email, ecc.), sia de visu (sedi o birrerie che siano).
      </div>

      <br />

      <div>
        Sono disponibili
        <a class="generalink" href="/regione.php">l'elenco completo di tutti i LUG</a>
        ordinati per provincia, una pagina dedicata alle
        <a class="generalink" href="/statistiche.php">statistiche</a>, ed un
        <a class="generalink" href="/lug-o-matic/">widget web</a> utilizzabile su altri siti.
      </div>
    </td>

    <td align="center" style="width: 50%;">
      <img id="italymap" src="/immagini/italia.gif" width="372" height="418" usemap="#italia" alt="Mappa LUG" />
    </td>

    <td align="right" style="width: 15%;">
      <?php
        foreach ($elenco_regioni as $file => $nome) {
          if ($file == 'Italia') {$nome = 'Gruppi Nazionali';}
          if (in_array($file, array('emilia','friuli','trentino','valle'))) {continue;}
          echo '<a class="generalink" href="/'.$file.'/">'.$nome.'</a><br>';
        }
      ?>
    </td>

  </tr>
</table>

<br />

<map name="italia">
  <area shape="poly" coords="196,200,197,197,205,198,204,193,200,190,200,183,207,181,207,177,210,176,213,173,216,172,226,188,238,199,237,201,237,204,233,206,229,204,221,208,219,210,214,208,208,208,203,203,200,200,196,200" href="/abruzzo/" title="Abruzzo" alt="Abruzzo" />
  <area shape="poly" coords="266,271,270,262,265,255,264,252,259,245,263,241,264,236,273,236,273,240,279,244,284,249,290,248,291,250,290,253,297,262,293,266,283,268,282,274,278,275,272,271,266,271" href="/basilicata/" title="Basilicata" alt="Basilicata" />
  <area shape="poly" coords="286,270,292,269,292,275,289,278,289,282,291,285,297,285,306,293,308,304,310,306,306,313,301,311,292,317,294,327,289,334,284,336,282,345,280,348,268,350,265,345,265,341,267,336,270,334,273,328,270,322,274,318,281,317,281,312,278,311,276,299,270,288,266,277,269,274,278,279,285,275,286,270" href="/calabria/" title="Calabria" alt="Calabria" />
  <area shape="poly" coords="214,230,218,230,226,221,235,226,248,222,251,229,252,234,259,236,259,240,256,243,259,250,258,252,261,254,261,259,266,262,259,274,255,276,251,270,243,267,242,263,244,260,241,253,237,252,227,256,226,252,230,249,225,246,221,248,217,245,218,242,212,234,214,230" href="/campania/" title="Campania" alt="Campania" />
  <area shape="poly" coords="107,115,103,112,105,110,102,106,97,106,100,103,100,99,99,98,100,94,104,94,108,95,111,93,116,94,116,96,120,97,123,98,128,101,132,102,135,100,148,100,151,98,156,101,161,101,163,99,167,97,171,99,177,99,178,103,175,106,172,106,174,122,177,128,183,132,188,134,187,135,180,133,172,133,170,138,163,135,160,127,157,126,156,128,152,124,148,125,139,125,136,122,129,123,124,119,120,117,116,116,115,113,107,115" href="/emilia-romagna/" title="Emilia Romagna" alt="Emilia Romagna" />
  <area shape="poly" coords="183,64,181,60,183,54,180,51,188,39,200,41,206,43,206,46,203,49,200,51,198,52,204,54,206,57,205,59,204,61,206,62,207,64,206,67,207,70,212,75,213,79,207,79,207,75,202,76,202,69,197,68,193,64,188,65,186,65,183,64" href="/friuli-venezia-giulia/" title="Friuli Venezia Giulia" alt="Friuli Venezia Giulia" />
  <area shape="poly" coords="147,189,160,188,161,185,160,183,165,178,164,176,167,178,166,181,170,181,171,181,173,184,182,190,194,181,201,178,202,179,197,182,196,187,199,194,194,195,192,200,194,203,197,203,205,211,219,214,219,218,216,224,216,225,213,227,206,234,198,231,192,233,190,230,179,223,171,214,162,206,160,203,157,203,154,195,148,192,147,189" href="/lazio/" title="Lazio" alt="Lazio" />
  <area shape="poly" coords="49,138,45,134,47,131,47,129,58,125,61,126,65,124,65,118,69,113,76,112,79,109,80,109,82,111,85,110,87,107,89,107,91,110,101,110,99,114,100,117,104,116,110,123,114,128,110,126,108,127,109,130,107,131,102,127,100,124,97,123,95,120,93,119,93,121,89,119,82,116,78,115,78,118,74,119,66,129,56,136,49,138" href="/liguria/" title="Liguria" alt="Liguria" />
  <area shape="poly" coords="80,89,86,81,85,77,79,70,79,61,86,54,87,55,89,57,95,50,97,45,100,40,103,42,107,49,107,47,114,46,116,49,119,47,116,43,121,37,128,45,126,51,128,52,125,61,127,67,133,68,131,76,129,79,134,84,134,87,137,86,146,95,139,97,135,96,130,98,126,94,120,94,117,90,110,90,110,91,106,91,101,89,96,93,94,100,89,96,87,91,82,93,80,89" href="/lombardia/" title="Lombardia" alt="Lombardia" />
  <area shape="poly" coords="176,145,176,143,177,139,173,138,182,137,187,139,191,136,194,140,206,146,215,169,210,169,209,172,206,172,204,174,204,171,199,171,193,166,189,149,176,145" href="/marche/" title="Marche" alt="Marche" />
  <area shape="poly" coords="223,218,224,212,229,208,231,211,236,211,241,203,242,200,248,204,249,209,246,213,244,218,245,220,239,220,237,223,231,218,223,218" href="/molise/" title="Molise" alt="Molise" />
  <area shape="poly" coords="34,98,28,88,36,81,50,76,57,77,63,72,61,67,62,61,66,55,68,46,77,42,79,49,81,51,75,65,82,80,76,86,76,92,79,96,84,97,88,101,86,102,80,105,73,108,67,107,66,111,60,115,61,121,44,125,41,124,37,125,33,118,31,112,32,106,37,102,34,98" href="/piemonte/" title="Piemonte" alt="Piemonte" />
  <area shape="poly" coords="252,212,252,204,272,202,276,204,278,208,273,214,270,217,277,223,304,234,314,241,324,245,336,254,342,259,342,262,345,266,342,272,341,276,336,278,328,271,325,261,313,261,305,258,306,255,302,255,299,258,294,253,295,248,292,244,287,245,282,241,282,239,276,238,277,234,272,231,260,232,256,231,256,227,253,226,248,217,252,212" href="/puglia/" title="Puglia" alt="Puglia" />
  <area shape="poly" coords="61,310,61,309,59,304,61,303,60,290,63,285,63,276,63,269,65,262,60,253,55,251,57,247,56,244,58,241,61,233,64,233,62,236,62,239,66,241,73,239,81,232,86,228,91,230,96,234,99,238,98,240,101,244,100,248,103,256,99,262,98,267,99,271,97,291,96,298,97,300,95,303,95,305,88,304,83,302,80,304,80,308,73,313,69,313,67,315,64,311,62,309,61,310" href="/sardegna/" title="Sardegna" alt="Sardegna" />
  <area shape="poly" coords="175,345,182,340,187,344,190,343,192,339,198,337,201,340,202,342,206,341,210,346,215,346,219,343,223,344,232,343,245,338,250,341,255,336,257,337,264,334,265,335,264,341,260,348,256,356,255,360,252,363,252,368,256,372,255,375,259,381,255,385,253,388,254,392,251,395,249,393,233,390,229,383,226,380,216,379,206,373,200,372,196,366,190,364,187,362,179,363,173,356,174,351,175,345" href="/sicilia/" title="Sicilia" alt="Sicilia" />
  <area shape="poly" coords="117,176,118,174,121,174,126,174,126,177,123,178,119,178,117,176, 123,170,123,166,123,156,119,149,118,139,116,135,118,132,118,127,111,118,113,117,126,124,127,127,131,127,132,126,135,126,138,130,150,128,154,131,158,131,160,137,168,142,170,141,172,142,169,148,169,154,170,156,166,160,165,171,161,172,161,176,159,180,156,181,155,185,146,186,144,189,140,192,138,187,140,187,140,184,132,176,129,176,129,174,131,174,129,170,123,170" href="/toscana/" title="Toscana" alt="Toscana" />
  <area shape="poly" coords="131,64,129,58,132,54,133,45,130,40,125,38,125,34,132,29,139,31,147,26,155,23,161,24,167,22,171,24,171,26,176,35,172,35,167,36,166,39,162,40,161,44,162,49,163,51,157,58,152,58,149,61,147,61,144,69,141,69,140,64,135,62,131,64" href="/trentino-alto-adige/" title="Trentino Alto Adige" alt="Trentino Alto Adige" />
  <area shape="poly" coords="173,147,186,151,191,169,198,174,192,178,186,183,182,186,177,182,176,179,171,175,169,173,169,169,170,165,168,164,168,162,174,158,172,151,173,147" href="/umbria/" title="Umbria" alt="Umbria" />
  <area shape="poly" coords="37,75,37,70,35,65,38,58,42,58,46,59,58,57,57,60,58,68,57,73,52,72,47,72,42,75,37,75" href="/valle-daosta/" title="Valle d'Aosta" alt="Valle d'Aosta" />
  <area shape="poly" coords="136,79,136,69,144,73,149,69,149,65,155,61,158,64,168,54,167,49,165,44,170,42,170,39,176,41,180,38,180,37,185,39,184,41,181,43,178,47,175,49,176,53,178,55,177,58,178,62,179,63,179,66,186,69,189,68,191,68,195,72,198,73,198,75,192,77,187,79,185,82,176,83,174,90,177,96,167,94,162,96,153,96,145,90,139,84,136,79" href="/veneto/" title="Veneto" alt="Veneto" />
</map>

<?php
  lugfooter ();
?>
