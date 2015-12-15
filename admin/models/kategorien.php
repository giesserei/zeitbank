<?php
/*
 * Created on 27.12.2010
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelKategorien extends JModelLegacy {
	var $_data;

	function __construct()
	{
	   parent::__construct();
	   $array = JRequest::getVar( 'cid', array(0), '', 'array' );
	   $edit = JRequest::getVar( 'edit', true );
	   if($edit) $this->_id = (int)$array[0];
	}
	
	function getData() {
		$sort = JRequest::getVar('filter_order',0);
		$dir = JRequest::getVar('filter_order_Dir','asc');
		
		if(empty($this->_data)):
			if($sort == 'reihenfolge'):
				$query = 'SELECT * FROM #__mgh_zb_kategorie ORDER BY ordering '.$dir;
			else:
				$query = 'SELECT * FROM #__mgh_zb_kategorie ORDER BY id '.$dir;
			endif;
			$this->_data = $this->_getList($query);
		endif;
			
		return $this->_data;
	} // getData()
	
	function delete() {
		return(false);
	}

	function move($direction) {
      
      $db = JFactory::getDBO();
//      global $mainframe;
      
      $row =& $this->getTable('kategorien');
      
      if (!$row->load($this->id)) {
         $this->setError($db->getErrorMsg());
         return false;
      }

      if (!$row->move( $direction, ' id = '.(int) $row->id )) {
         $this->setError($db->getErrorMsg());
         return false;
      }

      return true;
   }
}

?>
