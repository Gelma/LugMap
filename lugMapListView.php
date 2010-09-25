<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="language" content="italian">
  <meta name="keywords" content="Linux, GNU/Linux, software libero, freesoftware, LUG, Linux User Group, $regione">
  <link href="/community/linuxit2.css" rel="stylesheet" type="text/css">
  <title>LUG presenti nella regione $regione</title>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#FF0000" vlink="#AA0000" alink="#00AA00">
  <h2 align=center>Linux User Groups in $regione</h2>
  <div align="right">&raquo; <a href="index.phtml">torna alla LUGmap</a></div>
  <br>
  <table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="black">
    <tr>
      <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#00659c">
      <tr class="menubox">
        <th>Denominazione</th>
        <th>Area di attivit&agrave;</th>
        <th>Homepage</th>
      </tr>
      <?php 
      # recupero e stampa dei dati dal file della regione
      # print '<table>'; # la table in realta' è gia' aperta, vedi header
		  while (list ($nriga,$linea) = each ($db_regione)) {
			  if ($colore == 'bgcolor="#dddddd"') {$colore='bgcolor="#eeeeee"';} else {$colore='bgcolor="#dddddd"';}
			  # estrazione variabili
			  $campi = explode("|",$linea);
			  $provincia    = $campi[0];
			  $denominazione  = $campi[1];
			  $zona     = $campi[2];
			  $contatti   = $campi[3]; # al momento sara' solo il sito, ma l'idea sarebbe quella di avere anche delle informazioni più dirette
			  # stampa dei campi
			  print "<tr $colore><td align=\"center\">$denominazione</td><td align=\"center\">$zona</td><td align=\"center\"><a href=\"$contatti\">$contatti</a></td>";
		  }
    ?>
      </table>
   </table>
   <p align="right">&raquo; <a href="index.phtml">torna alla LUGmap</a></p>
   <hr size="1" noshade>
</body>
</html>