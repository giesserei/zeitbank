<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

JLoader::register('ZeitbankModelUpdBase', JPATH_COMPONENT . '/models/upd_base.php');

/**
 * Model zum Hinzufügen/Entfernen von Arbeit-Administratoren zu einer Kategorie.
 */
class ZeitbankModelArbeitAdmin extends ZeitbankModelUpdBase
{

    public function getTable($type = 'ArbeitAdmin', $prefix = 'ZeitbankTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.arbeitadmin', 'arbeitadmin', $loadData);
    }

    protected function getDataFromSession()
    {
        return JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array());
    }

    /**
     * Prüft, ob die Eingaben korrekt sind. Validierungsmeldungen werden im Model gespeichert.
     *
     * @return mixed Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
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
        $valid &= $this->validateKategorieAccess($validateResult['kat_id']);
        $valid &= $this->validateRequiredFields($validateResult['user_id']);

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    /**
     * Liefert alle Administratoren zur übergebenen Kategorie.
     *
     * @param int $katId Kategorie
     * @return mixed
     */
    public static function getAdministratoren($katId)
    {
        $db = JFactory::getDBO();
        $query =
            "SELECT u.name, k.id FROM #__mgh_zb_x_kat_arbeitadmin k
             LEFT JOIN #__users u ON k.user_id = u.id
             WHERE kat_id=" . $katId;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Liefert true, wenn der Benutzer Besitzer des Ämtli-Administrators ist, er also der passende
     * Kategorien-Admin ist.
     *
     * @param int   $id       ID des Ämtli-Admins
     *
     * @return boolean
     */
    public function isOwner($id)
    {
        $query = "SELECT count(*) AS owner
             FROM #__mgh_zb_x_kat_arbeitadmin AS a
             LEFT JOIN #__mgh_zb_kategorie k ON a.kat_id = k.id
             WHERE a.id = " . $this->db->quote($id) . " AND k.admin_id = " . $this->user->id;
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        return $result->owner == 1;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Der Benutzer muss Administrator der Kategorie sein.
     *
     * @param $katId int ID der Kategorie
     *
     * @return boolean
     */
    private function validateKategorieAccess($katId)
    {
        if (!ZeitbankAuth::isKategorieAdmin($katId)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt für diese Kategorie', 'warning');
            return false;
        }

        return true;
    }

    /**
     * Prüft die erforderlichen Werte.
     *
     * @param $userId int Benutzer
     *
     * @return boolean
     */
    private function validateRequiredFields($userId)
    {
        if ($userId == -1) {
            JFactory::getApplication()->enqueueMessage(
                'Bitte wähle einen Benutzer aus.', 'warning');
            return false;
        }
        return true;
    }

}