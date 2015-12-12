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
   *
   * @inheritdoc
   */
  public function getTable($type = 'Marketplace', $prefix = 'ZeitbankTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * @see JModelForm::getForm()
   *
   * @inheritdoc
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
   *
   * @inheritdoc
   */
  public function validate($form, $data, $group = NULL) {
    $validateResult = parent::validate($form, $data, $group);
    if ($validateResult === false) {
      return false;
    }
    
    // Beschreibung und Anforderung extra filtern, da Form-Filterung des Editors mit Joomla2.5 nicht funktioniert
    $validateResult['beschreibung'] = JComponentHelper::filterText($validateResult['beschreibung']);
    $validateResult['anforderung'] = JComponentHelper::filterText($validateResult['anforderung']);
    
    // Anforderung darf nur 255 Zeichen haben
    $validateResult['anforderung'] = ZeitbankFrontendHelper::cropText($validateResult['anforderung'], 255, false);
    
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
   *
   * @inheritdoc
   */
  public function save($data, $id) {
    $table = $this->getTable();

    try {
      // Daten in die Tabellen-Instanz laden
      $table->load($id);
      
      // Properties mit neuen Daten überschreiben
      if (!$table->bind($data, 'id')) {
        JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
        return false;
      }
  
      // Tabelle kann vor dem Speichern letzte Datenprüfung vornehmen
      if (!$table->check()) {
        JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
        return false;
      }
  
      // Jetzt Daten speichern
      if (!$table->store()) {
        JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
        return false;
      }
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      JFactory::getApplication()->enqueueMessage('Speichern fehlgeschlagen!', 'error');
      return false;
    }

    return true;
  }
  
  public function delete($id) {
    $table = $this->getTable();
    
    try {
      if (!$table->delete($id)) {
        JLog::add($table->getError(), JLog::ERROR);
        JFactory::getApplication()->enqueueMessage('Löschen fehlgeschlagen!', 'error');
        return false;
      }
    }
    catch (Exception $e) {
      JLog::add($e->getMessage(), JLog::ERROR);
      JFactory::getApplication()->enqueueMessage('Löschen fehlgeschlagen!', 'error');
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
   *
   * @param $art int Arbeitsangebot/Tausch
   * @param $richtung int Bieten/Suchen
   *
   * @return boolean
   */
  private function validateArtRichtung($art, $richtung) {
    if ($art == 2 && $richtung != 1 && $richtung != 2) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte treffe eine Auswahl beim Feld "Suche / Biete"', 'warning');
      return false;
    }
    return true;
  }
  
  /**
   * Liefert true, wenn 'Stundentausch' gewählt wurde. Wurde 'Arbeitsangebot' gewählt, so muss der 
   * User ein Ämtli-Administrator in der gewählten Kategorie sein. Ausserdem muss eine Arbeitsgattung gewählt worden
   * sein.
   *
   * @param $art int Arbeitsangebot/Tausch
   * @param $arbeitId int ID der Arbeitsgattung
   *
   * @return boolean
   */
  private function validateKategorie($art, $arbeitId) {
    if ($art == 1 && $arbeitId <= 0) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte wähle eine Arbeitsgattung aus', 'warning');
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
        JFactory::getApplication()->enqueueMessage(
            'Du bist kein Ämtli-Administrator für die gewählte Arbeitskategorie.', 'warning');
        return false;
      }
    }
    return true;
  }
  
  /**
   * Wenn ein 'Arbeitsangebot' bearbeitet oder angelegt wird, müssen verschiedene Felder zwingend angefüllt werden.
   *
   * @param $art int Arbeitsangebot/Tausch
   * @param $aufwand string Zeitaufwand
   * @param $zeit string Ausführungszeit
   * @param $anforderung string Anforderungen
   * @param $beschreibung string Beschreibung der Tätigkeit
   *
   * @return boolean
   */
  private function validateRequiredFields($art, $aufwand, $zeit, $anforderung, $beschreibung) {
    if ($art != 1) {
      return true;
    }
    if (ZeitbankFrontendHelper::isBlank($beschreibung)) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte beschreibe die Arbeit.', 'warning');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($anforderung)) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte erfasse die Anforderungen für die Arbeit.', 'warning');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($zeit)) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte gebe an, bis wann die Arbeit ausgeführt werden soll.', 'warning');
      return false;
    }
    if (ZeitbankFrontendHelper::isBlank($aufwand)) {
      JFactory::getApplication()->enqueueMessage(
          'Bitte gebe an, wie viel Aufwand für die Arbeit verbucht werden kann.', 'warning');
      return false;
    }
    return true;
  }
}