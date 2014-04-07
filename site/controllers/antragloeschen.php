<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdJournalBase', JPATH_COMPONENT . '/controllers/upd_journal_base.php');

/**
 * Controller zum Löschen von Anträgen.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerAntragLoeschen extends ZeitbankControllerUpdJournalBase {
  
  /**
   * Führt nach ein paar Vorarbeiten einen Redirect auf die View zur Bestätigung der Antragslöschung durch.
   */
  public function confirmDelete() {
    $app = JFactory::getApplication();
  
    if (!ZeitbankAuth::checkAuthZeitbank()) {
      return false;
    }
  
    // Daten in der Session löschen -> alte Daten
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, null);
  
    // Menü-Id in der User-Session speichern
    $jinput = $app->input;
    $menuId = $jinput->get("Itemid", "0", "INT");
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $menuId);
  
    $id = $this->getId();
  
    if (empty($id)) {
      JFactory::getApplication()->enqueueMessage('Antrag nicht gefunden.','warning');
      return false;
    }
    if (!$this->isEditAllowed($id)) {
      return false;
    }
  
    $this->redirectConfirmView($id);
  
    return true;
  }
  
  /**
   * Löscht einen Eintrag.
   */
  public function delete() {
    $app = JFactory::getApplication();
    
    if (!ZeitbankAuth::checkAuthZeitbank()) {
      return false;
    }
  
    $id = $this->getId();
  
    // Prüfen, ob der User den Antrag löschen darf
    if (!$this->isEditAllowed($id)) {
      return false;
    }
  
    // Eintrag löschen
    $model = $this->getModel();
    if ($model->delete($id)) {
      $this->redirectSuccessView();
      return true;
    }
    else {
      // Fehlermeldung dem Benutzer anzeigen
      $errors = $model->getErrors();
      foreach ($errors as $error) {
        $app->enqueueMessage($error, 'warning');
      }
    
      // Zurück zum Formular
      $this->redirectConfirmView($id);
    
      return false;
    }
    return false;
  }
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  /**
   * @see ZeitbankControllerUpdJournalBase::getViewName()
   */
  protected function getViewName() {
    return "antragloeschen";
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::saveDataInSession()
   */
  protected function saveDataInSession() {
    return false;
  }
  
  /**
   * @see ZeitbankControllerUpdJournalBase::filterFormFields()
   */
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    return $dataAllowed;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Auf die Confirm-View weiterleiten.
   */
  private function redirectConfirmView($id) {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=' . $this->getViewName() . '&layout=confirm&id='.$id.'&Itemid=' . $menuId, false)
    );
  }
}