<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdZeitbankBase', JPATH_COMPONENT . '/controllers/upd_zeitbank_base.php');

/**
 * Controller zum Editieren einer Kategorie.
 */
class ZeitbankControllerKategorie extends ZeitbankControllerUpdZeitbankBase
{

    protected function filterFormFields($data)
    {
        $dataAllowed = array();
        $dataAllowed['id'] = $data['id'];
        $dataAllowed['gesamtbudget'] = $data['gesamtbudget'];
        return $dataAllowed;
    }

    protected function isSaveDataInSession()
    {
        return true;
    }

    protected function getViewName()
    {
        return "kategorie";
    }

    /**
     * Liefert true, wenn der Benutzer die Kategorie bearbeiten darf.
     *
     * @inheritdoc
     */
    protected function isEditAllowed($id)
    {
        if ($id == 0 || !ZeitbankAuth::isKategorieAdmin($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
            return false;
        }
        return true;
    }

}