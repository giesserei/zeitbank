<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelQuittung_Amt extends JModel {
	
	// Liefert String mit menschenlesbarer Zeitangabe
	function showTime($time_in_minutes) {
		$hours = floor($time_in_minutes/60);
		$minutes = $time_in_minutes - $hours*60;
		return($hours.":".$minutes);
	} // showTime
	
	function getOffeneQuittierungen() {
    $db =& JFactory::getDBO();
    $user =& JFactory::getUser();
    
    // zu administrierenden Arbeiten bestimmen
    $query = "SELECT journal.id,journal.cf_uid as cf_uid,minuten,users.name as name,datum_antrag,arbeit.kurztext,
    		kat.user_id as gegenkonto,kommentar.text as text
    		FROM #__users as users, #__mgh_zb_arbeit as arbeit, #__mgh_zb_kategorie as kat,
    		#__mgh_zb_antr_kommentar as kommentar RIGHT JOIN #__mgh_zb_journal AS journal ON kommentar.journal_id = journal.id
    		WHERE datum_quittung='0000-00-00' AND admin_del='0' AND users.id = gutschrift_userid AND arbeit.id = journal.arbeit_id
    		AND kat.id = arbeit.kategorie_id AND arbeit.admin_id ='".$user->id."' ORDER BY arbeit.id,datum_antrag ASC,journal.id ASC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
	    
    return($rows);
  }  // getOffeneQuittierungen
  
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
  } // getUserName

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
  } // getBelastungsKommentar

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
  } // getBelastungsKommentar
  
  
} // class ZeitbankModelZeitbank
?>
