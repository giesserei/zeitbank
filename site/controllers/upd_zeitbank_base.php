<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdBase', JPATH_COMPONENT . '/controllers/upd_base.php');

/**
 * Basis-Klasse für die Controller zum Editieren von Zeitbank-Objekten.
 */
abstract class ZeitbankControllerUpdZeitbankBase extends ZeitbankControllerUpdBase
{

    protected function checkGeneralPermission()
    {
        return ZeitbankAuth::checkAuthZeitbank();
    }

    protected function clearSessionData()
    {
        $app = JFactory::getApplication();
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, null);
    }

    protected function saveDataInSession($data)
    {
        $app = JFactory::getApplication();
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, $data);
    }

}