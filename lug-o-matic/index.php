<?php

require_once ('utils.php');
require_once ('../funzioni.php');

if (isset ($_GET ['region'])) {
  require_once ('widget.php');
  exit (0);
}

lugheader ('LUG-o-matic', 'LUG-o-matic', array ('generator.css'), array ('jquery.js', 'generator.js'));

?>

<div id="introTabel">
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

    <textarea class="code" cols="45" rows="15"><?php echo htmlentities (
'<script language="JavaScript"><!--
function calcSize () { document.getElementById(\'lugmap\').height = document.getElementById(\'lugmap\').contentWindow.document.body.scrollHeight;
}
//--></script>
<iframe id="lugmap" src="' . $app_url . '?region=abruzzo"
onLoad="calcSize();" width="200px" scrolling="no" frameborder="0"></iframe>'); ?>
    </textarea>
  </div>

  <div>
    <p>
      Vuoi dare una mano nel promuovere le attività pro-freesoftware della tua zona?
    </p>

    <p>
      Hai un sito web?
    </p>

    <p class="important">
      Usa lug-o-matic!
    </p>

    <p>
      Usando il generatore qui accanto puoi ottenere il codice HTML di un semplice widget web da
      copiare ed incollare sul tuo sito, con l'elenco sempre automaticamente aggiornato dei Linux
      Users Group della regione selezionata.
    </p>
  </div>

  <div class="clear_spacer"></div>
</div>

<?php
  lugfooter ();
?>
