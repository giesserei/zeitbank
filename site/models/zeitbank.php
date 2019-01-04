<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Model für die Übersichtsseite der Zeitbank.
 */
class ZeitbankModelZeitbank extends JModelLegacy
{

    /**
     * Liefert String mit menschenlesbarer Zeitangabe HH:MM
     *
     * @param $time_in_minutes int
     *
     * @return string
     */
    public function showTime($time_in_minutes)
    {
        $time_in_minutes = round($time_in_minutes);

        // Negative Werte gesondert behandeln
        if ($time_in_minutes >= 0) {
            $hours = floor($time_in_minutes / 60);
            $minutes = $time_in_minutes - $hours * 60;
        } else {
            $hours = ceil($time_in_minutes / 60);
            $minutes = $time_in_minutes - $hours * 60;
        }

        // Minuszeichen bei den Minuten wegschneiden
        $minutes = ltrim($minutes, '-');
        if (strlen($minutes) <= 1) {
            $minutes = "0" . $minutes;
        }
        return ($hours . ":" . $minutes);
    }

    /**
     * Liefert die Liste mit den Anträgen "Privater Stundentausch", welche zur Belastung des eigenen Zeitkontos führen.
     *
     * In der ursprünlichen Version gab es keine Einschränkung auf den privaten Stundentausch. Hätte man sich mit einem
     * Zeitkonto-Login angemeldet, hätte man somit auch quittieren können.
     *
     * Besser ist jedoch eine Funktion im Backend, von wo man Quittierungen durchführen kann.
     */
    public function getOffeneQuittierungen()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $query = "SELECT j.id, j.minuten, j.datum_antrag, a.kurztext, j.kommentar_antrag text, u.email, m.vorname, m.nachname
    		      FROM #__users u, #__mgh_zb_arbeit a, #__mgh_zb_journal j, #__mgh_mitglied m
    		      WHERE j.datum_quittung='0000-00-00' 
                AND j.admin_del='0' 
                AND j.arbeit_id = a.id
    		        AND u.id = j.gutschrift_userid
                AND m.userid = j.gutschrift_userid
    		        AND j.belastung_userid = " . $user->id . "
    		        AND a.id = " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . "
              ORDER BY j.datum_antrag ASC, j.id ASC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return ($rows);
    }

    /**
     * Liefert die Liste der eigenen Anträge, welche noch nicht quittiert sind.
     */
    public function getOffeneAntraege()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $query = "SELECT j.id, j.minuten, u.name, j.datum_antrag, a.kurztext, j.kommentar_antrag as text,
                j.abgelehnt, j.kommentar_ablehnung, j.arbeit_id,
                IF (a.id != " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . ",
                    (SELECT vorname FROM #__mgh_mitglied m WHERE m.userid = a.admin_id),'') vorname,
                IF (a.id != " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . ",
                    (SELECT nachname FROM #__mgh_mitglied m WHERE m.userid = a.admin_id),'') nachname,  
                IF (a.id != " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . ",
                    (SELECT email FROM #__users admin WHERE admin.id = a.admin_id),'') email,    
                CASE 
                  WHEN a.id = " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . " THEN 'stundentausch.edit'
                  WHEN a.kategorie_id = " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG . " THEN 'freiwilligenarbeit.edit'
                  ELSE 'eigenleistungen.edit'
                END AS task 
    		      FROM #__users u, #__mgh_zb_arbeit a, #__mgh_zb_journal j
    		      WHERE j.datum_quittung = '0000-00-00' 
                AND j.admin_del = '0' 
                AND j.arbeit_id = a.id
    		        AND u.id = j.belastung_userid      
                AND j.gutschrift_userid ='" . $user->id . "'
              ORDER BY j.datum_antrag ASC, j.id ASC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return ($rows);
    }

    /**
     * Liefert alle Buchungen des angemeldeten Benutzers für das laufende Jahr.
     */
    public function getUserJournal($vorjahr)
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $view = $vorjahr ? "#__mgh_zb_journal_quittiert_vorjahr_inkl_freiw" : "#__mgh_zb_journal_quittiert_laufend_inkl_freiw";

        $query = "SELECT journal.id AS id, journal.cf_uid, minuten, belastung_userid, gutschrift_userid, datum_antrag,
                arbeit.kurztext, journal.arbeit_id, 
                CASE WHEN (journal.arbeit_id IN (SELECT id FROM #__mgh_zb_arbeit WHERE kategorie_id = -1)) THEN
                  'freiwillig'
                ELSE
                  'eigenleistung'
                END AS art
    		      FROM " . $view . " AS journal, #__mgh_zb_arbeit AS arbeit
    	       	WHERE arbeit_id = arbeit.id 	
    		        AND (gutschrift_userid = " . $user->id . " OR belastung_userid = " . $user->id . ")
    		      ORDER BY datum_antrag DESC, journal.id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows;
    }

    /**
     * Liefert den Namen aus der User-Tabelle zur übergebenen User-ID.
     *
     * @param $uid int
     * @return string
     */
    public function getUserName($uid)
    {
        $db = JFactory::getDBO();
        $query = "SELECT name FROM #__users WHERE id='" . $uid . "'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getAffectedRows() > 0):
            return ($rows[0]->name);
        else:
            return (NULL);
        endif;
    }

    /**
     * Liefert true, wenn das übergebene Mitglied ein Gewerbe ist.
     *
     * @param $userId int
     * @return boolean
     */
    public function isGewerbe($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT count(*)
              FROM #__mgh_mitglied
              WHERE userid = " . $userId . "
                AND typ = 2";
        $db->setQuery($query);
        $count = $db->loadResult();
        return $count == 1;
    }

    /**
     * Liefert das Kategorie-Objekt zur übergebenen ID
     *
     * @param int $id
     * @return object
     */
    public function getKategorieItem($id)
    {
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__mgh_zb_kategorie WHERE id = " . $id;
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * Ermittelt die Anzahl noch zu quittierenden Anträge für einen Arbeit-Admin.
     *
     * @param int $adminId ID des Admins
     * @return int
     */
    public function getAnzahlOffeneQuittierungen($adminId)
    {
        $db = JFactory::getDBO();

        $query =
            "SELECT count(*) FROM #__mgh_zb_journal j
             LEFT JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
    		 WHERE j.datum_quittung='0000-00-00' AND j.admin_del='0'
    		     AND a.admin_id = " . $adminId;
        $db->setQuery($query);
        return $db->loadResult();
    }

}
