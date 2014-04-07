<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.model');

/**
 * Model für die Übersichtsseite der Zeitbank.
 */
class ZeitbankModelZeitbank extends JModel {
	
	/**
	 * Liefert String mit menschenlesbarer Zeitangabe
	 */
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
	}
	
	/**
	 * Liefert die Liste mit den Anträgen "Privater Stundentausch", welche zur Belastung des eigenen Zeitkontos führen.
	 * 
	 * In der ursprünlichen Version gab es keine Einschränkung auf den privaten Stundentausch. Hätte man sich mit einem 
	 * Zeitkonto-Login angemeldet, hätte man somit auch quittieren können.
	 * 
	 * Besser ist jedoch eine Funktion im Backend, von wo man Quittierungen durchführen kann.
	 */
	function getOffeneQuittierungen() {
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $query = "SELECT journal.id,journal.cf_uid,minuten,users.name as name,datum_antrag,kurztext,arbeit.kurztext,journal.kommentar_antrag as text
    		      FROM #__users as users, #__mgh_zb_arbeit as arbeit, #__mgh_zb_journal AS journal
    		      WHERE datum_quittung='0000-00-00' 
                AND admin_del='0' 
                AND arbeit_id = arbeit.id
    		        AND users.id = gutschrift_userid
    		        AND belastung_userid =".$user->id."
    		        AND arbeit.id = ".ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH." 
              ORDER BY datum_antrag ASC, journal.id ASC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return($rows);
  }

  /**
   * Liefert die Liste der eigenen Anträge, welche noch nicht quittiert sind.
   */
  function getOffeneAntraege() {
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $query = "SELECT journal.id,journal.cf_uid,minuten,users.name as name,datum_antrag,kurztext,arbeit.kurztext,journal.kommentar_antrag as text,
                CASE 
                  WHEN arbeit.id = ".ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH." THEN 'stundentausch.edit'
                  WHEN arbeit.kategorie_id = ".ZeitbankConst::KATEGORIE_ID_FREIWILLIG." THEN 'freiwilligenarbeit.edit'   
                  ELSE 'eigenleistungen.edit'
                END AS task 
    		      FROM #__users as users, #__mgh_zb_arbeit as arbeit, #__mgh_zb_journal AS journal
    		      WHERE datum_quittung='0000-00-00' 
                AND admin_del='0' 
                AND arbeit_id = arbeit.id
    		        AND users.id = belastung_userid 
                AND gutschrift_userid ='".$user->id."' 
              ORDER BY datum_antrag ASC, journal.id ASC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return($rows);
  }

  /**
   * Liefert alle Buchungen des angemeldeten Benutzers für das laufende Jahr.
   */
  function getUserJournal() {
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $query = "SELECT journal.id AS id, journal.cf_uid, minuten, belastung_userid, gutschrift_userid, datum_antrag, 
                arbeit.kurztext, journal.arbeit_id, 
                CASE WHEN (journal.arbeit_id IN (SELECT id FROM #__mgh_zb_arbeit WHERE kategorie_id = -1)) THEN
                  'freiwillig'
                ELSE
                  'eigenleistung'
                END AS art
    		      FROM #__mgh_zb_journal_quittiert_laufend_inkl_freiw AS journal, #__mgh_zb_arbeit AS arbeit
    	       	WHERE arbeit_id = arbeit.id 	
    		        AND (gutschrift_userid = ".$user->id." OR belastung_userid = ".$user->id.") 
    		      ORDER BY datum_antrag DESC, journal.id DESC";
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
  
  /**
   * Liefert true, wenn das übergebene Mitglied ein Gewerbe ist.
   */
  public function isGewerbe($userId) {
    $db = JFactory::getDBO();
    $query = "SELECT count(*)
              FROM #__mgh_mitglied
              WHERE userid = ".$userId."
                AND typ = 2";
    $db->setQuery($query);
    $count = $db->loadResult();
    return $count == 1;
  }
  
}
?>
