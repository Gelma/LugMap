<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2010-2016  Italian Linux Society - http://www.linux.it

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.*/
?>
<?php

require_once ('utils.php');
require_once ('../funzioni.php');

if (isset ($_GET ['region'])) {
  require_once ('widget.php');
  exit (0);
}

lugheader ('LUG-o-matic', array ('generator.css'), array ('generator.js', 'http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js'));

?>

<div class="edit_box">
	<fieldset style="width: 45%; float: left;">
		<legend>Opzioni</legend>

		<p>
			<label for="region">Regione</label>

			<select name="region">
				<?php
				foreach ($elenco_regioni as $simple => $name) {
				        if (in_array($simple, array('emilia','friuli','trentino','valle'))) {continue;}
					if ($region == $simple)
						$selected = ' selected="selected"';
					else
						$selected = '';

					?>

					<option value="<?php echo $simple ?>"<?php echo $selected ?>><?php echo $name; ?></option>

					<?php
				}
				?>

				<option value="all">Tutti i LUG</option>
			</select>
		</p>

		<br />

		<p>
			<label for="width">Larghezza</label>
			<input name="width" type="text" value="200" size="4" />px
		</p>

		<p>
			<label for="head">Mostra Header</label>
			<input name="head" type="checkbox" checked="yes" />
		</p>

		<p class="depends_on_header">
			<label for="head_color">Colore Header</label>
			<input name="head_color" type="color" value="#FFA200" data-text="hidden" data-hex="true" style="height: 15px; width: 20px;" />
		</p>

		<p class="depends_on_header">
			<label for="head_text_color">Colore Testo Header</label>
			<input name="head_text_color" type="color" value="#FFFFFF" data-text="hidden" data-hex="true" style="height: 15px; width: 20px;" />
		</p>

		<p>
			<label for="foot">Mostra Footer</label>
			<input name="foot" type="checkbox" checked="yes" />
		</p>

		<br />

		<p>
			<p>Questa opzione torna un'immagine statica anzich&eacute; del codice JavaScript: utile per poter
			inserire il widget all'interno di siti che non consentono l'uso di codice HTML complesso,
			come <a class="generalink" href="http://wordpress.com/">Wordpress.com</a>.</p>

			<label for="image">Genera Immagine</label>
			<input name="image" type="checkbox" />
		</p>
	</fieldset>

	<div style="width: 45%; float: right; text-align: center;">
		<div class="preview">
			<iframe id="lugmap" src="<?php echo $app_url ?>widget.php?region=abruzzo&amp;format=html" onLoad="calcSize();" width="210px" scrolling="no" frameborder="0"></iframe>
		</div>

		<br />
		<br />
		<br />

		<p>Copia e incolla questo codice nella tua pagina web!</p>

		<textarea class="code" cols="45" rows="10"><?php echo htmlentities (
		'<script type="text/javascript" src="' . $app_url .'widget.php?region=abruzzo"></script>
		<img id="lugmap" src="'. $app_url . 'placeholder.png" onload="renderLugMap();" />') ?>
		</textarea>
	</div>
</div>

<div class="clear_spacer"></div>

<?php
  lugfooter ();
?>
