<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewKategorien extends JViewLegacy {

	protected $items;

	function display($tpl = null) {
 		JToolBarHelper::title('Zeitbank: Kategorien','user.png');
 		JToolBarHelper::editList();
 		JToolBarHelper::addNew();

		$this->items = $this->getModel()->getData();
 		
 		return parent::display($tpl);
 	}
}