<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdBase', JPATH_COMPONENT . '/controllers/upd_base.php');

/**
 * Basis-Klasse für die Controller zum Editieren eines Angebots im Marktplatz.
 */
abstract class ZeitbankControllerUpdAngebotBase extends ZeitbankControllerUpdBase
{

    protected function checkGeneralPermission()
    {
        return ZeitbankAuth::checkAuthMarket();
    }

    protected function clearSessionData()
    {
        $app = JFactory::getApplication();
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, null);
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, null);
    }

    protected function saveDataInSession($data)
    {
        $app = JFactory::getApplication();
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, $data);
    }

    protected function performPreEdit()
    {
        $id = $this->getId();
        if ($id == 0) {
            $app = JFactory::getApplication();
            $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, $this->getArt());
        }
    }

    /**
     * Liefert true, wenn der Benutzer den Eintrag bearbeiten darf. Wenn ID=0, wird immer true geliefert.
     *
     * @param $id int
     * @return boolean
     */
    protected function isEditAllowed($id)
    {
        if ($id == 0) {
            return true;
        }

        $model = $this->getModel();
        if (!$model->isOwner($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
            return false;
        }
        return true;
    }

    /**
     * Im Standardfall keine besondere Prüfung gegenüber der Edit-Prüfung.
     *
     * @inheritdoc
     */
    protected function isSaveAllowed($id, $data)
    {
        return $this->isEditAllowed($id);
    }

    protected function isDeleteAllowed($id)
    {
        $model = $this->getModel();
        if (!$model->isOwner($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu löschen.', 'warning');
            return false;
        }
        return true;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Liefert die Art des Angebots, welches erstellt werden soll.
     */
    private function getArt()
    {
        $jinput = JFactory::getApplication()->input;
        return $jinput->get("art", 2);
    }

}