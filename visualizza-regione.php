<?php

require_once ('varie.php');

# leggo il terzo livello per recuperare la regione richiesta
$livelli_del_dominio = explode('.', $_SERVER['HTTP_HOST']);
$regione_richiesta = $livelli_del_dominio[0];

if ($regione_richiesta == 'italia') {
	$db_file = 'Italia.txt';
	$db_regione = file ("./db/$db_file");
	$title = 'LUG di livello nazionale';
} elseif (array_key_exists ($regione_richiesta, $elenco_regioni)) {
	$regione = $elenco_regioni[$regione_richiesta];
	$db_file = "$regione_richiesta.txt";
	$db_regione = file ("./db/$db_file");
	$title = 'LUG presenti nella regione ' . $regione;
} elseif ($regione_richiesta == "elenco") {
	$db_regione = array ();

	foreach (glob ('./db/*.txt') as $db_file)
		$db_regione = array_merge ($db_regione, file ($db_file));

	sort ($db_regione);

	$db_file = null;
	$regione = 'Italia';
	$title = 'LUG presenti in Italia';
} else {
	header("location: http://lugmap.it/");
}

do_head ($title);

?>

<h1><?php echo $title; ?></h1>

<div id="center">
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
          <th>Provincia</th>
          <th>Denominazione</th>
          <th>Zona</th>
          <th>Sito</th>
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
   <a id="csvLink" href="http://github.com/Gelma/LugMap/tree/master/db/<?php echo $db_file ?>">&raquo; Elenco originale in formato CSV</a>
   <?php } else { ?>
   <br />
   <?php } ?>
</div>

<?php do_foot (); ?>
