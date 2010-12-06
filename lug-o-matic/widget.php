<?php
	require_once ('utils.php');
?>

<html>
	<head>
		<style type="text/css">
		<!--
			body {
				font-family: Helvetica;
				font-size: 12px;
				margin: 0px;
				padding: 0px;
				border: 3px solid #000000;
			}

			a {
				text-decoration: none;
			}

			.error {
				margin: 5px;
				padding: 3px;
				background-color: #F54B4B;
				text-align: center;
			}

			.title {
				font-weight: bold;
				background-color: #000080;
				color: #FFFFFF;
				border: 1px solid black;
				font-size: 12px;
				padding: 5px;
				text-align: center;
			}

			.list {
				border-collapse: collapse;
				margin: auto;
				padding: 10px;
				width: 100%;
			}

			.list tr > td:first-child {
				font-weight: bold;
			}

			.list tr.row_0 td {
				background-color: #EEEEEE;
			}

			.list tr.row_1 td {
				background-color: #DDDDDD;
			}

			.list td {
				border: 1px solid black;
				font-size: 12px;
				padding: 5px;
				text-align: center;
			}

			.link {
				margin-top: 5px;
				text-align: center;
				font-style: italic;
				color: #000000;
				font-weight: bold;
			}

			.link a {
				color: #FF0000;
				text-decoration: none;
			}
		-->
		</style>
	</head>

	<body>
		<?php

		if (!isset ($_GET ['region']) || in_array ($_GET ['region'], array_keys ($elenco_regioni)) == false) {
			?>

			<div class="error">
				<p>
					Oops, non hai specificato alcuna regione valida.
				</p>
			</div>

			<?php
		}
		else {
			/*
				Attualmente e' implementato il caching dei files raw, si potrebbero usare
				direttamente quelli della LugMap (se il widget viene hostato insieme ad essa)
			*/
			$cachepath = 'db/' . ($_GET ['region']) . '.txt';
			if (file_exists ($cachepath) == false || filemtime ($cachepath) < (time () - (60 * 60 * 24))) {
				$contents = file_get_contents ('http://github.com/Gelma/LugMap/raw/master/db/' . ($_GET ['region']) . '.txt');
				file_put_contents ($cachepath, $contents);
			}

			$lugs = file ($cachepath, FILE_IGNORE_NEW_LINES);

			if ($lugs == false || count ($lugs) == 0) {
				?>

				<div class="error">
					<p>
						Oops, elenco dei LUG non trovato online.
					</p>
					<p>
						Sicuro di aver specificato una regione corretta?
					</p>
				</div>

				<?php
			}
			else {
				$regionname = $elenco_regioni [$_GET ['region']];

				?>

				<div class="title">
					<p>
						Cerchi un Linux User Group in <?php echo $regionname; ?>?
					</p>
				</div>

				<table class="list">

					<?php
						$nriga = 0;

						while (list ($nriga, $lug) = each ($lugs)) {
							$data = explode ('|', $lug);

							?>

							<tr class="row_<?php echo ($nriga % 2); ?>">
								<td><?php echo $data [0]; ?></td>
								<td><a href="<?php echo $data [3]; ?>"><?php echo $data [1]; ?></a></td>
							</tr>

							<?php
						}

					?>

				</table>

			<?php

			}
		}

		?>

		<div class="link">
			Powered by <a href="<?php echo $app_url; ?>">lug-o-matic</a>
		</div>
	</body>
</html>
