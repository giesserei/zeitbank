<?php 
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

jimport('joomla.application.component.view');

/**
 * View-Klasse für die Reportseite der Zeitbank.
 *
 * @author Steffen Förster
 */
class ZeitbankViewReport extends JViewLegacy {

  /**
   * @var MarketPlaceOverview
   */
  protected $overview;

  /**
   * @var int
   */
  protected $menuId;
  
  public function display($tpl = null) {
    if (!ZeitbankAuth::checkAuthZeitbank() || !ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_KEY_DATA)) {
      return false;
    }
  
    $this->prepareDefault();
  
    return parent::display($tpl);
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
   * Liefert die Summen der verbuchten Giesserei-Stunden je Arbeitskategorie.
   */
  protected function getSummeGiessereiStundenNachKategorie() {
    $model = $this->getModel();
    return $model->getSummeGiessereiStundenNachKategorie();
  }
  
  /**
   * Liefert die Summen der verbuchten Sonstigen-Stunden (freiwillig, privater Stundentausch, Geschenke) je Arbeitskategorie.
   */
  protected function getSummeSonstigeStundenNachKategorie() {
    $model = $this->getModel();
    return $model->getSummeSonstigeStundenNachKategorie();
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