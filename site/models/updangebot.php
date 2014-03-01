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
   * Liefert die Liste mit den Arbeitskategorien, für die der Benutzer Ämtli-Administrator ist.
   */
  public function getKategorien() {
    $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.id IN (SELECT a.kat_id FROM #__mgh_zb_x_kat_arbeitadmin AS a WHERE a.user_id = " . $this->user->id . ")
              ORDER BY k.bezeichnung";
    $this->db->setQuery($query);
    return $this->db->loadObjectList();
  }
  
  /**
   * Liefert die Liste mit den Arbeiten, für die der Benutzer Arbeiten ausschreiben darf. Dies sind alle aktiven Arbeiten,
   * die einer der Arbeitskategorie zugeordnet sind, für die der Benutzer als Ämtli-Administrator registriert ist.
   * 
   * Die Liste ist eine geschachtelte Liste von Arrays. In der ersten Dimension sind die Arbeitskategorien gelistet. 
   * in der zweiten Dimension sind die zugehörigen Arbeiten gelistet.
   */
  public function getArbeitsgattungen() {
    // Zunächst alle relevanten Arbeitskategorien selektieren
    $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.id IN (SELECT a.kat_id FROM #__mgh_zb_x_kat_arbeitadmin AS a WHERE a.user_id = " . $this->user->id . ")
              ORDER BY k.bezeichnung";
    $this->db->setQuery($query);
    $kategorien = $this->db->loadObjectList();
    
    // Für jede Kategorie nun die Arbeiten laden
    $liste = array();
    foreach ($kategorien as $kat) {
      $liste[$kat->bezeichnung] = array();
      
      $query = "SELECT a.*
                FROM #__mgh_zb_arbeit as a
                WHERE a.kategorie_id = ".$kat->id."
                  AND a.aktiviert = '1'
                ORDER BY a.kurztext";
      $this->db->setQuery($query);
      $arbeiten = $this->db->loadObjectList();
      
      $groupItems = array();
      foreach ($arbeiten as $arb) {
        $groupItems[$arb->id] = $arb->kurztext;
      }
      $liste[$kat->bezeichnung]['items'] = $groupItems;
    }
    
    return $liste;
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
    
    // Beschreibung extra filtern, da Form-Filterung des Editors mit Joomla2.5 nicht funktioniert
    $validateResult['beschreibung'] = JComponentHelper::filterText($validateResult['beschreibung']);
    
    $valid = 1;
    $valid &= $this->validateArtRichtung($validateResult['art'], $validateResult['richtung']);
    $valid &= $this->validateKategorie($validateResult['art'], $validateResult['arbeit_id']);
    $valid &= $this->validateRequiredFields($validateResult['art'], $validateResult['aufwand'], 
                       $validateResult['zeit'], $validateResult['anforderung'], $validateResult['beschreibung']);
    
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
  
  public function delete($id) {
    $table = $this->getTable();
    
    try {
      if (!$table->delete($id)) {
        $this->setError($table->getError());
        return false;
      }
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      $this->setError('Löschen fehlgeschlagen!');
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
   * User ein Ämtli-Administrator in der gewählten Kategorie sein. Ausserdem muss eine Arbeitsgattung gewählt worden sein.
   */
  private function validateKategorie($art, $arbeitId) {
    if ($art == 1 && $arbeitId <= 0) {
      $this->setError('Bitte wähle eine Arbeitsgattung aus');
      return false;
    }
    else if ($art == 1) {
      $query = sprintf(
          "SELECT count(*)
           FROM #__mgh_zb_x_kat_arbeitadmin ka
             JOIN #__mgh_zb_arbeit a ON ka.kat_id = a.kategorie_id
           WHERE a.id = %s AND ka.user_id = %s", mysql_real_escape_string($arbeitId), $this->user->id);
      $this->db->setQuery($query);
      $count = $this->db->loadResult();
      if ($count == 0) {
        $this->setError('Du bist kein Ämtli-Administrator für die gewählte Arbeitskategorie.');
        return false;
      }
    }
    return true;
  }
  
  /**
   * Wenn ein 'Arbeitsangebot' bearbeitet oder angelegt wird, müssen verschiedene Felder zwingend angefüllt werden.
   */
  private function validateRequiredFields($art, $aufwand, $zeit, $anforderung, $beschreibung) {
    if ($art != 1) {
      return true;
    }
    if (ZeitbankFrontendHelper::isBlank($beschreibung)) {
      $this->setError('Bitte beschreibe die Arbeit.');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($anforderung)) {
      $this->setError('Bitte erfasse die Anforderungen für die Arbeit.');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($zeit)) {
      $this->setError('Bitte gebe an, bis wann die Arbeit ausgeführt werden soll.');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($aufwand)) {
      $this->setError('Bitte gebe an, wie viel Aufwand für die Arbeit verbucht werden kann.');
      return false;
    }
    return true;
  }
}