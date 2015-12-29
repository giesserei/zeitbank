<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

?>

<div class="component">

    <?php

    echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId . '">Zurück zur Übersicht</a><p/>';

    echo "<h1>Zeitbank: Liste der bestätigten Ämtli-Buchungen der letzten " . $this->getTage() . " Tage</h1>";

    echo '<table class="zeitbank">';
    echo '<tr class="head">
                <th>Datum</th>
            <th>übergeben an</th>
            <th>Arbeitsgattung</th>
            <th style="text-align:right">Zeit<br />[min]</th>
            <th style="text-align:right">B-Nr.</th>
          </tr>';

    $k = 0;
    foreach ($this->quittungsliste as $jn) {
        $style = $k ? "zb_even" : "zb_odd";
        echo '<tr class="' . $style . '">
                  <td>' . ZeitbankFrontendHelper::getLinkBuchung($jn->id, JHTML::date($jn->datum_antrag, 'd.m.Y')) . '</td>
              <td>' . $jn->konto_gutschrift . '</td>
              <td>' . $jn->kurztext . '</td>
              <td style="text-align:right">' . $jn->minuten . '</td>
                <td style="text-align:right">' . $jn->id . '</td>
            </tr>';
        $k = 1 - $k;
    }
    echo "</table>";
    ?>

</div>
