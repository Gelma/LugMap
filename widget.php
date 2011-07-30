<?php

require_once ('varie.php');
do_head ();

?>

<div class="description">
	<p>
		Usando il generatore qui sotto puoi ottenere il codice HTML di un semplice widget web da
		copiare ed incollare sul tuo sito, con l'elenco sempre automaticamente aggiornato dei Linux
		Users Groups della regione selezionata.
	</p>

	<p>
		Per tutti coloro che gestiscono delle pagine web, sia veterani del mondo Linux che semplici simpatizzanti,
		questo è un ottimo modo per fare pubblicità ai gruppi vicini di casa.
	</p>
</div>

<fieldset style="width: 45%; float: left;">
	<legend>Opzioni</legend>

	<p>
		<label for="region">Regione</label>

		<select name="region">
			<?php
			foreach ($elenco_regioni as $simple => $name) {
			?>

			<option value="<?php echo $simple; ?>"><?php echo $name; ?></option>

			<?php
			}
			?>

			<option value="all">Tutti i LUG</option>
		</select>
	</p>

	<p>
		<label for="head">Mostra Header</label>
		<input name="head" type="checkbox" checked="yes" />
	</p>

	<p>
		<label for="head_color">Colore Header</label>
		<input name="head_color" type="color" value="#000080" data-text="hidden" data-hex="true" style="height: 15px; width: 20px;" />
	</p>

	<p>
		<label for="foot">Mostra Footer</label>
		<input name="foot" type="checkbox" checked="yes" />
	</p>
</fieldset>

<div style="width: 45%; float: right; text-align: center;">
	<div class="preview">
		<iframe id="lugmap" src="http://lugmap.it/forge/lug-o-matic/widget.php?region=abruzzo&amp;html=true" onLoad="calcSize();" width="200px" scrolling="no" frameborder="0"></iframe>
	</div>

	<br />
	<br />
	<br />

	<p>Copia e incolla questo codice nella tua pagina web!</p>

	<textarea class="code" cols="45" rows="10"><?php echo htmlentities (
	'<script type="text/javascript" src="http://lugmap.it/forge/lug-o-matic/widget.php?region=abruzzo"></script>
	<img id="lugmap" src="http://lugmap.it/forge/lug-o-matic/placeholder.png" onload="renderLugMap();" />'); ?>
	</textarea>
</div>

<?php do_foot (); ?>
