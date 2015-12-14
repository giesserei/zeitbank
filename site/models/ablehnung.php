<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model zum Ablehnen von Anträgen.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelAblehnung extends ZeitbankModelUpdJournalBase {
  
  public function __construct() {
    parent::__construct();
  }

  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.ablehnung', 'ablehnung', array (
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
   * @inheritdoc
   */
  public function validate($form, $data, $group = NULL) {
    $validateResult = parent::validate($form, $data, $group);
    if ($validateResult === false) {
      return false;
    }
    
    $valid = 1;
    $valid &= $this->validateKommentar($validateResult['kommentar_ablehnung']);
    
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
   * Es muss eine Begründung eingegeben werden.
   */
  private function validateKommentar($kommentar) {
    if (ZeitbankFrontendHelper::isBlank($kommentar)) {
      JFactory::getApplication()->enqueueMessage('Bitte begründe die Ablehnung des Antrags.', 'warning');
      return false;
    }
    
    return true;
  }
  
}