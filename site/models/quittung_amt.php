<?php
defined('_JEXEC') or die('Restricted access');


/**
 * Model für die View, welche die offenen Anträge auflistet.
 */
class ZeitbankModelQuittung_Amt extends JModelLegacy
{

    /**
     * Liefert alle offenen Anträge, für die der angemeldete Benutzer der Administrator ist.
     *
     * @return array[]
     */
    public function getOffeneQuittierungen()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();

        $query =
            "SELECT j.id, j.minuten, j.datum_antrag, a.kurztext, j.kommentar_antrag text, u.email, m.vorname, m.nachname,
          j.abgelehnt, j.kommentar_ablehnung
         FROM #__mgh_zb_journal j JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
          JOIN #__users u ON u.id = j.gutschrift_userid
          JOIN #__mgh_mitglied m ON m.userid = j.gutschrift_userid
       WHERE j.datum_quittung is null
         AND j.admin_del='0'
         AND a.admin_id = " . $user->id . "
       ORDER BY a.id, j.datum_antrag ASC, j.id ASC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

}
