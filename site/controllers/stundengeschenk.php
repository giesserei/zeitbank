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
    $dataAllowed['id'] = 0;
    $dataAllowed['empfaenger_id'] = $data['empfaenger_id'];
    $dataAllowed['empfaenger'] = $data['empfaenger'];
    $dataAllowed['minuten'] = $data['minuten'];
    $dataAllowed['kommentar'] = $data['kommentar'];
    
    return $dataAllowed;
  }
  
  /**
   * Buchung vervollständigen.
   */
  protected function completeBuchung($data) {
    $buchung = array();
    $buchung['minuten'] = intval($data['minuten']);
    $buchung['belastung_userid'] = JFactory::getUser()->id;
    $buchung['gutschrift_userid'] = $data['empfaenger_id'];
    $buchung['datum_antrag'] = date('Y-m-d');
    $buchung['datum_quittung'] = date('Y-m-d');
    $buchung['admin_del'] = 0;
    $buchung['arbeit_id'] = ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;
    $buchung['cf_uid'] = md5(uniqid(rand(), true));
    $buchung['kommentar'] = $data['kommentar'];
    return $buchung;
  }
  
  /**
   * @see ZeitbankControllerStundenGeschenk::redirectSuccessView()
   */
  protected function redirectSuccessView() {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid='.$menuId, false)
    );
  }
  
}