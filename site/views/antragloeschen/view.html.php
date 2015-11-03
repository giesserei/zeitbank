<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * View-Klasse für das Confirm-Formular.
 * 
 * @author Steffen Förster
 */
class ZeitbankViewAntragLoeschen extends JViewLegacy {

  protected $menuId;
  
  protected $state;
  
  protected $antrag;

  /**
   * @see JView::display()
   */
  public function display($tpl = null) {
    $app = JFactory::getApplication();
    
    $this->state = $this->get('State');

    if (count($errors = $this->get('Errors'))) {
      throw new Exception(implode('\n', $errors));
    }
    
    ZeitbankFrontendHelper::addComponentStylesheet();

    // Menü-Id wird in View im Form-Action gesetzt
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    
    $this->antrag = $this->getModel()->getAntrag($this->getId());
    
    parent::display($tpl);
  }
  
  protected function getId() {
    return (int) $this->state->get($this->getModel()->getName().'.id');
  }

}