<?php
/*
 * Created on 27.12.2010
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelJournal extends JModel {
	var $_data;
	function getData() {
		if(empty($this->_data)) {
			if(JRequest::getVar('act',NULL) == 'unvalidierte'):
				$query = 'SELECT * FROM #__mgh_mitglied WHERE austritt = "0000-00-00" ORDER BY nachname';
			else:
				$query = 'SELECT * FROM #__mgh_zb_journal ORDER BY id';
			endif;
			
			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}

	function delete() {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid',array(0),'post','array');
		$ok = true;
		
		if( count($cids) ):
			foreach($cids as $id):
				if(intval($id) > 0):
					$query="UPDATE #__mgh_zb_journal SET admin_del='1' WHERE id='".$id."'"; 
					$db->setQuery( $query );
					$db->query();
					if(mysql_affected_rows() > 0 && $ok):
						$ok = true;
					else:
						$ok = false;
					endif;
				endif;
			endforeach;
		endif;
		
		return($ok);
	}
}
?>
