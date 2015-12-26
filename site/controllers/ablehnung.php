<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

/**
 * Controller zum Ablehnen von Anträgen. Ein abgelehnter Antrag hat einen entsprechenden Status
 * und eine Begründung.
 */
class ZeitbankControllerAblehnung extends ZeitbankControllerUpdJournalBase
{

    // -------------------------------------------------------------------------
    // protected section
    // -------------------------------------------------------------------------

    protected function getViewName()
    {
        return "ablehnung";
    }

    protected function isSaveDataInSession()
    {
        return true;
    }

    protected function filterFormFields($data)
    {
        $dataAllowed = array();
        $dataAllowed['id'] = $data['id'];
        $dataAllowed['kommentar_ablehnung'] = $this->cropKommentar($data['kommentar_ablehnung']);
        return $dataAllowed;
    }

    /**
     * Buchung vervollständigen.
     *
     * @inheritdoc
     */
    protected function modifyDataBeforeSave($data)
    {
        $buchung = array();
        $buchung['abgelehnt'] = 1;
        $buchung['kommentar_ablehnung'] = $data['kommentar_ablehnung'];
        return $buchung;
    }

    /**
     * Liefert true, wenn der Benutzer den Eintrag ablehnen darf.
     *
     * @inheritdoc
     */
    protected function isEditAllowed($id)
    {
        $model = $this->getModel();
        if (!$model->isArbeitAdmin($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag abzulehnen.', 'warning');
            return false;
        }
        return true;
    }

    /**
     * Wenn ein Ämtli-Antrag quittiert wurde, so wird auf die Liste der offenen Anträge verzweigt.
     *
     * @inheritdoc
     */
    protected function redirectSuccessView($id = 0)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $model = $this->getModel();

        if ($model->isJournalAemtli($id)) {
            $this->setRedirect(
                JRoute::_('index.php?option=com_zeitbank&view=quittung_amt&Itemid=' . $menuId, false)
            );
        } else {
            $this->setRedirect(
                JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $menuId, false)
            );
        }
    }

}