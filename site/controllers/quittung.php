<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

/**
 * Controller zum Quittieren von Anträgen.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerQuittung extends ZeitbankControllerUpdJournalBase {
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  protected function getViewName() {
    return "quittung";
  }
  
  protected function isSaveDataInSession() {
    return true;
  }
  
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    $dataAllowed['kommentar_quittung'] = $this->cropKommentar($data['kommentar_quittung']);
    return $dataAllowed;
  }
  
  /**
   * Buchung vervollständigen.
   */
  protected function modifyDataBeforeSave($data) {
    $buchung = array();
    $buchung['datum_quittung'] = date('Y-m-d');
    $buchung['kommentar_quittung'] = $data['kommentar_quittung'];
    return $buchung;
  }
  
  /**
   * Liefert true, wenn der Benutzer den Eintrag quittieren darf.
   */
  protected function isEditAllowed($id) { 
    $model = $this->getModel();
    if(!$model->isArbeitAdmin($id)) {
      JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu quittieren.','warning');
      return false;
    }
    return true;
  }
  
  /**
   * Wenn ein Ämtli quittiert wurde, so wird auf die Liste der offenen Anträge verzweigt.
   *
   * @inheritdoc
   */
  protected function redirectSuccessView($id = 0) {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $model = $this->getModel();
    
    if ($model->isJournalAemtli($id)) {
      $this->setRedirect(
          JRoute::_('index.php?option=com_zeitbank&view=quittung_amt&Itemid='.$menuId, false)
      );
    }
    else {
      $this->setRedirect(
          JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid='.$menuId, false)
      );
    }
  }
  
}