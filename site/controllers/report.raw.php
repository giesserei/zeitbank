<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.controller');

/**
 * Basis-Klasse für die Controller zum Editieren eines Angebots im Marktplatz.
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
    
    //$document = JFactory::getDocument();
    //$document->setType('raw');
    
    $model = $this->getModel('report');
    $model->exportKontosaldoToCSV();
  }
  
}