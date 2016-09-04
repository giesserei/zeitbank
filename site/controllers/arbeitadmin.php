<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdZeitbankBase', JPATH_COMPONENT . '/controllers/upd_zeitbank_base.php');

/**
 * Controller zum Hinzufügen/Entfernen von Arbeit-Administratoren zu einer Kategorie.
 */
class ZeitbankControllerArbeitAdmin extends ZeitbankControllerUpdZeitbankBase
{

    protected function filterFormFields($data)
    {
        $dataAllowed = array();
        $dataAllowed['id'] = $data['id'];
        $dataAllowed['kat_id'] = $data['kat_id'];
        $dataAllowed['user_id'] = $data['user_id'];
        return $dataAllowed;
    }

    protected function isSaveDataInSession()
    {
        return true;
    }

    protected function getViewName()
    {
        return "arbeitadmin";
    }

    protected function isEditAllowed($id)
    {
        if (!ZeitbankAuth::getKategorieId()) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt.', 'warning');
            return false;
        }
        return true;
    }

    protected function isSaveAllowed($id, $data)
    {
        if (!ZeitbankAuth::isKategorieAdmin($data['kat_id'])) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt.', 'warning');
            return false;
        }
        return true;
    }

    protected function isDeleteAllowed($id)
    {
        $model = $this->getModel();
        if (!$model->isOwner($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt.', 'warning');
            return false;
        }
        return true;
    }

    /**
     * Verbleibt nach einer erfolgreichen Änderung auf der Edit-Seite.
     *
     * @inheritdoc
     */
    protected function redirectSuccessView($id)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $this->setRedirect(
            JRoute::_('index.php?option=com_zeitbank&task=arbeitadmin.edit&id=0&Itemid=' . $menuId, false)
        );
    }

}