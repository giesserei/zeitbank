<?php
/*
 * Created on 30.05.2013
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once(JPATH_BASE.DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'zentralbank_func.php');



class ZeitbankModelStatus extends JModel {
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
				$query = 'SELECT * FROM #__mgh_zb_kategorie';
				//$this->_db->setQuery( $query );
				$this->_data = $this->_getList($query);
		endif;

		if(!$this->_data):
			$this->_data = new stdClass();
			$this->_data->_id = 0;
		endif;
		
		return $this->_data;
	} // getData()

	function writeStatus($status) {
		// Kategoriestatus in alle Kategorien schreiben
		$db =& JFactory::getDBO();
		$query = "UPDATE #__mgh_zb_kategorie SET status='".intval($status)."' WHERE 1";	
		$db->setQuery( $query );
		if($db->query()):
			return(true);
		else:
			return(false);
		endif;					
	} // writeStatus
	
	function store($ok) {
		global $mainframe;			// für Fehlerausgabe

		// Datenbankhandle für "fremde" Tabellen
		$db =& JFactory::getDBO();
		
		$row =& $this->getTable();
		$data = JRequest::get( 'post' );
		
		// Nur bei Änderung des Status aktiv werden
		if($data['aktueller_status'] != $data['status'] AND $ok ==  'ok'):
			switch($data['status']):
				case 1: // Status 1: Neues Budget einreichen
					if($this->writeStatus($data['status'])) return(1);
					break;
				case 2: // Status 2: Jahresbudget verteilt
					
					// Erwachsener Bewohner zählen mit Berücksichtigung der Bezugstermine: Summe aller "Arbeitstage"
					$sBT = summeBewohnerTage()+summeGewerbeTage();
					
					// Summe aller Kategorien / Summer aller Arbeitstage = Stunden pro Person und Tag (aufrunden!)
					$jahressoll = ceil(summeKategorien()*365/$sBT);
					
					// Umbuchen in Haupttopf aus den Kategorien gemäss Budgetierung
					buchenKategorien();
					
					// Umbuchen Haupttopf zu den persönlichen Konti: h/d * Arbeitstage = Summe der Person (mit Kommentar zur Berechnungsgrundlage) 
					buchenJahresbudget($jahressoll,summeKategorien()); 
					
					if($this->writeStatus($data['status'])) return('Jahresbudget_verteilt');
					break;
				case 3: // Status 3: Nachträge einreichen
					// Freischalten der Eingabemaske für kategorieverantwortliche Person
					if($this->writeStatus($data['status'])) return(1);
					break;
				case 4: // Status 4: Nachträge buchen
					// Umbuchen in Haupttopf aus den Kategorien gemäss Nachtragsangabe
					// Erwachener Bewohner zählen mit Berücksichtigung der Bezugstermine: Summe aller "Arbeitstage"
					// Summe aller Kategorien / Summer aller Arbeitstage = Stunden pro Person und Tag (aufrunden!)
					// Aufzeigen der Konsequenzen und abholen: Bist du sicher?
					// Umbuchen Haupttopf zu den persönlichen Konti: h/d * Arbeitstage = Summe der Person (mit Kommentar zur Berechnungsgrundlage) 
					if($this->writeStatus($data['status'])) return(1);
					break;
				case 5: // Status 5: Jahresabschluss
					if($this->writeStatus($data['status'])) return(1);
					break;
			endswitch; 
			
		else:
			// auch Fehler, wenn nichts geändert
			return(false);
		endif;
	} // end store()
	
	function delete() {
		return(false);
	}
}
?>
