<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

jimport('joomla.application.component.controllerform');

/**
 * Controller zum Übertragen von Stunden als anonymes Geschenk.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerStundenGeschenk extends ZeitbankControllerUpdJournalBase {
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  /**
   * @see ZeitbankControllerStundenGeschenk::getViewName()
   */
  protected function getViewName() {
    return "stundengeschenk";
  }
  
  /**
   * @see ZeitbankControllerStundenGeschenk::saveDataInSession()
   */
  protected function saveDataInSession() {
    return true;
  }
  
  /**
   * @see ZeitbankControllerStundenGeschenk::filterFormFields()
   */
  protected function filterFormFields($data) {
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
  
  /**
   * @see ZeitbankControllerStundenGeschenk::redirectSuccessView()
   */
  protected function redirectSuccessView() {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$menuId, false)
    );
  }
  
}