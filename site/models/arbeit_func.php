<?php

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . '/components/com_zeitbank/models/zeitbank.php');


/**
 * Stellt die Liste aller Ämtli für die Endbenutzer dar
 */
function get_arbeitsliste_enduser()
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
                $output .= "<table class=\"zeitbank\">";
                $output .= "<tr class=\"head\">
	    		              <th width=\"300\">Kurztext</th>
	    		              <th width=\"150\">Zuständig</th>
	    					        <th width=\"70\" align=\"right\">Jahressoll</th>
	    					        <th width=\"100\" align=\"right\">Buchungen " . $laufendes_jahr . "</th>
	    					        <th width=\"100\" align=\"right\">Buchungen " . $lastYear . "</th>
	    					        <th width=\"70\" align=\"right\">Pauschale</th>
	    					    </tr>";

                foreach ($arbeiten as $ab) {
                    $style = $k ? "even" : "odd"; // Zebramuster
                    $output .= "<tr class=\"" . $style . "\"><td><strong>" . $ab->kurztext . "</strong></td>";
                    $output .= "<td>" . JFactory::getUser($ab->admin_id)->name . "</td>";
                    $output .= "<td align=\"right\">" . $ab->jahressoll . " h</td>";
                    $output .= "<td align=\"right\">" . round(arbeit_summe($ab->id, $laufendes_jahr) / 60, 0) . " h</td>";
                    $output .= "<td align=\"right\">" . round(arbeit_summe($ab->id, $lastYear) / 60, 0) . " h</td>";
                    $output .= "<td align=\"right\">" . ($ab->pauschale > 0 ? $ab->pauschale . ' min' : '-') . "</td>";
                    $output .= "</tr>";

                    /*
                    if (strlen($ab->beschreibung) > 1) {
                        $output .= "<tr class=\"" . $style . "\"><td colspan=\"7\"> &nbsp; &raquo; " . $ab->beschreibung . "</td></tr>";
                    }
                    */

                    $k = 1 - $k;
                }
                $output .= "</table><br />";
            }
        }
    }

    return $output;
}


// Ermittelt die Summe der Stunden eines bestimmten Ämtlis während des laufenden Kalenderjahres
function arbeit_summe($id, $jahr)
{
    $db = JFactory::getDBO();

    $query = "SELECT COALESCE(sum(minuten),0) minuten FROM #__mgh_zb_journal
	          WHERE datum_quittung != '0000-00-00' 
	            AND (datum_antrag BETWEEN '" . $jahr . "-01-01' AND '" . $jahr . "-12-31')
	            AND admin_del = 0 
	            AND arbeit_id = " . $id;

    $db->setQuery($query);
    return $db->loadResult();
}

// Ermittelt die Anzahl noch zu quittierenden Anträge für einen Admin
function get_anzahl_offen()
{
    $db = JFactory::getDBO();
    $user = JFactory::getUser();

    $query = "SELECT journal.id,journal.cf_uid as cf_uid,minuten,users.name as name,datum_antrag,arbeit.kurztext,
    		kat.user_id as gegenkonto,kommentar.text as text
    		FROM #__users as users, #__mgh_zb_arbeit as arbeit, #__mgh_zb_kategorie as kat,
    		#__mgh_zb_antr_kommentar as kommentar RIGHT JOIN #__mgh_zb_journal AS journal ON kommentar.journal_id = journal.id
    		WHERE datum_quittung='0000-00-00' AND admin_del='0' AND users.id = gutschrift_userid AND arbeit.id = journal.arbeit_id
    		AND kat.id = arbeit.kategorie_id AND arbeit.admin_id ='" . $user->id . "' ORDER BY datum_antrag ASC,journal.id ASC";
    $db->setQuery($query);
    $db->loadObjectList();

    return ($db->getAffectedRows());

} // get_anzahl_offen()



// Bestimmt den Wert für das Feld reihenfolge, damit neues Ämtli am Schluss eingefügt wird
function get_ende_reihenfolge()
{
    $db = JFactory::getDBO();

    $query = "SELECT ordering FROM #__mgh_zb_arbeit ORDER BY ordering DESC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    if (mysql_affected_rows() > 0):
        return ($rows[0]->ordering + 1);
    else:
        return (1);
    endif;


} // get_ende_reihenfolge()