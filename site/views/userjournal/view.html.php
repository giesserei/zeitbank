<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewUserjournal extends JView {
  
  protected $journal;
  
  function display($tpl = null) {
    $model =& $this->getModel();
    $this->journal = $model->getUserJournal();
    
    parent::display($tpl);
  }
}
?> 
