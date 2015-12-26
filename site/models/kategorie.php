<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('ZeitbankModelUpdBase', JPATH_COMPONENT . '/models/upd_base.php');

/**
 * Model zum Bearbeiten eines Kategorie.
 */
class ZeitbankModelKategorie extends ZeitbankModelUpdBase
{

    public function getTable($type = 'Kategorie', $prefix = 'ZeitbankTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.kategorie', 'kategorie', $loadData);
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

        return $validateResult;
    }

}