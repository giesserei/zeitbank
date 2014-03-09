<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.modeladmin');
jimport('joomla.log.log');

/**
 * Model für die Ausführung eines Stundengeschenks.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelStundenGeschenk extends JModelAdmin {

  private $db;
  
  private $user;
  
  public function __construct() {
    parent::__construct();
    $this->db = JFactory::getDBO();
    $this->user = JFactory::getUser();
  }
  
  /**
   * @see JModel::getTable()
   */
  public function getTable($type = 'Journal', $prefix = 'Table', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
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
    
    // Kommentar darf nur 300 Zeichen haben
    $validateResult['kommentar'] = ZeitbankFrontendHelper::cropText($validateResult['kommentar'], 300, false);
    
    $valid = 1;
    $valid &= $this->validateEmpfaenger($validateResult['empfaenger_id']);
    $valid &= $this->validateMinuten($validateResult['minuten']);
    
    if (!(bool) $valid) {
      return false;
    }
    return $validateResult;
  }
  
  /**
   * Eigene Implementierung der save-Methode.
   * 
   * @return true, wenn das Speichern erfolgreich war, sonst false
   * 
   * @see JModelAdmin::save()
   */
  public function save($data, $id) {
    $user = JFactory::getUser();
    $table = $this->getTable();
  
    try {
      // Daten in die Tabellen-Instanz laden
      $table->load($id);
      
      // Properties mit neuen Daten überschreiben
      if (!$table->bind($data, 'id')) {
        $this->setError($table->getError());
        return false;
      }
  
      // Tabelle kann vor dem Speichern letzte Datenprüfung vornehmen
      if (!$table->check()) {
        $this->setError($table->getError());
        return false;
      }
  
      // Jetzt Daten speichern
      if (!$table->store()) {
        $this->setError($table->getError());
        return false;
      }
      
      // Kommentar speichern
      $this->saveComment($data);
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      $this->setError('Speichern fehlgeschlagen!');
      return false;
    }

    return true;
  }
  
  /**
   * Liefert alle aktiven Bewohner und das Gewerbe mit Ausnahme des angemeldeten Benutzers,
   * welche mit dem Like-Operator gefunden werden.
   */
  public function getEmpfaengerLike($search) {
    $searchMySql = mysql_real_escape_string($search);
    $query = "SELECT userid, vorname, nachname 
              FROM #__mgh_aktiv_mitglied 
              WHERE (vorname LIKE '%".$searchMySql."%' OR nachname LIKE '%".$searchMySql."%') 
                AND typ != 5
                AND userid != ".$this->user->id;
    $this->db->setQuery($query);
    return $this->db->loadObjectList();
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
   * Liefert true, wenn der Empfänger ein aktives Mitglied ist; sonst false.
   */
  private function validateEmpfaenger($empfaengerId) {
    if (!isset($empfaengerId)) {
      $this->setError('Bitte Empfänger auswählen');
      return false;
    }
    
    $query = "SELECT userid, vorname, nachname 
              FROM #__mgh_aktiv_mitglied 
              WHERE userid = ".mysql_real_escape_string($empfaengerId)."
                AND typ != 5  
                AND userid != ".$this->user->id;
    $this->db->setQuery($query);
    $count = $this->db->loadResult();
    
    if ($count == 0) {
      $this->setError('Der Empfänger ist nicht zulässig');
      return false;
    }
    
    return true;
  }
  
  /**
   * Die verschenkte Zeit darf das vorhandene Guthaben nicht übersteigen.
   */
  private function validateMinuten($minuten) {
    if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
      $this->setError('Bitte die Zeit eingeben, die du verschenken möchtest');
      return false;
    }
    if (!is_numeric($minuten)) {
      $this->setError('Im Feld Minuten sind nur Zahlen zulässig');
      return false;
    }
    $minutenInt = intval($minuten);
    
    $query = "SELECT COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend
                               WHERE gutschrift_userid = ".$this->user->id."), 0) - 
                     COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend
                               WHERE belastung_userid = ".$this->user->id."), 0)";
    $this->db->setQuery($query);
    $saldo = $this->db->loadResult();
    $saldoInt = intval($saldo);
    
    if ($minutenInt > $saldoInt) {
      $this->setError('Du kannst maximal dein aktuelles Guthaben verschenken ('.$saldoInt.' Minuten)');
      return false;
    }
    
    return true;
  }
  
  /**
   * Speichert den Kommentar, sofern ein Kommentar erfasst ist.
   */
  private function saveComment($data) {
    if (ZeitbankFrontendHelper::isBlank($data['kommentar'])) {
      return;
    }
    
    $query = "SELECT id
              FROM #__mgh_zb_journal
              WHERE cf_uid = '".$data['cf_uid']."'";
    $this->db->setQuery($query);
    $id = $this->db->loadResult();
    
    $query = "INSERT INTO #__mgh_zb_antr_kommentar (journal_id, text) VALUES (".$id.",'".$data['kommentar']."')";
    $this->db->setQuery($query);
    $this->db->query();
  }
}