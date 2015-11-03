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
class ZeitbankViewEigenleistungen extends JViewLegacy {

  protected $form;
  
  protected $menuId;
  
  protected $state;

  /**
   * @see JView::display()
   */
  public function display($tpl = null) {
    $app = JFactory::getApplication();
    
    // Form holen für Aufbereitung des Formulars
    $this->state = $this->get('State');
    
    $this->form	= $this->get('Form');

    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode('\n', $errors));
    }
    
    ZeitbankFrontendHelper::addComponentStylesheet();

    // Menü-Id wird in View im Form-Action gesetzt
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    
    parent::display($tpl);
  }
  
  protected function getId() {
    return (int) $this->state->get($this->getModel()->getName().'.id');
  }
  
  protected function isNew() {
    return $this->getId() == 0;
  }
  
  protected function getArbeitsgattungen() {
    $model = $this->getModel();
    return $model->getArbeitsgattungen();
  }

}