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
  
  /**
   * Liefert die Summe der verbuchten Arbeitstunden ohne den Stundentausch.
   */
  public function getSummeArbeitStunden() {
    $model = $this->getModel();
    return $model->getSummeArbeitStunden();
  }
  
  /**
   * Liefert die Summe der nicht quittierten Arbeitstunden ohne den Stundentausch.
   */
  public function getSummeNichtQuittierteStunden() {
    $model = $this->getModel();
    return $model->getSummeNichtQuittierteStunden();
  }
  
  /**
   * Liefert die durchschnittliche Wartezeit der noch unquittierten Buchungen.
   */
  public function getWartezeitUnquittierteBuchungen() {
    $model = $this->getModel();
    return $model->getWartezeitUnquittierteBuchungen();
  }
  
  /**
   * Liefert die Summen der verbuchten Stunden je Arbeitskategorie.
   */
  protected function getSummeStundenNachKategorie() {
    $model = $this->getModel();
    return $model->getSummeStundenNachKategorie();
  }
  
  /**
   * Liefert die maximale und die durchschnittliche Dauer zwischen einer Buchung und der Quittierung.
   */
  public function getQuittungDauer() {
    $model = $this->getModel();
    return $model->getQuittungDauer();
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