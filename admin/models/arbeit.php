<?php
/*
 * Created on 27.12.2010
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ArbeitenModelArbeit extends JModel {
	var $_data;

	function __construct() {
		parent::__construct();
		$array = JRequest::getVar('cid', 0, '', 'array');
		$this->setId($array[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_data = null;
	}  // end setId()
	
	function getData() {
		if(empty($this->_data)):
				$query = 'SELECT * FROM #__mgh_zb_arbeit WHERE id='.$this->_id;
				$this->_db->setQuery( $query );
				$this->_data = $this->_db->loadObject();
		endif;

		if(!$this->_data):
			$this->_data = new stdClass();
			$this->_data->_id = 0;
		endif;
		
		return $this->_data;
	} // getData()	
	
	function getKategorien() {
	    $db =& JFactory::getDBO();
		$query = "SELECT * FROM #__mgh_zb_kategorie ORDER BY ordering";
	    $db->setQuery($query);
	    $rows = $db->loadObjectList();
	    return($rows);
	} // getKategorien
	
	function store() {
		global $mainframe;			// für Fehlerausgabe

		// Datenbankhandle für "fremde" Tabellen
		$db =& JFactory::getDBO();
		
		$row =& $this->getTable();
		$data = JRequest::get( 'post' );
			
		// Ämtlidaten schreiben in Tabelle
	    if(!$row->bind( $data )):
			$this->setError($this->_db->getErrorMsg());
			return false;
		endif;
		
		if(!$row->check()):
			$this->setError($this->_db->getErrorMsg());
			return false;
		endif;
		
		if(!$row->store()):
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		endif;
		
		return true;
	} // end store()
	
	function delete() {
		return(false);
	}

}

?>
