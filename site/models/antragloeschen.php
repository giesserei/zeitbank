<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

jimport('joomla.log.log');

/**
 * Model für die Ausführung eines Stundengeschenks.
 *
 * @author Steffen Förster
 */
class ZeitbankModelAntragLoeschen extends ZeitbankModelUpdJournalBase {

  public function __construct() {
    parent::__construct();
  }
 
  /**
   * @see JModelForm::getForm()
   */
  public function getForm($data = array(), $loadData = true) {
    return false;
  }
  
  /**
   * Löscht den übergebenen Antrag aus der Datenbank.
   */
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
}

