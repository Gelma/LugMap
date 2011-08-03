<?php
# parsing della richiesta della regione, che può arrivare da 4 tipi di url:
# lug-list.phtml?reg=nome - formato storico   (vogliamo che dia un redirect al definitivo)
# regione.php?reg=nome  - formato di transizione (idem)
# /regione/nome-regione - formato di transizione (idem)
# /nome-regione/        - definitivo

if (ereg('index\.php$', $_SERVER["SCRIPT_NAME"])) { # se sono nel file index.php, allora sono stato invocato da /nome-regione/
  require_once ('../funzioni.php');
  $regione = substr(dirname($_SERVER["SCRIPT_NAME"]), 1); # estraggo la regione dal percorso
  if (array_key_exists ($regione, $elenco_regioni)) { # lasciamo il controllo, ma in ogni caso dovremmo ottenere un 404
    $db_file = '../db/'.$regione.'.txt';
    $db_regione = file ($db_file);
    $title = 'LUG presenti nella regione '. $elenco_regioni[$regione];
  } else {
            header("location: http://lugmap.linux.it/"); }

} else { # qui se sono stato invocato alla vecchia maniera
  require_once ('funzioni.php');
  if (isset ($_REQUEST["reg"])) {
    if (array_key_exists ($_REQUEST["reg"], $elenco_regioni)) { # lasciamo il controllo, ma probabilmente non serve più
      switch ($_REQUEST["reg"]) {
          case "emilia":
            $regione_da_reindirizzare = "emilia-romagna";
            break;
          case "friuli":
            $regione_da_reindirizzare = "friuli-venezia-giulia";
            break;
          case "trentino":
            $regione_da_reindirizzare = "trentino-alto-adige";
            break;
          case "valle":
            $regione_da_reindirizzare = "valle-daosta";
            break;
          default:
            $regione_da_reindirizzare = $_REQUEST["reg"];
      }
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: http://lugmap.linux.it/".$regione_da_reindirizzare."/");
      exit();
    } else { header("location: http://lugmap.linux.it/"); }
  } else {
    $db_regione = array ();

    foreach (glob ('./db/*.txt') as $db_file)
      $db_regione = array_merge ($db_regione, file ($db_file));

    sort ($db_regione);

    $db_file = null;
    $regione = 'Italia';
    $title = 'I LUG italiani';
  }
}

lugheader ($title);

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
      <a id="csvLink" href="<?php echo $db_file ?>">&raquo; Elenco in formato CSV</a>
   <?php } else { ?>
   <br />
   <?php } ?>
   <?php ultimo_aggiornamento(); ?>
</div>

<?php
  lugfooter ();
?>
