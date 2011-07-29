<?php

require_once ('varie.php');
do_head ();

?>

<div>
	<?php

	$db_regione = array ();

	foreach (glob ('./db/*.txt') as $db_file) {
		$db_regione = array_merge ($db_regione, file ($db_file));
		sort ($db_regione);
	}

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
				<th>Provincia</th>
				<th>Denominazione</th>
				<th>Zona</th>
				<th>Contatti</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th>Provincia</th>
				<th>Denominazione</th>
				<th>Zona</th>
				<th>Contatti</th>
			</tr>
		</tfoot>

		<tbody>
			<?php

			while (list ($nriga, $linea) = each ($db_regione)):
				$campi = explode("|",$linea);
				$provincia    = $campi[0];
				$denominazione  = $campi[1];
				$zona     = $campi[2];
				$contatti   = $campi[3];
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
</div>

<?php
do_foot ();
?>
