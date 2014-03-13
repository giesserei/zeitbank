<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

/**
 * View der Einstiegsseite zur Zeitbank.
 */
class ZeitbankViewZeitbank extends JView {
  
  function display($tpl = null) {
    $model =& $this->getModel();

    $quittierungen = $model->getOffeneQuittierungen();
    $antraege = $model->getOffeneAntraege();
    $journal = $model->getUserJournal();
    $saldo_vorjahr = $model->getSaldoVorjahr();

    $this->assignRef('quittierungen',$quittierungen);
    $this->assignRef('antraege',$antraege);
    $this->assignRef('journal',$journal);
    $this->assignRef('saldo_vorjahr',$saldo_vorjahr);
    
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
   * Liefert das Saldo für den Bewohner.
   */
  protected function getSaldo() {
    $user = JFactory::getUser();
    return ZeitbankCalc::getSaldo($user->id);
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
