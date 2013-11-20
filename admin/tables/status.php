<?php
/*
 * Created on 28.12.2012
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableStatus extends JTable {
	var $id = null;
	var $ordering = null;
	var $bezeichnung = null;
	var $gesamtbudget = null;
	var $nachtrag = null;
	var $user_id = null;
	var $admin_id = null;
	var $status = null;
	
	function TableStatus( &$db ) {
		parent::__construct('#__mgh_zb_kategorie','id',$db);
	}
}
?>
