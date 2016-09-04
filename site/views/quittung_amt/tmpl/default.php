<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
?>

<div class="component">

    <?php

    echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId . '">Zurück zur Übersicht</a><p/>';

    echo '<h1 class="zeitbank">Zeitbank: Quittierung der Stunden der von dir verwalteten Ämtli</h1>';

    // Offene Quittungen ausgeben (Bestätigung dass Stunden beim aktuellen User ab, beim Antragsteller eingebucht werden)
    if (!empty($this->quittierungen)) {
        echo '<table class="zeitbank" >';
        echo '<tr class="head">
                  <th>Datum</th>
          <th>Antrag von</th>
          <th>Arbeitsgattung</th>
          <th style="text-align:right;">Zeit<br/>[min]</th>
          <th width="250">Kommentar</th>
          <th style="text-align:right">B-Nr.</th>
          <th>&nbsp;</th>
        </tr>';

        $k = 0;
        foreach ($this->quittierungen as $qt) {
            $style = $k ? "zb_even" : "zb_odd";

            echo '<tr class="' . $style . '">
                      <td>' . JHTML::date($qt->datum_antrag, 'd.m.Y') . '</td>
            <td>' . ZeitbankFrontendHelper::getEmailLink($qt->vorname, $qt->nachname, $qt->email,
                    'Zeitbank / Dein Antrag / ' . ZeitbankFrontendHelper::cropText($qt->kurztext, 75)) . '</td>
            <td>' . $qt->kurztext . '</td>
            <td style="text-align:right;">' . $qt->minuten . '</td>
            <td>' . ZeitbankFrontendHelper::cropText($qt->text, 30, true) . '</td>
            <td style="text-align:right">' . $qt->id . '</td>
            <td>
                <input type="button" value="bestätigen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=quittung.edit&id=' . $qt->id . '&Itemid=' . $this->menuId . '\'">
                <input type="button" value="ablehnen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=ablehnung.edit&id=' . $qt->id . '&Itemid=' . $this->menuId . '\'">
            </td>
          </tr>';
            if ($qt->abgelehnt == 1) {
                echo '<tr class="' . $style . '">
                        <td colspan="7" style="color:red">Du hast den Antrag abgelehnt: ' . $qt->kommentar_ablehnung . '</td>
                      </tr>';
            }
            $k = 1 - $k;
        }
        echo '</table>';

        echo '
        <div style="margin-top:35px">
          <strong>Hinweis:</strong>
          <ul>
            <li>Ist die Buchung aus deiner Sicht fehlerhaft, so kannst du den Antrag ablehnen oder den/die AntragstellerIn persönlich informieren.
                <br/>Ein Klick auf den Namen öffnet dein E-Mail Programm (sofern eine E-Mail Adresse bekannt ist).</li>
          </ul>
        </div>';
    } else {
        echo "Keine offenen Quittierungen";
    }
    ?>
</div>
