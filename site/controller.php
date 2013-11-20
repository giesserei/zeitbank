<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
class ZeitbankController extends JController {

  function display() {
    parent::display();
  }

  function detail() {
	global $mainframe;
	JRequest::setVar('view','detail');
    parent::display();
	$mainframe->close();
  } // datail

}
?>