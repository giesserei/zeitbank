<?php
/*
 * Created on 27.12.2010
 *
 */
 defined('_JEXEC') or die('Restricted access');

 jimport('joomla.application.component.view');

 class ZeitbankViewJournal extends JViewLegacy {
 	function display($tpl = null) {
 		JToolBarHelper::title('Zeitbank: Journal','user.png');
 		JToolBarHelper::deleteList();
// 		JToolBarHelper::editListX();
// 		JToolBarHelper::addNewX();
 		$items =& $this->get('Data');
 		$this->assignRef('items',$items);
 		parent::display($tpl);
 	}
 }
?>
