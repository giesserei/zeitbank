<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model zum Erstellen und Bearbeiten eines Antrags für Freiwilligenarbeit.
 * 
 * @author Steffen Förster
 */
class ZeitbankModelFreiwilligenarbeit extends ZeitbankModelUpdJournalBase {
  
  public function __construct() {
    parent::__construct();
  }
  
  /**
   * Liefert das Zeitkonto für die Freiwilligenarbeit.
   */
  public function getZeitkonto() {
    $query = "SELECT k.user_id
              FROM #__mgh_zb_kategorie k
              WHERE k.id = ".ZeitbankConst::KATEGORIE_ID_FREIWILLIG;
    $this->db->setQuery($query);
    return $this->db->loadResult();
  }
  
  /**
   * Liefert die Liste mit den Arbeiten.
   *
   * Die Liste ist eine geschachtelte Liste von Arrays. In der ersten Dimension sind die Arbeitskategorien gelistet.
   * in der zweiten Dimension sind die zugehörigen Arbeiten gelistet.
   */
  public function getArbeitsgattungen() {
    // Zunächst die Arbeitskategorie selektieren
    $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.id = ".ZeitbankConst::KATEGORIE_ID_FREIWILLIG."
              ORDER BY k.ordering";
    $this->db->setQuery($query);
    $kategorien = $this->db->loadObjectList();
  
    // Für jede Kategorie nun die Arbeiten laden
    $liste = array();
    
    // Hint hinzufügen
    $liste[""] = array();
    $hintItems = array();
    $hintItems[-1] = "---- Arbeitsgattung auswählen ----";
    $liste[""]['items'] = $hintItems;
    
    foreach ($kategorien as $kat) {
      $liste[$kat->bezeichnung] = array();
  
      $query = "SELECT a.*
                FROM #__mgh_zb_arbeit as a
                WHERE a.kategorie_id = ".$kat->id."
                  AND a.aktiviert = '1'
                ORDER BY a.ordering";
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
   * @see JModelForm::getForm()
   */
  public function getForm($data = array(), $loadData = true) {
    $form = $this->loadForm('com_zeitbank.freiwilligenarbeit', 'freiwilligenarbeit', array (
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
    $valid &= $this->validateArbeit($validateResult['arbeit_id']);
    
    if ((bool) $valid) {
      $valid &= $this->validateMinuten($validateResult['minuten']);
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
   * Die gewählte Arbeitskategorie muss zur Kategorie Freiwilligenarbeit gehören.
   */
  private function validateArbeit($arbeitId) {
    if (empty($arbeitId) || $arbeitId <= 0) {
      $this->setError('Bitte eine Arbeitskategorie auswählen.');
      return false;
    }
    
    $query = "SELECT count(*)
              FROM #__mgh_zb_arbeit
              WHERE id = ".mysql_real_escape_string($arbeitId)."
                AND aktiviert = 1
                AND kategorie_id = ".ZeitbankConst::KATEGORIE_ID_FREIWILLIG;
    $this->db->setQuery($query);
    $count = $this->db->loadResult();
    
    if ($count == 0) {
      $this->setError('Die Arbeitskategorie ist nicht zulässig.');
      return false;
    }
    
    return true;
  }
  
  private function validateMinuten($minuten) {
    $minutenToValidate = $minuten;
    if (!isset($minutenToValidate) || ZeitbankFrontendHelper::isBlank($minutenToValidate)) {
      $minutenToSave = 0;
    }
    if (!is_numeric($minutenToValidate)) {
      $this->setError('Im Feld Minuten sind nur Zahlen zulässig.');
      return false;
    }    
    
    // Bei einer Arbeitskategorie mit einer Pauschale, die Pauschale holen
    if ($minutenToValidate <= 0) {
      $this->setError('Die Anzahl der Minuten muss grösser 0 sein.');
      return false;
    }
    
    return true;
  }
  
}