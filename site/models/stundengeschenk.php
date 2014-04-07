<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model für die Ausführung eines Stundengeschenks.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelStundenGeschenk extends ZeitbankModelUpdJournalBase {
  
  public function __construct() {
    parent::__construct();
  }
  
  /**
   * @see JModelForm::getForm()
   */
  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.stundengeschenk', 'stundengeschenk', array (
        'control' => 'jform',
        'load_data' => $loadData 
    ));
    
    if (empty($form)) {
      return false;
    }
    
    return $form;
  }
  
  /**
   * Prüft, ob die Eingaben korrekt sind.
   * 
   * Validierungsmeldungen werden im Model gespeichert.
   * 
   * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
   * 
   * @see JModelForm::validate()
   */
  public function validate($form, $data) {
    $validateResult = parent::validate($form, $data);
    if ($validateResult === false) {
      return false;
    }
    
    $valid = 1;
    $valid &= $this->validateEmpfaenger($validateResult['empfaenger_id']);
    
    if ((bool) $valid) {
      $valid &= $this->validateMinuten($validateResult['minuten'], $validateResult['empfaenger_id']);
    }
    
    if (!(bool) $valid) {
      return false;
    }
    return $validateResult;
  }
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------

  /**
   * Es werden keine Buchungen bearbeitet, damit muss auch nichts aus der DB geladen werden.
   * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
   * 
   * @see JModelForm::loadFormData()
   */
  protected function loadFormData() {
    $data = JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array ());
    return $data;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Die verschenkte Zeit darf das vorhandene Guthaben nicht übersteigen.
   * Es kann Zeit maximal bis zur Erreichung des Stundensolls des Empfängers verschenkt werden.
   */
  private function validateMinuten($minuten, $empfaengerId) {
    if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
      $this->setError('Bitte die Zeit eingeben, die du verschenken möchtest.');
      return false;
    }
    if (!is_numeric($minuten)) {
      $this->setError('Im Feld Minuten sind nur Zahlen zulässig.');
      return false;
    }
    $minutenInt = intval($minuten);
    
    $saldo = ZeitbankCalc::getSaldo($this->user->id);
    
    if ($minutenInt > $saldo) {
      $this->setError('Du kannst maximal dein aktuelles Guthaben verschenken ('.$saldo.' Minuten).');
      return false;
    }
    
    $saldoEmpfaenger = ZeitbankCalc::getSaldo($empfaengerId);
    $sollEmpfaenger = ZeitbankCalc::getSollBewohner($empfaengerId);
    
    if ($saldoEmpfaenger >= $sollEmpfaenger) {
      $this->setError('Der Empfänger benötigt keine Stunden mehr.');
      return false;
    }
    else if ($saldoEmpfaenger + $minutenInt > $sollEmpfaenger) {
      $this->setError('Der Empfänger benötigt nur noch '.($sollEmpfaenger - $saldoEmpfaenger).' Minuten zur Erreichung des Stundensolls.');
      return false;
    }
    
    return true;
  }
  
}