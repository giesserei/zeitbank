<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
class ZeitbankController extends JController {

  public function execute($task) {
    return parent::execute($task);
  }
  
  function display() {
    parent::display();
  }

  function detail() {
	  global $mainframe;
	  JRequest::setVar('view','detail');
    parent::display();
	  $mainframe->close();
  }

}
?>