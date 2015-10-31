<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

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
   * @see ZeitbankControllerUpdJournalBase::getViewName()
   */
  protected function getViewName() {
    return "stundengeschenk";
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::saveDataInSession()
   */
  protected function isSaveDataInSession() {
    return true;
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::filterFormFields()
   */
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = 0;
    $dataAllowed['empfaenger_id'] = $data['empfaenger_id'];
    $dataAllowed['empfaenger'] = $data['empfaenger'];
    $dataAllowed['minuten'] = $data['minuten'];
    $dataAllowed['datum_antrag'] = $data['datum_antrag'];
    $dataAllowed['kommentar_antrag'] = $this->cropKommentar($data['kommentar_antrag']);
    
    return $dataAllowed;
  }
  
  /**
   * Buchung vervollständigen.
   */
  protected function modifyDataBeforeSave($data) {
    $buchung = array();
    $buchung['minuten'] = intval($data['minuten']);
    $buchung['belastung_userid'] = JFactory::getUser()->id;
    $buchung['gutschrift_userid'] = $data['empfaenger_id'];
    $buchung['datum_antrag'] = $data['datum_antrag'];
    $buchung['datum_quittung'] = $data['datum_antrag'];
    $buchung['admin_del'] = 0;
    $buchung['arbeit_id'] = ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;
    $buchung['cf_uid'] = md5(uniqid(rand(), true));
    $buchung['kommentar_antrag'] = $data['kommentar_antrag'];
    return $buchung;
  }
  
}