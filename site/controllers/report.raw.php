<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

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
    if (!ZeitbankFrontendHelper::checkAuthReports()) {
      return false;
    }
    
    $model = $this->getModel('report');
    $model->exportKontosaldoToCSV();
  }
  
}