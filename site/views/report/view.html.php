<?php 
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * View-Klasse für die Reportseite der Zeitbank.
 *
 * @author Steffen Förster
 */
class ZeitbankViewReport extends JView {
  
  protected $overview;
  
  protected $menuId;
  
  function display($tpl = null) {
    if (!ZeitbankFrontendHelper::checkAuthReports()) {
      return false;
    }
  
    $this->prepareDefault();
  
    parent::display($tpl);
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  private function prepareDefault() {
    $app = JFactory::getApplication();
  
    // Statistiken aus Datenbank laden
    //$model = $this->getModel();
    //$this->overview = $model->getOverview(5);
  
    ZeitbankFrontendHelper::addComponentStylesheet();
  
    // Menü-Id in der User-Session speichern
    $jinput = $app->input;
    $this->menuId = $jinput->get("Itemid", "0", "INT");
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);
  }
}