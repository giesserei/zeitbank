<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('ZeitbankModelUpdBase', JPATH_COMPONENT . '/models/upd_base.php');

/**
 * Basisklasse für die Model-Klassen, mit denen Journal-Einträge erstellt oder bearbeitet werden können.
 */
abstract class ZeitbankModelUpdJournalBase extends ZeitbankModelUpdBase
{

    public function getTable($type = 'Journal', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function getDataFromSession()
    {
        return JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array());
    }

    /**
     * Liefert true, wenn der angemeldete Benutzer Erfasser des übergebenen Journaleintrags ist.
     * Weiterhin darf der Antrag noch nicht bestätigt sein.
     * -> Funktioniert nicht für Stundengeschenke, da der Antragssteller hier keine Gutschrift bekommt.
     *
     * @param $id int ID des Datensatzes
     *
     * @return boolean
     */
    public function isOwner($id)
    {
        $query = "SELECT count(*) AS owner
         FROM #__mgh_zb_journal AS j
         WHERE j.id = " . $this->db->quote($id) . " AND j.gutschrift_userid = " . $this->user->id . "
           AND j.admin_del = 0
           AND j.datum_quittung is null";
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->owner == 1;
    }

    /**
     * Liefert true, wenn der angemeldete Benutzer der Admin der Arbeitskategorie ist, welche im übergebenen
     * Journaleintrag verwendet wird.
     * Ausnahme: Beim Stundentausch muss der angemeldete Benutzer der Besitzer des Belastungskontos sein.
     *
     * @param $id int ID der zu prüfenden Buchung
     *
     * @return boolean
     */
    public function isArbeitAdmin($id)
    {
        $query = "SELECT count(*) AS admin
         FROM #__mgh_zb_journal AS j JOIN #__mgh_zb_arbeit AS a ON j.arbeit_id = a.id
         WHERE j.id = " . $this->db->quote($id) . "
            AND (a.admin_id = " . $this->user->id . "
            OR (j.belastung_userid = " . $this->user->id . " AND a.id = " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . "))";
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->admin == 1;
    }

    /**
     * Liefert true, wenn der Journaleintrag zu einem Ämtli gehört und damit nicht zum privaten Stundentausch.
     *
     * @param $id int ID der Buchung
     *
     * @return boolean
     */
    public function isJournalAemtli($id)
    {
        $query = "SELECT count(*) AS aemtli
          FROM #__mgh_zb_journal j
          WHERE j.id = " . $this->db->quote($id) . " AND j.arbeit_id != " . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH;
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->aemtli == 1;
    }

    /**
     * Liefert den Antrag.
     *
     * @param $id int ID der Buchung, die zu bestätigen ist
     *
     * @return array
     */
    public function getAntrag($id)
    {
        $query = "SELECT journal.id, minuten, datum_antrag, kurztext, journal.kommentar_antrag AS text, journal.arbeit_id,
           (SELECT u.name FROM #__users u WHERE u.id = journal.belastung_userid) konto_belastung,
           (SELECT u.name FROM #__users u WHERE u.id = journal.gutschrift_userid) konto_gutschrift
                 FROM #__mgh_zb_arbeit AS arbeit JOIN #__mgh_zb_journal AS journal ON journal.arbeit_id = arbeit.id
                 WHERE journal.id = " . $this->db->quote($id);
        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    /**
     * Prüft, ob das Antragsdatum korrekt gesetzt ist.
     *
     * @param $datumAntrag DateTime Antragsdatum
     *
     * @return boolean
     */
    public function validateDatumAntrag($datumAntrag)
    {
        if (ZeitbankCalc::isBuchungGesperrt()) {
            JFactory::getApplication()->enqueueMessage(
                'Das Antragsdatum ist nicht korrekt!', 'warning');
            return false;
        }

        if (strcmp($datumAntrag, date('Y-m-d')) == 0) {
            return true;
        }
        if (ZeitbankCalc::isLastYearAllowed()) {
            $lastYear = intval(date('Y')) - 1;
            if (strcmp($datumAntrag, $lastYear . '-12-31') == 0) {
                return true;
            }
        }

        JFactory::getApplication()->enqueueMessage(
            'Das Antragsdatum ist nicht korrekt!', 'warning');
        return false;
    }

}
