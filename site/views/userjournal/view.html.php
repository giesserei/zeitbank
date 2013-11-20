<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewUserjournal extends JView {
  function display($tpl = null) {
    $model =& $this->getModel();
    $journal = $model->getUserJournal();

 	$pagination =& $this->get('Pagination');
 
	$this->assignRef('pagination', $pagination); 
    $this->assignRef('journal',$journal);
    
    parent::display($tpl);
  }
}
?> 
