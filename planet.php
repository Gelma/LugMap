<?php

require_once ('varie.php');
do_head ('Notizie dai LUG Italiani');

?>

<div>
	<div class="description">
		<p>
			Qui sono aggregate le notizie, gli annunci, gli eventi dai LUG indicizzati nella LugMap.
		</p>
		<p>
			Per maggiori informazioni su questa pagina, <a href="<?php echo $main_url ?>/planet_info.php">guarda qui</a>.
		</p>
	</div>

	<?php include ('forge/opml-to-planet/index.html') ?>
</div>

<?php
do_foot ();
?>
