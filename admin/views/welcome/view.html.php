<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * Welcome-Seite für die Zeitbank.
 * 
 * @author Steffen Förster
 */
class ZeitbankViewWelcome extends JViewLegacy {
  
 	public function display($tpl = null) {
 		JToolBarHelper::title('Zeitbank Giesserei');
 		
 		$user = JFactory::getUser();
 		$assetname = 'com_zeitbank';
 		
 		if ($user->authorise('core.manage', $assetname)) {
 		  JToolBarHelper::preferences('com_zeitbank');
 		}
 		
 		parent::display($tpl);
 	}
}