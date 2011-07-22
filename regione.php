<?php

require_once ('funzioni.php');

# parsing della richiesta per individuare
if (isset ($_REQUEST["reg"])) {
  if (array_key_exists ($_REQUEST["reg"], $elenco_regioni)) {
    $regione = $elenco_regioni[$_REQUEST["reg"]];
    $db_file = $_REQUEST["reg"];
    $db_regione = file ('./db/'.$db_file.'.txt');
    $title = 'LUG presenti nella regione ' . $regione;
  }
  else {
    header("location: http://lugmap.linux.it/");
  }
}
else {
  $db_regione = array ();

  foreach (glob ('./db/*.txt') as $db_file)
    $db_regione = array_merge ($db_regione, file ($db_file));

  sort ($db_regione);

  $db_file = null;
  $regione = 'Italia';
  $title = 'LUG presenti in Italia';
}

lugheader ($title, $regione);

?>

<div id="center">
  <a id="backLugMapLink" href="/">&raquo; torna alla LUGmap</a>
  <table id="lugListTable">
    <thead>
        <tr>
          <th>Provincia</th>
          <th>Denominazione</th>
          <th>Zona</th>
          <th>Sito</th>
        </tr>
     </thead>
     <tfoot>
      <tr>
        <td colspan="4"></td>
        </tr>
    </tfoot>
    <tbody>
      <?php while (list ($nriga, $linea) = each ($db_regione)): ?>
        <?php # estrazione variabili
            $campi = explode("|",$linea);
            $provincia    = $campi[0];
            $denominazione  = $campi[1];
            $zona     = $campi[2];
            $sito   = $campi[3];
        # stampa dei campi ?>
        <tr class="row_<?php echo ($nriga % 2); ?>">
         <td class="province"><?php echo $provincia ?></td>
         <td><?php echo $denominazione ?></td>
         <td><?php echo $zona ?></td>
         <td class="contactUrl"><a href="<?php echo $sito ?>"><?php echo $sito ?></a></td>
        </tr>
      <?php endwhile;?>
    </tbody>
   </table>

   <?php if ($db_file != null) { ?>
   <a id="csvLink" href="db/<?php echo $db_file ?>.txt">&raquo; Elenco in formato CSV</a>
   <?php ultimo_aggiornamento(); ?>
   <?php } else { ?>
   <br />
   <?php } ?>
</div>

<?php
  lugfooter ();
?>
