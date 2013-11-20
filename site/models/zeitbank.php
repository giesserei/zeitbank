<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelZeitbank extends JModel {
	
	// Liefert String mit menschenlesbarer Zeitangabe
	function showTime($time_in_minutes) {
		$time_in_minutes = round($time_in_minutes);
		
		// Negative Werte gesondert behandeln
		if($time_in_minutes >= 0):
			$hours = floor($time_in_minutes/60);
			$minutes = $time_in_minutes - $hours*60;
		else:
			$hours = ceil($time_in_minutes/60);
			$minutes = $time_in_minutes - $hours*60;
		endif;

		// Minuszeichen bei den Minuten wegschneiden
		$minutes = ltrim($minutes,'-');
		if(strlen($minutes) <= 1) $minutes = "0".$minutes;
		return($hours.":".$minutes);
	} // showTime
	
	function getOffeneQuittierungen() {
    $db =& JFactory::getDBO();
    $user =& JFactory::getUser();
    $query = "SELECT journal.id,journal.cf_uid,minuten,users.name as name,datum_antrag,arbeit.kurztext,kommentar.text as text
    		FROM #__users as users,#__mgh_zb_arbeit as arbeit, #__mgh_zb_antr_kommentar as kommentar 
    		RIGHT JOIN #__mgh_zb_journal AS journal ON kommentar.journal_id = journal.id
    		WHERE datum_quittung='0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id
    		AND users.id = gutschrift_userid AND belastung_userid ='".$user->id."' ORDER BY datum_antrag ASC,journal.id ASC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return($rows);
  }  // getOffeneQuittierungen

  function getOffeneAntraege() {
    $db =& JFactory::getDBO();
    $user =& JFactory::getUser();
    $query = "SELECT journal.id,journal.cf_uid,minuten,users.name as name,datum_antrag,kurztext,arbeit.kurztext,kommentar.text as text
    		FROM #__users as users,#__mgh_zb_arbeit as arbeit, #__mgh_zb_antr_kommentar as kommentar
    		RIGHT JOIN #__mgh_zb_journal AS journal ON kommentar.journal_id = journal.id
    		WHERE datum_quittung='0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id
    		AND users.id = belastung_userid AND gutschrift_userid ='".$user->id."' ORDER BY datum_antrag ASC,journal.id ASC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return($rows);
  } // getOffeneAntraege

  function getUserJournal() {
    $db =& JFactory::getDBO();
    $user =& JFactory::getUser();
    $laufendes_jahr = date('Y');
    $query = "SELECT journal.id as id,journal.cf_uid,minuten,belastung_userid,gutschrift_userid,datum_antrag,arbeit.kurztext
    		FROM #__mgh_zb_journal AS journal,#__mgh_zb_arbeit as arbeit
    		WHERE datum_quittung != '0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id AND datum_antrag >= '".$laufendes_jahr."-01-01' 	
    		AND (gutschrift_userid ='".$user->id."' OR belastung_userid ='".$user->id."') ORDER BY datum_antrag DESC,journal.id DESC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return($rows);
  } // getUserJournal
  
  function getSaldoVorjahr() {
    $db =& JFactory::getDBO();
    $user =& JFactory::getUser();
    $vorjahr = date('Y',time() - (365 * 24 * 60 * 60));
    $query = "SELECT journal.id as id,journal.cf_uid,minuten,belastung_userid,gutschrift_userid,datum_antrag,arbeit.kurztext
    		FROM #__mgh_zb_journal AS journal,#__mgh_zb_arbeit as arbeit
    		WHERE datum_quittung != '0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id AND datum_antrag >= '".$vorjahr."-01-01' AND datum_antrag <= '".$vorjahr."-12-31' 	
    		AND (gutschrift_userid ='".$user->id."' OR belastung_userid ='".$user->id."') ORDER BY datum_antrag DESC,journal.id DESC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    
	$saldo = 0;
	if($db->AffectedRows() > 0) foreach($rows as $jn):
		if($jn->belastung_userid == $user->id):
			$saldo -= $jn->minuten;
		else:
			$saldo += $jn->minuten;
		endif;
	endforeach;
    
    return($saldo);
  } // getUserJournal
  
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
