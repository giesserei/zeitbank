<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model zum Erstellen und Bearbeiten eines Antrags für einen Stundentausch.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelStundentausch extends ZeitbankModelUpdJournalBase {
  
  public function __construct() {
    parent::__construct();
  }
  
  public function getEmpfaengerName($userId) {
    $query = "SELECT CONCAT(vorname, ' ', nachname) name
              FROM #__mgh_aktiv_mitglied
              WHERE userid = ".$userId;
    $this->db->setQuery($query);
    return $this->db->loadResult();
  }

  /**
   * @see JModelForm::getForm()
   */
  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.stundentausch', 'stundentausch', array (
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
   * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
   * 
   * @see JModelForm::loadFormData()
   */
  protected function loadFormData() {
    $data = JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array ());
    
    if (empty($data)) {
      $data = $this->getItem();
      $data->empfaenger_id = $data->belastung_userid;
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
  
  private function validateMinuten($minuten, $empfaengerId) {
    if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
      $this->setError('Bitte die Minuten eingeben.');
      return false;
    }
    if (!is_numeric($minuten)) {
      $this->setError('Im Feld Minuten sind nur Zahlen zulässig.');
      return false;
    }    
    return true;
  }
  
}