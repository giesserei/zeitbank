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

    /**
     * Liefert true, wenn der Benutzer den Eintrag bearbeiten darf. Wenn ID=0, wird immer true geliefert.
     *
     * @inheritdoc
     */
    protected function isEditAllowed($id)
    {
        if ($id == 0) {
            return true;
        }

        $model = $this->getModel();
        if (!$model->isEditAllowed($id)) {
            JFactory::getApplication()->enqueueMessage(
                'Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
            return false;
        }
        return true;
    }

}