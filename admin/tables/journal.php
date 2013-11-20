<?php
/*
 * Created on 27.12.2010
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableJournal extends JTable {
	var $id = null;
	var $minuten = null;
	var $belastung_userid = null;
	var $gutschrift_userid = null;
	var $datum_antrag = null;
	var $datum_quittung = null;
	var $admin_del = null;
	var $arbeit_id = null;
	
	
	function TableJournal( &$db ) {
		parent::__construct('#__mgh_zb_journal','id',$db);
	}
}
?>
