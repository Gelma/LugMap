<?php
require_once ('varie.php');
?>
<html>
  <head>
    <link href="<?php echo $app_url . 'widget.css' ?>" rel="stylesheet" type="text/css">
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
      $lugs = file ('../../db/' . ($_GET ['region']) . '.txt', FILE_IGNORE_NEW_LINES);
      $regionname = $elenco_regioni [$_GET ['region']];

      if ($lugs == false || count ($lugs) == 0) {
        ?>

        <div class="error">
          <p>
            Non sembrano esserci LUG in <?php echo $regionname; ?>.
          </p>
          <p>
            <a href="http://www.badpenguin.org/italian-lug-howto" target="_blank">Creane uno!</a>
          </p>
        </div>

        <?php
      }
      else {
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
                <td><a href="<?php echo $data [3]; ?>" target="_blank"><?php echo $data [1]; ?></a></td>
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
