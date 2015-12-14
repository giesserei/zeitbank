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
   * @inheritdoc
   */
  public function validate($form, $data, $group = NULL) {
    $validateResult = parent::validate($form, $data, $group);
    if ($validateResult === false) {
      return false;
    }
    
    $valid = 1;
    $valid &= $this->validateEmpfaenger($validateResult['empfaenger_id']);
    
    if ((bool) $valid) {
      $valid &= $this->validateMinuten($validateResult['minuten']);
    }
    if ((bool) $valid) {
      $valid &= $this->validateDatumAntrag($validateResult['datum_antrag']);
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
  
  private function validateMinuten($minuten) {
    if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
      JFactory::getApplication()->enqueueMessage('Bitte die Minuten eingeben.', 'warning');
      return false;
    }
    if (!is_numeric($minuten)) {
      JFactory::getApplication()->enqueueMessage('Im Feld Minuten sind nur Zahlen zulässig.', 'warning');
      return false;
    }  
    $minutenInt = intval($minuten);
    if ($minutenInt <= 0) {
      JFactory::getApplication()->enqueueMessage('Die Anzahl der Minuten muss grösser 0 sein.', 'warning');
      return false;
    }
    
    return true;
  }
  
  /**
   * Liefert true, wenn der Empfänger ein aktiver Bewohner oder Gewerbe ist; sonst false.
   * Auch darf dies nicht der angemeldete Benutzer sein.
   *
   * @param $empfaengerId int User-ID des Empfängers
   *
   * @return boolean
   */
  private function validateEmpfaenger($empfaengerId) {
    if (!isset($empfaengerId)) {
      JFactory::getApplication()->enqueueMessage('Bitte Empfänger auswählen', 'warning');
      return false;
    }
    
    $query = "SELECT userid, vorname, nachname
              FROM #__mgh_mitglied m
              WHERE m.typ IN (1,2) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
                AND userid = ".mysql_real_escape_string($empfaengerId)."
                AND userid != ".$this->user->id;
  
    $this->db->setQuery($query);
    $count = $this->db->loadResult();
  
    if ($count == 0) {
      JFactory::getApplication()->enqueueMessage('Der Empfänger ist nicht zulässig.', 'warning');
      return false;
    }
  
    return true;
  }
  
}