<?php

require_once ('varie.php');

# leggo il terzo livello per recuperare la regione richiesta
$livelli_del_dominio = explode('.', $_SERVER['HTTP_HOST']);
$regione_richiesta = $livelli_del_dominio[0];

if (array_key_exists ($regione_richiesta, $elenco_regioni)) {
	$regione = $elenco_regioni[$regione_richiesta];
	$db_regione = file ('./db/'.$regione_richiesta.'.txt');
	$title = 'LUG presenti nella regione ' . $regione;
} elseif ($regione_richiesta == "elenco") {
	$db_regione = array ();

	foreach (glob ('./db/*.txt') as $db_file)
		$db_regione = array_merge ($db_regione, file ($db_file));

	sort ($db_regione);

	$db_file = null;
	$regione = 'Italia';
	$title = 'LUG presenti in Italia';
} else { header("location: http://lugmap.it/"); }

lugheader ($title, $regione);
?>

<div id="center">
  <a id="backLugMapLink" href="http://lugmap.it/">&raquo; torna alla LUGmap</a>
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
			$campi         = explode("|",$linea);
			$provincia     = $campi[0];
			$denominazione = $campi[1];
			$zona          = $campi[2];
			$contatti      = $campi[3];
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

   <?php if ($db_file != null) { ?>
   <a id="csvLink" href="http://lugmap.it/db/<?php echo $db_file ?>.txt">&raquo; Elenco in formato CSV</a>
   <?php } else { ?>
   <br />
   <?php } ?>
</div>

<?php
	lugfooter ();
	exit();
?>
