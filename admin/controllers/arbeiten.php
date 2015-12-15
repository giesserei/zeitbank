<?php

defined('_JEXEC') or die('Restricted access');

jimport ('joomla.application.component.controller');

class ZeitbankControllerArbeiten extends JControllerLegacy {
	function display() {
		parent::display();
	}

	function __construct() {
		parent::__construct();
		JRequest::setVar('view', 'arbeiten');
		
		$this->registerTask( 'add','edit' );
		$this->registerTask('orderup','order');
    	$this->registerTask('orderdown','order');
	}

	function edit() {
		JRequest::setVar( 'view', 'arbeit');
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'hidemainmenu', 1);
		parent::display();
	}	

	function save() {
		$model = $this->getModel('arbeit');

		if( $model->store() ):
			$msg = 'Speichern war erfolgreich';
		else:
			$msg = 'Fehler beim Speichern der Kategorie';
		endif;
		
		$this->setRedirect('index.php?option=com_zeitbank&controller=arbeiten',$msg);
		$this->redirect();
	}
	
	function remove() {
		$model = $this->getModel('journal');
		if( $model->delete() ):
			$msg = 'Löschen war erfolgreich';
		else:
			$msg = 'Fehler beim Löschen';
		endif;
		$this->setRedirect('index.php?option=com_zeitbank&controller=arbeiten',$msg);
		$this->redirect();
	}

	function cancel() {
		$msg = 'Aktion abgebrochen';
		$this->setRedirect( 'index.php?option=com_zeitbank&controller=arbeiten',$msg,'message' );
		$this->redirect();
	}


  function order()
  {
    $cid = JRequest::getVar('cid', array(), 'post', 'array');

    // Direction
    $dir  = 1;
    $task = JRequest::getCmd('task');
    if($task == 'orderup')
    {
      $dir = -1;
    }

    if(isset($cid[0]))
    {
	  $path = JPATH_ADMINISTRATOR.DS."components".DS."com_zeitbank".DS."tables";
	  JTable::addIncludePath($path);
      $tabelle = & JTable::getInstance('arbeiten','Table');
      
      $tabelle->load((int)$cid[0]);
      $tabelle->move($dir,'kategorie_id ='.$tabelle->kategorie_id);
      $tabelle->reorder('kategorie_id ='.$tabelle->kategorie_id);
    }
	$msg = 'Reihenfolge geändert';
    $this->setRedirect('index.php?option=com_zeitbank&controller=arbeiten',$msg,'message');
  } // order
  
   	
  function saveOrder()
  {
    $cid    = JRequest::getVar('cid', array(0), 'post', 'array');
    $ordering  = JRequest::getVar('ordering', array (0), 'post', 'array'); 
    
    // Create and load the categories table object
	$path = JPATH_ADMINISTRATOR.DS."components".DS."com_zeitbank".DS."tables";
	JTable::addIncludePath($path);
    $row = & JTable::getInstance('arbeiten', 'Table');

    // Update the ordering for items in the cid array
    for($i = 0; $i < count($cid); $i ++)
    {
      $row->load((int)$cid[$i]);
      if($row->ordering != $ordering[$i])
      {
        $row->ordering = $ordering[$i];
        $cat = $row->kategorie_id;
        if(!$row->store())
        {
          JError::raiseError( 500, $this->_db->getErrorMsg() );
          return false;
        }
      }
    }

    // $row->reorderAll();
    $row->reorder('kategorie_id ='.$cat);
    
	$msg = 'Reihenfolge geändert';
    $this->setRedirect('index.php?option=com_zeitbank&controller=arbeiten',$msg,'message');
      } // saveOrder	
}


?>
