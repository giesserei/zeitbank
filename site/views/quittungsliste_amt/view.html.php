<?php 
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * View zeigt alle Buchungen an, die der Benutzer quittiert hat. 
 * 
 * @author JAL
 * @author Steffen Förster
 */
class ZeitbankViewQuittungsliste_Amt extends JView {
  
  public function display($tpl = null) {
    $model = $this->getModel();
    $this->quittungsliste = $model->getQuittungsliste();
 
 	  ZeitbankFrontendHelper::addComponentStylesheet();
 	  
    parent::display($tpl);
  }
}
?> 
