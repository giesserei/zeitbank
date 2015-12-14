<?php
/*
 * Created on 27.12.2010
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ArbeitenModelArbeiten extends JModelLegacy {
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
				$query = 'SELECT *,kat.bezeichnung as kategorie_bez, arb.ordering as ordering, arb.id as id, arb.admin_id as admin_id
				FROM #__mgh_zb_arbeit as arb,#__mgh_zb_kategorie as kat
				WHERE arb.kategorie_id = kat.id ORDER BY kat.ordering,arb.ordering ASC';
			else:
				$query = 'SELECT * FROM #__mgh_zb_arbeit ORDER BY id '.$dir;
			endif;
			$this->_data = $this->_getList($query);		
		endif;
			
		return $this->_data;
	} // getData()
	
	function delete() {
		return(false);
	}

	function getArbeitSaldo($id,$pauschale) {
		// Test letztes Jahr
		// $jahr = '2012';
		// Laufendes Jahr für Zeitsaldo des Arbeitseintrags
		$jahr = date('Y');
		
		$query = "SELECT minuten FROM #__mgh_zb_journal
		WHERE arbeit_id = '".$id."' AND admin_del = '0' AND datum_quittung > '0000-00-00' AND datum_antrag >= '".$jahr."-01-01' AND datum_antrag <= '".$jahr."-12-31'";

		$zeit = $this->_getList($query);
		$saldo = 0;

		foreach($zeit as $zt):
			// Nur wenn Arbeit nicht pauschaliert, eingetragene Zeit übernehmen
			if($pauschale <= 0):
				$saldo += $zt->minuten;
			else:
				$saldo += $pauschale;
			endif;
		endforeach;
		return($saldo);
		
	} // getArbeitSaldo

	function getArbeitAbweichung($id,$pauschale) {
		// Test letztes Jahr
		$jahr = '2012';
		// Laufendes Jahr für Zeitsaldo des Arbeitseintrags
		// $jahr = date('Y');
		
		$query = "SELECT pauschal_abweichung FROM #__mgh_zb_journal
		WHERE arbeit_id = '".$id."' AND admin_del = '0' AND datum_quittung > '0000-00-00' AND datum_antrag >= '".$jahr."-01-01' AND datum_antrag <= '".$jahr."-12-31'
		AND pauschal_abweichung > 0";

		$zeit = $this->_getList($query);
		$schnitt = 0;

		if(mysql_affected_rows() > 0):
			foreach($zeit as $zt):
				$schnitt += $zt->pauschal_abweichung;
			endforeach;
		endif;
			
		if (count($zeit) > 0) $schnitt = round($schnitt / count($zeit)); else $schnitt = 0;
		return($schnitt);
		
	} // getArbeitSaldo
	
}

?>
