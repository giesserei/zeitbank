<?php
/*
 * Created on 27.12.2010
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport ('joomla.application.component.controller');

class ZeitbankController extends JControllerLegacy {
	function display() {
		parent::display();
	}

	function __construct() {
		parent::__construct();
		$this->registerTask( 'add','edit' );
		$this->registerTask( 'change_status','change_status' );
	}

	function edit() {
		JRequest::setVar( 'view', 'buchung');
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'hidemainmenu', 1);
		parent::display();
	}	

	function change_status() {
		JRequest::setVar( 'view', 'status');
		JRequest::setVar( 'layout', 'form');
		// JRequest::setVar( 'hidemainmenu', 1);
		$input = JFactory::getApplication()->input;
		$view = $input->get('view', $this->default_view);
		ZeitbankHelper::addSubmenu($view);
		
		parent::display();
	}
	
	function save() {
		$model = $this->getModel('status');
		$data = JRequest::get( 'post' );
		
		// Bestätigung des Statuswechsels
		if($data['ok_status'] == 'ok'):			
			switch($model->store('ok')):
			case 'Jahresbudget_verteilt':
				$msg = 'Jahresbudget wurde auf alle verteilt.';
				$this->setRedirect('index.php?option=com_zeitbank&controller=zeitbank&task=change_status',$msg);
				$this->redirect();
				break;
			default:
				$msg = 'Neuer Status nicht gespeichert.';
				break;
			endswitch;
		else:
		// Statusänderung beantragt, rückfragen
			switch($data['status']):
				case 1: // Status 1: Neues Budget einreichen
					break;
				case 2: // Status 2: Jahresbudget verteilen
					$msg="Buchungsdaten prüfen!";
					$this->setRedirect('index.php?option=com_zeitbank&controller=zeitbank&task=change_status&act=check&status=2',$msg);
					$this->redirect();
					break;
				case 3: // Status 3: Nachträge einreichen
					break;
				case 4: // Status 4: Nachträge buchen
					break;
				case 5: // Status 5: Jahresabschluss
					break;
			endswitch; 		
		endif;
		$this->setRedirect('index.php?option=com_zeitbank&controller=zeitbank&task=change_status',$msg);
		$this->redirect();
	}
	
	function remove() {
		$model = $this->getModel('journal');
		if( $model->delete() ):
			$msg = 'Löschen war erfolgreich';
		else:
			$msg = 'Fehler beim Löschen';
		endif;
		$this->setRedirect('index.php?option=com_zeitbank&controller=zeitbank&view=journal',$msg);
		$this->redirect();
	}

	function cancel() {
		$msg = 'Aktion abgebrochen';
		$this->setRedirect( 'index.php?option=com_zeitbank&controller=zeitbank&view=journal',$msg,'message' );
		$this->redirect();
	}
}


?>
