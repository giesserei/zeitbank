<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelKategorien extends JModelLegacy {

	var $_data;

	private $id;

	public function __construct() {
	   parent::__construct();
	   $array = JRequest::getVar( 'cid', array(0), '', 'array' );
	   $edit = JRequest::getVar( 'edit', true );
	   if ($edit) {
			 $this->id = (int)$array[0];
		 }
	}
	
	public function getData() {
		$sort = JRequest::getVar('filter_order', 0);
		$dir = JRequest::getVar('filter_order_Dir', 'asc');
		
		if (empty($this->_data)) {
			if ($sort == 'reihenfolge') {
				$query = 'SELECT * FROM #__mgh_zb_kategorie ORDER BY ordering ' . $dir;
			} else {
				$query = 'SELECT * FROM #__mgh_zb_kategorie ORDER BY id ' . $dir;
			}
			$this->_data = $this->_getList($query);
		}
			
		return $this->_data;
	}

	/**
	 * Kategorie kann nicht gelöscht werden.
	 * @return bool false
	 */
	public function delete() {
		return false;
	}

	public function move($direction) {
      $table = $this->getTable('kategorien');
      
      if (!$table->load($this->id)) {
         return false;
      }

      if (!$table->move( $direction, ' id = '.(int) $table->id )) {
         return false;
      }

      return true;
   }
}
