<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2013  Italian Linux Society - http://www.linux.it

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

if ($_GET ['save'] == 1) {
	file_put_contents ('radar.txt', $_POST ['contents']);
}

?>

<html>
	<body>
		<form method="POST" action="?save=1">
			<textarea name="contents"><?php echo file_get_contents ('radar.txt') ?></textarea>
			<br />
			<input value="Salva" type="submit" />
		</form>
	</body>
</html>
