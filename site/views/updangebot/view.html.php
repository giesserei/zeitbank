<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * View-Klasse für das Edit-Formular.
 * 
 * @author Steffen Förster
 */
class ZeitbankViewUpdAngebot extends JView {

  protected $form;
  
  protected $menuId;
  
  protected $state;
  
  protected $art;

  /**
   * @see JView::display()
   */
  public function display($tpl = null) {
    $app = JFactory::getApplication();
    
    // Form holen für Aufbereitung des Formulars
    $this->state = $this->get('State');
    
    $this->form	= $this->get('Form');
    $this->setArt();

    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode('\n', $errors));
    }
    
    ZeitbankFrontendHelper::addComponentStylesheet();

    // Menü-Id wird in View im Form-Action gesetzt
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    
    parent::display($tpl);
  }
  
  protected function getKategorien() {
    $model = $this->getModel();
    return $model->getKategorien();
  }
  
  protected function getArbeitsgattungen() {
    $model = $this->getModel();
    return $model->getArbeitsgattungen();
  }
  
  protected function getId() {
    return (int) $this->state->get($this->getModel()->getName().'.id');
  }
  
  protected function isTauschView() {
    return $this->getArt() == 2;
  }
  
  protected function isArbeitView() {
    return $this->getArt() == 1;
  }
  
  protected function getArt() {
    return $this->art;
  }
  
  protected function isNew() {
    return $this->getId() == 0;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Speichert die Art des Eintrag für den Aufbau der View.
   */
  private function setArt() {
    if ($this->isNew()) {
      $app = JFactory::getApplication();
      $this->art = $app->getUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART);
    }
    else {
      $this->art = $this->form->getValue('art');
    }
  }
}