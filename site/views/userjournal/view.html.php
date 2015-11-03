<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

class ZeitbankViewUserjournal extends JViewLegacy {
  
  protected $journal;
  
  function display($tpl = null) {
    $model = $this->getModel();
    $this->journal = $model->getUserJournal();
    
    ZeitbankFrontendHelper::addComponentStylesheet();
    
    parent::display($tpl);
  }
}
?> 
