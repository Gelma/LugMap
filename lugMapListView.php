<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="italian" />
  <link href="assets/css/main.css" rel="stylesheet" type="text/css" />
  <title>LUG presenti nella regione <?php echo $regione ?></title>
</head>
<body>

<div id="header">
  <img src="ilsheader-wide.png" alt="Italian Linux Society" />
  <h2 id="title">Linux Users Groups della regione <?php echo $regione ?></h2>
</div>

<div id="center">
  <a id="backLugMapLink" href="index.phtml">&raquo; torna alla LUGmap</a>
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
		  <?php while (list ($nriga, $linea) = each ($db_regione)): ?>
			  <?php # estrazione variabili
					  $campi = explode("|",$linea);
					  $provincia    = $campi[0];
					  $denominazione  = $campi[1];
					  $zona     = $campi[2];
					  $contatti   = $campi[3]; 
			  # stampa dei campi ?>
			  <tr class="row_<?php echo ($nriga % 2); ?>">
			   <td class="province"><?php echo $provincia ?></td>
			   <td><?php echo $denominazione ?></td>
			   <td><?php echo $zona ?></td>
			   <td class="contactUrl"><a href="<?php echo $contatti?>"><?php echo $contatti ?></a></td>
			  </tr>
		  <?php endwhile;?>
    </tbody>
   </table>
   <a id="csvLink" href="db/<?php echo $db_file ?>.txt">&raquo; Elenco in formato CSV</a>
</div>

<div id="footer">
  <table width="70%" align="center">
	<tr><td>
		<p class="helpMessage">Aiutaci a mantenere la LugMap aggiornata!</p>
		<p class="helpMessage">
		Segnalaci nuovi gruppi, cos&igrave; come errori ed omissioni, scrivendo
		alla <a class="generalink" href="http://lists.linux.it/listinfo/lugmap">mailing list pubblica</a>,
		oppure contattando direttamente Andrea Gelmini (telefonicamente al
		<a class="generalink" href="tel:328-72-96-628">328-72-96-628</a>, via
		<a class="generalink" href="mailto:andrea.gelmini@lugbs.linux.it">mail</a>, o attraverso
		<a class="generalink" href="http://www.facebook.com/andrea.gelmini">Facebook</a>.)<br>
		Puoi partecipare direttamente, sia alla stesura del codice che del database, sfruttando il
		<a class="generalink" href="http://github.com/Gelma/LugMap">repository GitHub</a>.
		Per saperne di pi&ugrave; &egrave; disponibile la
		<a class="generalink" href="http://github.com/Gelma/LugMap/raw/docs/Guida_Intergalattica_Alla_LugMap.pdf">
		Guida Intergalattica alla LugMap</a>.
		<p class="helpMessage">
		Te ne saremo eternamente grati!
	  </td></tr>
  </table>
</div>

</body>
</html>
