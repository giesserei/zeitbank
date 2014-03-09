<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelUserjournal extends JModel {

  private function buildQuery() {
    $user =& JFactory::getUser();
  	$query = "SELECT SQL_CALC_FOUND_ROWS journal.id as id,journal.cf_uid,minuten,belastung_userid,gutschrift_userid,datum_antrag,arbeit.kurztext,journal.arbeit_id
    		FROM #__mgh_zb_journal AS journal,#__mgh_zb_arbeit as arbeit
    		WHERE datum_quittung != '0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id 	
    		AND (gutschrift_userid ='".$user->id."' OR belastung_userid ='".$user->id."') ORDER BY datum_antrag DESC,journal.id DESC";
  	return($query);
  }
  
  function getUserJournal() {
    $db =& JFactory::getDBO();
    $query = $this->buildQuery();
    $db->setQuery($query);
    $rows = $db->loadObjectList();
	  return $rows;
  } 
  
  function getUserName($uid) {
    $db =& JFactory::getDBO();
    $query = "SELECT name FROM #__users WHERE id='".$uid."'";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    if($db->getAffectedRows() > 0):
    	return($rows[0]->name);
    else:
    	return(NULL);
    endif;
  } 

  function getBelastungsKommentar($jid) {
    $db =& JFactory::getDBO();
    $query = "SELECT text FROM #__mgh_zb_antr_kommentar WHERE journal_id='".$jid."'";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    if($db->getAffectedRows() > 0):
    	return($rows[0]->text);
    else:
    	return(NULL);
    endif;
  } 

  function getQuittierungsKommentar($jid) {
    $db =& JFactory::getDBO();
    $query = "SELECT text FROM #__mgh_zb_quit_kommentar WHERE journal_id='".$jid."'";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    if($db->getAffectedRows() > 0):
    	return($rows[0]->text);
    else:
    	return(NULL);
    endif;
  }
  
} 
?>
