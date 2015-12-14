<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

/**
 * Controller zum Buchungen von Freiwilligenarbeit.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerFreiwilligenarbeit extends ZeitbankControllerUpdJournalBase {
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  protected function getViewName() {
    return "freiwilligenarbeit";
  }
  
  protected function isSaveDataInSession() {
    return true;
  }
  
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    $dataAllowed['arbeit_id'] = $data['arbeit_id'];
    $dataAllowed['minuten'] = $data['minuten'];
    $dataAllowed['datum_antrag'] = $data['datum_antrag'];
    $dataAllowed['kommentar_antrag'] = $this->cropKommentar($data['kommentar_antrag']);
    return $dataAllowed;
  }
  
  /**
   * Buchung vervollständigen.
   *
   * @inheritdoc
   */
  protected function modifyDataBeforeSave($data) {
    $buchung = array();
    $buchung['minuten'] = intval($data['minuten']);
    $buchung['belastung_userid'] = $this->getModel()->getZeitkonto($data['arbeit_id']);
    $buchung['gutschrift_userid'] = JFactory::getUser()->id;
    $buchung['datum_antrag'] = $data['datum_antrag'];
    $buchung['datum_quittung'] = '0000-00-00';
    $buchung['admin_del'] = 0;
    $buchung['arbeit_id'] = $data['arbeit_id'];
    $buchung['cf_uid'] = empty($data['cf_uid']) ? md5(uniqid(rand(), true)) : $data['cf_uid'];
    $buchung['kommentar_antrag'] = $data['kommentar_antrag'];
    $buchung['abgelehnt'] = 0;
    return $buchung;
  }
  
}