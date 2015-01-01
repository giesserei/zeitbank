<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

jimport('joomla.application.component.controller');

/**
 * Controller für den Download von Reports.
 *
 * @author Steffen Förster
*/
class ZeitbankControllerReport extends JController {
  
  /**
   * Liefert die aktuellen Kontosaldo aller Bewohner für das laufende Jahr.
   */
  public function kontosaldo() {
    if (!ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_DOWNLOAD_SALDO)) {
      return false;
    }
    
    $model = $this->getModel('report');
    $model->exportKontosaldoToCSV();
  }
  
  /**
   * Liefert die aktuellen Kontosaldo aller Bewohner für das Vorjahrjahr.
   */
  public function kontosaldoVorjahr() {
    if (!ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_DOWNLOAD_SALDO)) {
      return false;
    }
  
    $model = $this->getModel('report');
    $model->exportKontosaldoVorjahrToCSV();
  }
  
  /**
   * Liefert eine CSV-Datei mit quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
   */
  public function aemtliBuchungen() {
    if (!ZeitbankAuth::checkAuthZeitbank()) {
      return false;
    }
    
    $model = $this->getModel('report');
    $model->exportAemtliBuchungenToCSV();
  }
  
  /**
   * Liefert eine CSV-Datei mit quittierten Buchungen für das Konto des angemeldeten Benutzers - ohne Zeiteinschränkung. 
   */
  public function kontoauszug() {
    if (!ZeitbankAuth::checkAuthZeitbank()) {
      return false;
    }
  
    $model = $this->getModel('report');
    $model->exportKontoauszugToCSV();
  }
}