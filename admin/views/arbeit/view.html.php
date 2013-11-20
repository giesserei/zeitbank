<?php
/*
 * Created on 27.12.2010
 *
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ArbeitenViewArbeit extends JView {
	
	function display( $tpl = null ) {
		$arbeit =& $this->get( 'Data' );
		$isNew = ($arbeit->id < 1); 
		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title( 'Zeitbank-Arbeit: <small>['.$text.']</small>');
		JToolBarHelper::save();
		if($isNew):
			JToolBarHelper::cancel();
		else:
			JToolBarHelper::cancel( 'cancel', 'Close' );
		endif;	

		$kategorien =& $this->get('Kategorien');
		
//		$whg_typen =& $this->get( 'Wohnungstypen' );
//		$whg_optlist =& $this->get( 'Optionenliste' );
//		$whg_opt =& $this->get( 'Optionen' );
		
		$this->assignRef( 'arbeit', $arbeit);
		$this->assignRef( 'kategorien', $kategorien);
		
//		$this->assignRef( 'wohnungstypen', $whg_typen);
//		$this->assignRef( 'optionenliste', $whg_optlist);
//		$this->assignRef( 'optionen', $whg_opt);
		
		parent::display( $tpl );
	 }
}

?>
