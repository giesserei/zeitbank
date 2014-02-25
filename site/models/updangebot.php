<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.modeladmin');
jimport('joomla.log.log');

/**
 * Model zum Editieren eines Angebots.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelUpdAngebot extends JModelAdmin {

  private $db;
  
  private $user;
  
  public function __construct() {
    parent::__construct();
    $this->db = JFactory::getDBO();
    $this->user = JFactory::getUser();
  }
  
  /**
   * Liefert true, wenn der angemeldete Benutzer Erfasser des übergebenen Angebotes ist.
   */
  public function isOwner($id) {
    $query = sprintf(
        "SELECT count(*) as owner
         FROM #__mgh_zb_market_place as m
		     WHERE m.id = %s AND m.userid", mysql_real_escape_string($id), $this->user->id);
    $this->db->setQuery($query);
    $result = $this->db->loadObject();
    return $result->owner == 1;
  }
  
  /**
   * Liefert die Liste mit den Arbeitskategorien.
   */
  public function getKategorien() {
    $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.bezeichnung NOT IN ('Privat')
              ORDER BY k.bezeichnung";
    $this->db->setQuery($query);
    return $this->db->loadObjectList();
  }
  
  /**
   * @see JModel::getTable()
   */
  public function getTable($type = 'Marketplace', $prefix = 'ZeitbankTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * @see JModelForm::getForm()
   */
  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.updangebot', 'updangebot', array (
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
    $valid &= $this->validateArtRichtung($validateResult['art'], $validateResult['richtung']);
    $valid &= $this->validateKategorie($validateResult['art'], $validateResult['kategorie_id']);
    
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
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      $this->setError('Speichern fehlgeschlagen!');
      return false;
    }

    return true;
  }
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------

  /**
   * @see JModelForm::loadFormData()
   */
  protected function loadFormData() {
    $data = JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, array ());
    
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
   * Liefert true, wenn Suche oder Biete beim Stundentausch gewählt wurde; sonst false.
   * True wird auch geliefert, wenn Arbeitsangebot gewählt wurde.
   * Die Fehlermeldung wird im Model gespeichert.
   */
  private function validateArtRichtung($art, $richtung) {
    if ($art == 2 && $richtung != 1 && $richtung != 2) {
      $this->setError('Bitte treffe eine Auswahl beim Feld "Suche / Biete"');
      return false;
    }
    return true;
  }
  
  /**
   * Liefert true, wenn 'Stundentausch' gewählt wurde. Wurde 'Arbeitsangebot' gewählt, so muss der 
   * User eine Berechtigung für ein Ämtli in der gewählten Kategorie haben. Ausserdem muss eine 
   * Kategorie gewählt worden sein.
   */
  private function validateKategorie($art, $kategorieId) {
    if ($art == 1 && $kategorieId <= 0) {
      $this->setError('Bitte wähle eine Arbeitskategorie aus');
      return false;
    }
    else if ($art == 1) {
      $query = sprintf(
          "SELECT count(*)
           FROM #__mgh_zb_arbeit AS a 
           WHERE a.kategorie_id = %s AND a.admin_id = %s", mysql_real_escape_string($kategorieId), $this->user->id);
      $this->db->setQuery($query);
      $count = $this->db->loadResult();
      if ($count == 0) {
        $this->setError('Du hast keine Berechtigung, diese Arbeitskategorie auszuwählen');
        return false;
      }
    }
    return true;
  }
}