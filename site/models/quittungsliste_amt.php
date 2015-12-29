<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Model für die Liste der quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
 */
class ZeitbankModelQuittungsliste_Amt extends JModelLegacy
{

    /**
     * Hinweis:
     * Der der Admin zu einem Ämtli geändert werden kann, muss nicht unbedingt der aktuelle Admin auch die Quittierung
     * durchgeführt haben.
     *
     * @param int   $tage   Anzahl der Tage, für die die Buchungen zurück in der Vergangenheit berücksichtigt werden
     * @return mixed
     */
    public function getQuittungsliste($tage)
    {
        $db = JFactory::getDBO();
        $query = $this->buildQuery($tage);
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function buildQuery($tage)
    {
        $user = JFactory::getUser();
        $query =
            "SELECT SQL_CALC_FOUND_ROWS j.id, j.minuten, j.datum_antrag, a.kurztext,
               (SELECT u.name FROM #__users u WHERE u.id = j.gutschrift_userid) konto_gutschrift
    	     FROM #__mgh_zb_journal j JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
             WHERE j.datum_quittung != '0000-00-00'
                 AND j.admin_del = '0'
                 AND ADDDATE(j.datum_quittung, " . $tage . ") > CURRENT_DATE
                 AND a.admin_id = " . $user->id . "
             ORDER BY j.datum_antrag DESC, j.id DESC";
        return ($query);
    }
}
