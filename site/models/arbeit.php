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
        $query = sprintf(
            "SELECT count(*) AS owner
             FROM #__mgh_zb_arbeit AS a
             LEFT JOIN #__mgh_zb_kategorie k ON a.kategorie_id = k.id
             WHERE a.id = %s AND k.admin_id = %s", mysql_real_escape_string($id), $this->user->id);
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->owner == 1;
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

        $query = sprintf(
            "SELECT count(*) FROM #__users u
             WHERE u.id = %s", mysql_real_escape_string($adminId));
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