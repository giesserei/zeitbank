<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdAngebotBase', JPATH_COMPONENT . '/controllers/upd_angebot_base.php');

/**
 * Controller zum Editieren eines Angebots.
 */
class ZeitbankControllerUpdAngebot extends ZeitbankControllerUpdAngebotBase
{

    protected function getViewName()
    {
        return "updangebot";
    }

    protected function isSaveDataInSession()
    {
        return true;
    }

    protected function filterFormFields($data)
    {
        $dataAllowed = array();
        $dataAllowed['id'] = $data['id'];
        $dataAllowed['titel'] = $data['titel'];
        $dataAllowed['beschreibung'] = $data['beschreibung'];
        $dataAllowed['art'] = $data['art'];
        $dataAllowed['richtung'] = $data['richtung'];
        $dataAllowed['arbeit_id'] = $data['arbeit_id'];
        $dataAllowed['status'] = $data['status'];
        $dataAllowed['ablauf'] = $data['ablauf'];
        $dataAllowed['zeit'] = $data['zeit'];
        $dataAllowed['anforderung'] = $data['anforderung'];
        $dataAllowed['aufwand'] = $data['aufwand'];

        return $dataAllowed;
    }

    protected function redirectSuccessView($id)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $this->setRedirect(
            JRoute::_('index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid=' . $menuId, false)
        );
    }

}