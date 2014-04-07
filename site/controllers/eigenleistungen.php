<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

/**
 * Controller zum Buchungen von Eigenleistungsstunden.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerEigenleistungen extends ZeitbankControllerUpdJournalBase {
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  /**
   * @see ZeitbankControllerUpdJournalBase::getViewName()
   */
  protected function getViewName() {
    return "eigenleistungen";
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::saveDataInSession()
   */
  protected function saveDataInSession() {
    return true;
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::filterFormFields()
   */
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    $dataAllowed['arbeit_id'] = $data['arbeit_id'];
    $dataAllowed['minuten'] = $data['minuten'];
    $dataAllowed['kommentar_antrag'] = $this->cropKommentar($data['kommentar_antrag']);
    return $dataAllowed;
  }
  
  /**
   * Buchung vervollständigen.
   */
  protected function completeBuchung($data) {  
    $buchung = array();
    $buchung['minuten'] = $this->getModel()->getMinuten(intval($data['minuten']), $data['arbeit_id']);
    $buchung['belastung_userid'] = $this->getModel()->getZeitkonto($data['arbeit_id']);
    $buchung['gutschrift_userid'] = JFactory::getUser()->id;
    $buchung['datum_antrag'] = date('Y-m-d');
    $buchung['datum_quittung'] = '0000-00-00';
    $buchung['admin_del'] = 0;
    $buchung['arbeit_id'] = $data['arbeit_id'];
    $buchung['cf_uid'] = empty($data['cf_uid']) ? md5(uniqid(rand(), true)) : $data['cf_uid'];
    $buchung['kommentar_antrag'] = $data['kommentar_antrag'];
    return $buchung;
  }
  
}