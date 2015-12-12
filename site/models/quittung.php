<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model zum Quittieren von Anträgen.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelQuittung extends ZeitbankModelUpdJournalBase {
  
  public function __construct() {
    parent::__construct();
  }

  /**
   * @see JModelForm::getForm()
   *
   * @inheritdoc
   */
  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.quittung', 'quittung', array (
        'control' => 'jform',
        'load_data' => $loadData 
    ));
    
    if (empty($form)) {
      return false;
    }
    
    return $form;
  }
  
  /**
   * Prüft, ob die Eingaben korrekt sind. Validierungsmeldungen werden im Model gespeichert.
   * 
   * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
   * 
   * @see JModelForm::validate()
   *
   * @inheritdoc
   */
  public function validate($form, $data, $group = NULL) {
    $validateResult = parent::validate($form, $data, $group);
    if ($validateResult === false) {
      return false;
    }
    
    $valid = 1;
    $valid &= $this->validateMinuten($validateResult['id']);
    
    if (!(bool) $valid) {
      return false;
    }
    return $validateResult;
  }
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------

  /**
   * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
   * 
   * @see JModelForm::loadFormData()
   */
  protected function loadFormData() {
    $data = JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array ());
    
    if (empty($data)) {
      $data = $this->getItem();
    }
    else {
      // ID im State setzen, damit diese von der View ausgelesen werden kann
      $this->state->set($this->getName().'.id', $data['id']);
    }
    
    return $data;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Beim privaten Stundentausch muss ein entsprechendes Guthaben vorhanden sein.
   * Sonst gibt es keine weitere Validierung.
   *
   * @param $journalId int ID der Buchung
   *
   * @return boolean True, wenn die Validierung erfolgreich war
   */
  private function validateMinuten($journalId) {
    $buchung = $this->getItem($journalId);
    
    if ($buchung->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH) {
      $saldo = ZeitbankCalc::getSaldo($this->user->id);
      
      if ($buchung->minuten > $saldo) {
        JFactory::getApplication()->enqueueMessage(
            'Der Stundentausch übersteigt dein aktuelles Guthaben ('.$saldo.' Minuten).', 'warning');
        return false;
      }
    }
    
    return true;
  }
  
}