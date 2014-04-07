<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

/**
 * View der Einstiegsseite zur Zeitbank.
 */
class ZeitbankViewZeitbank extends JView {
  
  protected $quittierungen;
  
  protected $antraege;
  
  protected $journal;
  
  function display($tpl = null) {
    $model = $this->getModel();

    $this->quittierungen = $model->getOffeneQuittierungen();
    $this->antraege = $model->getOffeneAntraege();
    $this->journal = $model->getUserJournal();
    
    ZeitbankFrontendHelper::addComponentStylesheet();

    parent::display($tpl);
  }
  
  /**
   * Liefert das Soll für den Bewohner.
   */
  protected function getSoll() {
    $user = JFactory::getUser();
    return ZeitbankCalc::getSollBewohner($user->id);
  }
  
  /**
   * Liefert das Saldo für den Bewohner/das Gewerbe.
   */
  protected function getSaldo() {
    $user = JFactory::getUser();
    return ZeitbankCalc::getSaldo($user->id);
  }
  
  /**
   * Liefert das Saldo der Freiwilligenarbeit für den Bewohner/das Gewerbe.
   */
  protected function getSaldoFreiwilligenarbeit() {
    $user = JFactory::getUser();
    return ZeitbankCalc::getSaldoFreiwilligenarbeit($user->id);
  }
  
  /**
   * Liefert das Saldo des Vorjahres für den Bewohner/das Gewerbe.
   */
  protected function getSaldoVorjahr() {
    $user = JFactory::getUser();
    return ZeitbankCalc::getSaldoVorjahr($user->id);
  }
  
  /**
   * Liefert true, wenn das angemeldete Mitglied ein Gewerbe ist.
   */
  protected function isGewerbe() {
    $user = JFactory::getUser();
    $model = $this->getModel();
    return $model->isGewerbe($user->id);
  }
}
?> 
