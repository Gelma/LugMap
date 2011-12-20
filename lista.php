<?php

require_once ('varie.php');
do_head ('Lista completa dei LUG', array ('js/jquery.dataTables.min.js', 'js/lista.js'));

?>

<div>
	<?php

	$db_regione = array ();

	foreach ($elenco_regioni as $shortfile => $name) {
		$file = file ("./db/${shortfile}.txt");

		foreach ($file as &$row)
			$row = "$shortfile|$name|$row";

		$db_regione = array_merge ($db_regione, $file);
	}

	sort ($db_regione);

	?>

	<div class="description">
		<p>
			Ci sono <?php echo count ($db_regione); ?> Linux Users Groups in Italia.
		</p>

		<p>
			Probabilmente, almeno uno di questi è vicino a casa tua.
		</p>
	</div>

	<table id="lugListTable">
		<thead>
			<tr>
				<th>Regione</th>
				<th>Provincia</th>
				<th>Zona</th>
				<th>Denominazione</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th>Regione</th>
				<th>Provincia</th>
				<th>Zona</th>
				<th>Denominazione</th>
			</tr>
		</tfoot>

		<tbody>
			<?php

			$regione_riferimento = "";

			foreach ($db_regione as $linea):
				list ($shortregion, $regione, $provincia, $denominazione, $zona, $contatti, $mail) = explode("|",$linea);
				?>

				<tr>
					<td class="region"><a href="http://<?php echo $shortregion ?>.lugmap.it/"><?php echo $regione ?></a></td>
					<td class="province"><?php echo $provincia ?></td>
					<td class="zone"><a href="http://lugmap.it/?zoom=<?php echo $denominazione ?>"><?php echo $zona ?></a></td>
					<td class="contactUrl"><a href="<?php echo $contatti?>"><?php echo $denominazione ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php
do_foot ();
?>
