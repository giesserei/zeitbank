<?php
/*
 * Created on 28.12.2012
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableArbeit extends JTable {
	var $id = null;
	var $kurztext = null;
	var $beschreibung = null;
	var $jahressoll = null;
	var $kadenz = null;
	var $pauschale = null;
	var $kategorie_id = null;
	var $ordering = null;
	var $user_id = null;
	var $admin_id = null;
	var $kommentar = null;
	
	function TableArbeit( &$db ) {
		parent::__construct('#__mgh_zb_arbeit','id',$db);
	}
}
?>
