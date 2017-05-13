<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankModelUpdBase', JPATH_COMPONENT . '/models/upd_base.php');

/**
 * Model zum Bearbeiten einer Arbeit.
 */
class ZeitbankModelArbeit extends ZeitbankModelUpdBase
{

    public function getTable($type = 'Arbeit', $prefix = 'ZeitbankTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.arbeit', 'arbeit', $loadData);
    }

    protected function getDataFromSession()
    {
        return JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array());
    }

    /**
     * Prüft, ob die Eingaben korrekt sind.
     *
     * Validierungsmeldungen werden im Model gespeichert.
     *
     * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
     *
     * @inheritdoc
     */
    public function validate($form, $data, $group = NULL)
    {
        $validateResult = parent::validate($form, $data, $group);
        if ($validateResult === false) {
            return false;
        }

        $valid = 1;
        $valid &= $this->validateAdmin($validateResult['admin_id']);

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    /**
     * Liefert true, wenn der Benutzer Besitzer der Arbeit ist, er also der passende Kategorien-Admin ist.
     *
     * @param int   $id       ID der Arbeit
     *
     * @return boolean
     */
    public function isOwner($id)
    {
        $query = "SELECT count(*) AS owner
             FROM #__mgh_zb_arbeit AS a
             LEFT JOIN #__mgh_zb_kategorie k ON a.kategorie_id = k.id
             WHERE a.id = " . $this->db->quote($id) . " AND k.admin_id = " . $this->user->id;
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->owner == 1;
    }

    /**
     * Liefert den Wert für die Reihenfolge, so dass ein neues Ämtli am Ende eingefügt wird.
     *
     * @return int
     */
    public function getNextOrdering()
    {
        $query = "SELECT MAX(ordering) FROM #__mgh_zb_arbeit";
        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        return $result + 1;
    }

    /**
     * Verschiebt die übergebene Arbeit in der Reihenfolge einen Schritt nach oben.
     *
     * @param int  $id      ID der Arbeit
     * @return boolean
     */
    public function orderUp($id)
    {
        $idEsc = $this->db->quote($id);

        $query =
            "SELECT a.* FROM #__mgh_zb_arbeit a
             WHERE a.id = " . $idEsc;
        $this->db->setQuery($query);
        $arbeit = $this->db->loadObject();

        // Die Arbeit selektieren, welche in der Reihenfolge direkt oberhalb steht
        // Die Zahlen im Feld ordering müssen nicht zwangsläufig eine lückenlose Folge sein
        $query =
            "SELECT a.* FROM #__mgh_zb_arbeit a
             WHERE a.ordering < " . $arbeit->ordering . "
                 AND a.kategorie_id = " . $arbeit->kategorie_id . "
                 AND a.ordering = (
                     SELECT MAX(a2.ordering) FROM #__mgh_zb_arbeit a2
                     WHERE a2.ordering < " . $arbeit->ordering . "
                         AND a2.kategorie_id = " . $arbeit->kategorie_id . "
               )";
        $this->db->setQuery($query);
        $arbeitPre = $this->db->loadObject();

        if ($this->db->getAffectedRows() == 0) {
            return false;
        }

        $query = "UPDATE #__mgh_zb_arbeit SET ordering = " . $arbeit->ordering . " WHERE id = " . $arbeitPre->id;
        $this->db->setQuery($query);
        $this->db->execute();

        $query = "UPDATE #__mgh_zb_arbeit SET ordering = " . $arbeitPre->ordering . " WHERE id = " . $arbeit->id;
        $this->db->setQuery($query);
        $this->db->execute();

        return true;
    }

    /**
     * Verschiebt die übergebene Arbeit in der Reihenfolge einen Schritt nach unten.
     *
     * @param int  $id      ID der Arbeit
     * @return boolean
     */
    public function orderDown($id)
    {
        $idEsc = $this->db->quote($id);

        $query =
            "SELECT a.* FROM #__mgh_zb_arbeit a
             WHERE a.id = " . $idEsc;
        $this->db->setQuery($query);
        $arbeit = $this->db->loadObject();

        // Die Arbeit selektieren, welche in der Reihenfolge direkt unterhalb steht
        // Die Zahlen im Feld ordering müssen nicht zwangsläufig eine lückenlose Folge sein
        $query =
            "SELECT a.* FROM #__mgh_zb_arbeit a
             WHERE a.ordering > " . $arbeit->ordering . "
                 AND a.kategorie_id = " . $arbeit->kategorie_id . "
                 AND a.ordering = (
                     SELECT MIN(a2.ordering) FROM #__mgh_zb_arbeit a2
                     WHERE a2.ordering > " . $arbeit->ordering . "
                         AND a2.kategorie_id = " . $arbeit->kategorie_id . "
               )";
        $this->db->setQuery($query);
        $arbeitNext = $this->db->loadObject();

        if ($this->db->getAffectedRows() == 0) {
            return false;
        }

        $query = "UPDATE #__mgh_zb_arbeit SET ordering = " . $arbeit->ordering . " WHERE id = " . $arbeitNext->id;
        $this->db->setQuery($query);
        $this->db->execute();

        $query = "UPDATE #__mgh_zb_arbeit SET ordering = " . $arbeitNext->ordering . " WHERE id = " . $arbeit->id;
        $this->db->setQuery($query);
        $this->db->execute();

        return true;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function validateAdmin($adminId)
    {
        if (!isset($adminId)) {
            JFactory::getApplication()->enqueueMessage(
                'Bitte Administrator auswählen oder ggf. vorher einen Administrator erfassen.', 'warning');
            return false;
        }

        $query = "SELECT count(*) FROM #__users u
             WHERE u.id = " . $this->db->quote($adminId);
        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if ($result != 1) {
            JFactory::getApplication()->enqueueMessage(
                'Oops. Der Administrator ist unbekannt.', 'warning');
            return false;
        }
        return true;
    }
}