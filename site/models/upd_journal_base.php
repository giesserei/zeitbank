<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

jimport('joomla.application.component.modeladmin');
jimport('joomla.log.log');

/**
 * Basisklasse für die Model-Klassen, mit denen Journal-Einträge erstellt oder bearbeitet werden können.
 * 
 * @author Steffen Förster
 */
abstract class ZeitbankModelUpdJournalBase extends JModelAdmin {
  
  protected $db;
  
  protected $user;
  
  public function __construct() {
    parent::__construct();
    $this->db = JFactory::getDBO();
    $this->user = JFactory::getUser();
  }
  
  /**
   * Liefert true, wenn der angemeldete Benutzer Erfasser des übergebenen Journaleintrags ist.
   * Weiterhin darf der Antrag noch nicht bestätigt sein.
   * -> Funktioniert nicht für Stundengeschenke, da der Antragssteller hier keine Gutschrift bekommt.
   */
  public function isEditAllowed($id) {
    $query = sprintf(
        "SELECT count(*) AS owner
         FROM #__mgh_zb_journal AS j
         WHERE j.id = %s AND j.gutschrift_userid = %s
           AND j.admin_del = 0
           AND j.datum_quittung = '0000-00-00'", mysql_real_escape_string($id), $this->user->id);
    $this->db->setQuery($query);
    $result = $this->db->loadObject();
    return $result->owner == 1;
  }
  
  /**
   * Liefert true, wenn der angemeldete Benutzer der Admin der Arbeitskategorie ist, welche im übergebenen 
   * Journaleintrag verwendet wird. 
   * Ausnahme: Beim Stundentausch muss der angemeldete Benutzer der Besitzer des Belastungskontos sein.
   */
  public function isArbeitAdmin($id) {
    $query = sprintf(
        "SELECT count(*) AS admin
         FROM #__mgh_zb_journal AS j JOIN #__mgh_zb_arbeit AS a ON j.arbeit_id = a.id
         WHERE j.id = %s AND (a.admin_id = %s OR (j.belastung_userid = %s AND a.id = %s))", 
           mysql_real_escape_string($id), $this->user->id, $this->user->id, ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $this->db->setQuery($query);
    $result = $this->db->loadObject();
    return $result->admin == 1;
  }
  
  /**
   * Liefert true, wenn der Journaleintrag zu einem Ämtli gehört und damit nicht zum privaten Stundentausch.
   */
  public function isJournalAemtli($id) {
    $query = sprintf(
        "SELECT count(*) AS aemtli
         FROM #__mgh_zb_journal j
         WHERE j.id = %s AND j.arbeit_id != %s",
        mysql_real_escape_string($id), ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $this->db->setQuery($query);
    $result = $this->db->loadObject();
    return $result->aemtli == 1;
  }
  
  /**
   * Liefert den Antrag.
   */
  public function getAntrag($id) {
    $query = sprintf(
        "SELECT journal.id, minuten, datum_antrag, kurztext, journal.kommentar_antrag AS text, journal.arbeit_id,
           (SELECT u.name FROM #__users u WHERE u.id = journal.belastung_userid) konto_belastung, 
           (SELECT u.name FROM #__users u WHERE u.id = journal.gutschrift_userid) konto_gutschrift 
    		 FROM #__mgh_zb_arbeit AS arbeit JOIN #__mgh_zb_journal AS journal ON journal.arbeit_id = arbeit.id
    		 WHERE journal.id = %s", mysql_real_escape_string($id));
    $this->db->setQuery($query);
    return $this->db->loadObject();
  }
  
  /**
   * @see JModel::getTable()
   *
   * @inheritdoc
   */
  public function getTable($type = 'Journal', $prefix = 'Table', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }
  
  /**
   * Eigene Implementierung der save-Methode.
   *
   * @param $data array Zu speichernde Daten
   * @return true, wenn das Speichern erfolgreich war, sonst false
   *
   * @see JModelAdmin::save()
   */
  public function save($data) {
    $id = $data['id'];
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
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      $this->setError('Speichern fehlgeschlagen!');
      return false;
    }
  
    return true;
  }
  
  /**
   * Prüft, ob das Antragsdatum korrekt gesetzt ist.
   */
  public function validateDatumAntrag($datumAntrag) {
    if (ZeitbankCalc::isBuchungGesperrt()) {
      $this->setError('Das Antragsdatum ist nicht korrekt!');
      return false;
    }
    
    if (strcmp($datumAntrag, date('Y-m-d')) == 0) {
      return true;
    }
    if (ZeitbankCalc::isLastYearAllowed()) {
      $lastYear = intval(date('Y')) - 1;
      if (strcmp($datumAntrag, $lastYear.'-12-31') == 0) {
        return true;
      }
    }
  
    $this->setError('Das Antragsdatum ist nicht korrekt!');
    return false;
  }
  
}