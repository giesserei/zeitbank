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
    $valid &= $this->validateBirthdate($validateResult['birthdate']);
    $valid &= $this->validateEmail($validateResult['email']);
    
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
  public function save($data) {
    $user = JFactory::getUser();
    $table = $this->getTable();
  
    try {
      // Daten in die Tabellen-Instanz laden
      $table->load($user->id);
      
      // Properties mit neuen Daten überschreiben 
      // ID und User-ID nicht überschreiben -> sicherstellen, dass diese nicht verändert werden
      if (!$table->bind($data, "id, userid")) {
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
      
      // Nun müssen wir noch den User in der Joomla-User in der Session aktualisieren
      // Sonst sieht man nach der E-Mail Änderung die alte E-Mail noch im Forum-Profil
      $this->reloadUserInSession($user->id);
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
      $data = $this->getItem($this->getState('angebot.id'));
    }
    
    return $data;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  

}