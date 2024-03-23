<?php

defined('_JEXEC') or die('Restricted access');

/**
 * Model für die View, welche alle Kategorien mit den aktiven Ämtli auflistet.
 */
class ZeitbankModelArbeitsliste extends JModelLegacy
{
    /**
     * Stellt die Liste aller Ämtli für die Endbenutzer dar.
     */
    public function getArbeitsliste()
    {
        $db = JFactory::getDBO();

        $laufendes_jahr = intval(date('Y'));
        $lastYear = $laufendes_jahr - 1;

        $output = "";

        $query = "SELECT bezeichnung, id FROM #__mgh_zb_kategorie ORDER BY ordering";
        $db->setQuery($query);
        $kategorien = $db->loadObjectList();

        if ($db->getAffectedRows() > 0) {
            foreach ($kategorien as $kat) {
                $query = "SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='" . $kat->id . "' AND aktiviert='1' ORDER BY ordering";
                $db->setQuery($query);
                $arbeiten = $db->loadObjectList();
                $k = 0;  // Zebra start

                if ($db->getAffectedRows() > 0) {
                    $output .= "<h3>Kategorie: " . $kat->bezeichnung . "</h3>";
                    $output .= "<table class=\"zeitbank\" style=\"width: 950px;\">";
                    $output .= "<tr class=\"head\">
                                      <th width=\"300\">Kurztext</th>
                                      <th width=\"150\">Zuständig</th>
                                                        <th width=\"70\" align=\"right\">Budget</th>
                                                        <th width=\"100\" align=\"right\">Buchungen " . $laufendes_jahr . "</th>
                                                        <th width=\"100\" align=\"right\">Buchungen " . $lastYear . "</th>
                                                        <th width=\"70\" align=\"right\">Pauschale</th>
                                                    </tr>";

                    foreach ($arbeiten as $ab) {
                        $style = $k ? "even" : "odd"; // Zebramuster
                        $output .= "<tr class=\"" . $style . "\"><td><strong>" . $ab->kurztext . "</strong></td>";
                        $output .= "<td>" . JFactory::getUser($ab->admin_id)->name . "</td>";
                        $output .= "<td align=\"right\">" . $ab->jahressoll . " h</td>";
                        $output .= "<td align=\"right\">" . round($this->saldo($ab->id, $laufendes_jahr) / 60, 0) . " h</td>";
                        $output .= "<td align=\"right\">" . round($this->saldo($ab->id, $lastYear) / 60, 0) . " h</td>";
                        $output .= "<td align=\"right\">" . ($ab->pauschale > 0 ? $ab->pauschale . ' min' : '-') . "</td>";
                        $output .= "</tr>";

                        $k = 1 - $k;
                    }
                    $output .= "</table><br />";
                }
            }
        }

        return $output;
    }

    private function saldo($id, $jahr)
    {
        $db = JFactory::getDBO();

        $query = "SELECT COALESCE(sum(minuten),0) minuten FROM #__mgh_zb_journal
                  WHERE datum_quittung is not null
                    AND (datum_antrag BETWEEN '" . $jahr . "-01-01' AND '" . $jahr . "-12-31')
                    AND admin_del = 0
                    AND arbeit_id = " . $id;

        $db->setQuery($query);
        return $db->loadResult();
    }

}
