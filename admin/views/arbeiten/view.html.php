<?php

 defined('_JEXEC') or die('Restricted access');

 jimport('joomla.application.component.view');

 class ZeitbankViewArbeiten extends JViewLegacy {
 	 function display($tpl = null) {
		 JToolBarHelper::title('Zeitbank: Arbeiten','user.png');
 		 JToolBarHelper::editList();
 		 JToolBarHelper::addNew();

 		 $items = $this->get('Data');
 		 $this->assignRef('items', $items);

		 return parent::display($tpl);
	 }
 }

