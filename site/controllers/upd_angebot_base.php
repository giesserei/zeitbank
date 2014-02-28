<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.controllerform');

/**
 * Basis-Klasse für die Controller zum Editieren eines Angebots im Marktplatz.
 *
 * @author Steffen Förster
 */
abstract class ZeitbankControllerUpdAngebotBase extends JControllerForm {
  
  /**
   * Führt nach ein paar Vorarbeiten einen Redirect auf die View durch, welche das Formular anzeigt.
   */
  public function edit() {
    $app = JFactory::getApplication();
    
    if (!ZeitbankFrontendHelper::checkAuthMarket()) {
      return false;
    }
    
    // Daten in der Session löschen -> alte Daten
    $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, null);
    $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, null);
    
    $id = $this->getId();
    if ($id == 0) {
      $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, $this->getArt());
    }
    
    if (!$this->isEditAllowed($id)) {
      return false;
    }
    
    $this->redirectEditView($id);
    
    return true;
  }
  
  /**
   * Speichert die Formulardaten des Profils in der Datenbank.
   */
  public function save() {
    if (!ZeitbankFrontendHelper::checkAuthMarket()) {
      return false;
    }
    
    $app = JFactory::getApplication();
    
    // Form-Token prüfen -> Token wird in Template gesetzt
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
    
    $formData = $this->getFormData();
    $id = $formData['id'];
    
    // Prüfen, ob der User das Angebot bearbeiten darf
    if (!$this->isEditAllowed($id)) {
      return false;
    }

    // Validierung -> Validierungsmeldungen werden direkt ausgegeben
    $validateResult = $this->validateData($formData, $id);
    if ($validateResult === false) {
      return false;
    }
    
    // Daten Speichern
    if ($this->processSave($validateResult, $id)) {
      // Daten in der Session löschen
      $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, null);
      $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, null);
      
      $this->redirectSuccessView();
      return true;
    }
    
    return false;
  }
  
  /**
   * Löscht einen Eintrag.
   */
  public function delete() {
    if (!ZeitbankFrontendHelper::checkAuthMarket()) {
      return false;
    }
    
    $id = $this->getId();
    
    // Prüfen, ob der User das Angebot löschen darf
    if (!$this->isEditAllowed($id)) {
      return false;
    }
    
    // Eintrag löschen
    $model = $this->getModel();
    if ($model->delete($id)) {
      $this->redirectSuccessView();
      return true;
    }
    
    return false;
  }
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  /**
   * Liefert ein Array mit den Formdaten zurück, die gespeichert werden dürfen.
   * -> Verhindert, dass nicht zulässige Tabellen-Felder verändert werden.
   */
  abstract protected function filterFormFields($data);
  
  /**
   * Liefert true, wenn bei einer fehlgeschlagenen Validierung oder Speicherung die Daten
   * für eine Anzeige in der Session gespeichert werden sollen.
  */
  abstract protected function saveDataInSession();
  
  /**
   * Liefert den Namen der View.
   */
  abstract protected function getViewName();
  
  /**
   * Führt einen Redirect auf die Seite durch, die nach dem Speichern angezeigt werden soll.
   */
  abstract protected function redirectSuccessView();
  
  /**
   * Auf die Edit-View weiterleiten.
   */
  protected function redirectEditView($id) {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=' . $this->getViewName() . '&layout=edit&id='.$id.'&Itemid=' . $menuId, false)
    );
  }
  
  /**
   * Daten vor dem Speichern ggf. in die DB-Darstellung umformatieren.
   */
  protected function formatData($data) {
    return $data;
  }
  
  /**
   * Speichert die Daten. Tritt ein Fehler auf, werden die Eingaben in der Session gespeichert,
   * damit diese erneut angezeigt werden können.
   *
   * Fehlermeldungen werden direkt angezeigt.
   *
   * @return boolean True, wenn das Speichern erfolgreich war
   */
  protected function processSave($data, $id) {
    $app = JFactory::getApplication();
    
    $model = $this->getModel();
  
    $data = $this->formatData($data);
  
    // Fehlermeldung dem Benutzer anzeigen
    if (!$model->save($data, $id)) {
      $errors = $model->getErrors();
      foreach ($errors as $error) {
        $app->enqueueMessage($error, 'warning');
      }
  
      // Daten in der Session speichern
      if ($this->saveDataInSession()) {
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, $data);
      }
  
      // Zurück zum Formular
      $this->redirectEditView($id);
  
      return false;
    }
  
    return true;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Holt die Formulardaten des Profilformulars aus dem JInput.
   */
  private function getFormData() {
    $app = JFactory::getApplication();
    $input = $app->input;
    $model = $this->getModel();
    $form = $model->getForm(array(), false);
    $data = $input->get($form->getFormControl(), '', 'array');
    
    return $this->filterFormFields($data);
  }
  
  /**
   * Prüft, ob die Eingaben korrekt sind. Sind die Eingaben nicht korrekt, werden die 
   * Eingaben in der Session gespeichert, damit diese erneut angezeigt werden können.
   * 
   * Validierungsmeldungen werden direkt ausgegeben.
   * 
   * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
   */
  private function validateData($data, $id) {
    $app = JFactory::getApplication();
    $model = $this->getModel();
    $form = $model->getForm($data, false);
    
    $validateResult = $model->validate($form, $data);
    
    // Nur die ersten drei Fehler dem Benutzer anzeigen
    if ($validateResult === false) {
      $errors = $model->getErrors();
    
      for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
        if ($errors[$i] instanceof Exception) {
          $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
        }
        else {
          $app->enqueueMessage($errors[$i], 'warning');
        }
      }
    
      // Daten in der Session speichern
      if ($this->saveDataInSession()) {
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, $data);
      }
    
      // Zurück zum Formular
      $this->redirectEditView($id);
    
      return false;
    }
    
    return $validateResult;
  }
  
  /**
   * Liefert die ID des Angebots, welche bearbeitet werden soll.
   */
  private function getId() {
    $app = JFactory::getApplication();
    $input = $app->input;
    return $input->get("id", "0");
  }
  
  /**
   * Liefert die Art des Angebots, welches erstellt werden soll.
   */
  private function getArt() {
    $jinput = JFactory::getApplication()->input;
    return $jinput->get("art", 2);
  }
  
  /**
   * Liefert true, wenn der Benutzer den Eintrag bearbeiten darf. Wenn ID=0, wird immer true geliefert.
   */
  private function isEditAllowed($id) {
    if ($id == 0) {
      return true;
    }
    
    $model = $this->getModel();
    if(!$model->isOwner($id)) {
      JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.','warning');
      return false;
    }
    return true;
  }
  
}