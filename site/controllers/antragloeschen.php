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
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------

  /**
   * Zeigt die Fehlermeldungen an, wenn das Löschen nicht funktioniert hat.
   */
  protected function perfomOnDeleteError() {
    $model = $this->getModel();
    $errors = $model->getErrors();
    foreach ($errors as $error) {
      $app = JFactory::getApplication();
      $app->enqueueMessage($error, 'warning');
    }

    // Zurück zum Formular
    $id = $this->getId();
    $this->redirectConfirmView($id);
  }

  protected function getViewName() {
    return "antragloeschen";
  }
  
  protected function isSaveDataInSession() {
    return false;
  }
  
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
   *
   * @param $id int
   */
  private function redirectConfirmView($id) {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=' . $this->getViewName() . '&layout=confirm&id='.$id.'&Itemid=' . $menuId, false)
    );
  }
}