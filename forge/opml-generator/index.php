<?php

require_once ('../../utils.php');
lugheader ('OPML Generator', 'OPML, RSS');

?>

<div id="introTabel">
  <p>
    Il generatore <a href="http://www.opml.org/">OPML</a> della LugMap permette di ricostruire la lista dei feeds
    <a href="http://it.wikipedia.org/wiki/Really_simple_syndication">RSS</a> dei siti dei LUG indicizzati nella mappa.
    Esso ispeziona i vari files delle regioni, per ogni URL indicato scarica l'HTML della homepage del sito e verifica
    l'esistenza del tag <i>&lt;link rel="alternate"&gt;</i> abitualmente utilizzato per l'auto-discovery.
  </p>

  <p>
    Tale file OPML può poi essere importato nel proprio lettore RSS, se si vogliono leggere tutte le notizie riguardanti
    l'esteso e variegato mondo degli User Groups, oppure essere utilizzato come punto di partenza per nuove applicazioni
    che prevedono l'aggregazione di contenuti a tema prettamente "linuxofilo".
  </p>

  <p>
    Lo script è in PHP, e può essere lanciato dalla linea di comando con <i>php find_feeds.php</i>
  </p>

  <p>
    <a href="find_feeds.php.gz">Scarica lo script qui!</a>
  </p>
</div>

<?php
  lugfooter ();
?>
