<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Model für die Detailansicht einer Buchung.
 */
class ZeitbankModelBuchung extends JModelLegacy
{

    protected $db;

    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->db = JFactory::getDBO();
        $this->user = JFactory::getUser();
    }

    /**
     * Liefert true, wenn der angemeldete Benutzer Empfänger oder Geber der Stunden ist. Es wird auch
     * true geliefert, wenn der Benutzer der Admin des betroffenen Ämtli ist.
     *
     * @param int $id ID der Buchung
     * @return boolean
     */
    public function isViewAllowed($id)
    {
        $query = "SELECT count(*) AS allowed
         FROM #__mgh_zb_journal AS j JOIN #__mgh_zb_arbeit AS a ON j.arbeit_id = a.id
         WHERE j.id = " . $this->db->quote($id) . " 
           AND (j.gutschrift_userid = " . $this->user->id . " 
             OR j.belastung_userid = " . $this->user->id . " OR a.admin_id = " . $this->user->id . ")
           AND j.admin_del = 0";
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->allowed == 1;
    }

    /**
     * Liefert die Buchung mit der übergebenen ID.
     *
     * @param int $id ID der Buchung
     * @return array
     */
    public function getBuchung($id)
    {
        $query = "SELECT j.id, j.minuten, j.datum_antrag, j.datum_quittung, a.kurztext, j.kommentar_antrag, j.kommentar_quittung, j.arbeit_id,
           j.belastung_userid, j.gutschrift_userid,
           (SELECT u.name FROM #__users u WHERE u.id = j.belastung_userid) konto_belastung,
           (SELECT u.name FROM #__users u WHERE u.id = j.gutschrift_userid) konto_gutschrift
    		 FROM #__mgh_zb_arbeit AS a JOIN #__mgh_zb_journal AS j ON j.arbeit_id = a.id
    		 WHERE j.id = " . $this->db->quote($id);
        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

}
