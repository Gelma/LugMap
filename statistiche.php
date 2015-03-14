<?php
/*Codice della mappa dei LUG italiani
  Copyright (C) 2010-2015  Italian Linux Society - http://www.linux.it

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

require_once ('funzioni.php');

function do_row ($nriga, $region, $tot, $perc) {
  global $elenco_regioni;

  ?>

  <tr class="row_<?php echo ($nriga % 2); ?>">
    <td class="province"><a href="/<?php echo $region; ?>/"><?php echo $elenco_regioni [$region]; ?></a></td>
    <td><?php echo $tot; ?></td>
    <td><?php echo round ($perc, 1); ?></td>
  </tr>

  <?php
}

lugheader ('Statistiche dei LUG italiani');

?>

<div id="center">
  <a id="backLugMapLink" href="/">&raquo; torna alla LUGmap</a>
  
  <div id="infographs">
    <p>
      Distribuzione geografica degli iscritti alla <a href="http://www.ils.org/newsletter">newsletter ILS</a> (a sinistra) e dei LUG (a destra), divisi per provincia.
    </p>
    <div>
      <img src="../immagini/newsletter.svg" />
    </div>
    <div>
      <img src="../immagini/lugs.svg" />
    </div>
  </div>
  
  <table id="lugListTable">
    <thead>
        <tr>
          <th>Regione</th>
          <th>Numero di LUG</th>
          <th>Percentuale sul Totale</th>
        </tr>
     </thead>
     <tfoot>
      <tr>
        <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
      <?php

        $tots = array ();
        $tot_ita = 0;
        $sum = 0;

        foreach (glob ('db/*.txt') as $filename) {
          if (in_array($filename, array('db/emilia.txt', 'db/friuli.txt', 'db/trentino.txt', 'db/valle.txt')))
            continue;
          $contents = file ($filename, FILE_IGNORE_NEW_LINES);
          $tot = count ($contents);
          $sum += $tot;

          if ($filename != 'db/Italia.txt')
            $tots [] = $tot;
          else
            $tot_ita = $tot;
        }

        /*
          I riferimenti ai LUG nazionali lo metto in cima e lo salto nell'iterazione dopo
        */
        do_row (0, 'Italia', $tot_ita, $tot_ita == 0 ? 0 : ($tot_ita * 100) / $sum);

        $nriga = 1;

        foreach (glob ("db/*.txt") as $filename) {
          if (in_array($filename, array('db/Italia.txt', 'db/emilia.txt', 'db/friuli.txt', 'db/trentino.txt', 'db/valle.txt')))
            continue;

          $t = $tots [$nriga - 1];
          list ($name) = explode ('.', basename ($filename));
          do_row ($nriga, $name, $t, $t == 0 ? 0 : ($t * 100) / $sum);
          $nriga++;
        }

      ?>

      <tr class="row_special">
        <td class="province">Totale</td>
        <td><?php echo $sum; ?></td>
        <td>100</td>
      </tr>
    </tbody>
   </table>
   <br />
</div>

<?php
  lugfooter ();
?>
