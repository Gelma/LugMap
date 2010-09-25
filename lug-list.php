<?php
error_reporting(E_ALL);
include_once 'config.php';

# parsing della richiesta per individuare 
if ( isset($_REQUEST["reg"]) && array_key_exists( $_REQUEST["reg"], $elenco_regioni) ) {
	$regione = $elenco_regioni[$_REQUEST["reg"]];
	$db_file = $_REQUEST["reg"];
}else{
	header("location: http://www.linux.it/LUG/");
}

$db_regione = file('./db/'.$db_file.'.txt');
$colore='bgcolor="#ecfb86"';

include_once 'lugMapListView.php';

?>
