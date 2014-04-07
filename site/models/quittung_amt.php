<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Model für die View, welche die offenen Anträge auflistet.
 * 
 * @author JAL
 * @author Steffen Förster
 */
class ZeitbankModelQuittung_Amt extends JModel {
	
  /**
   * Liefert alle offenen Anträge, für die der angemeldete Benutzer der Administrator ist.
   */
	public function getOffeneQuittierungen() {
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    
    $query = 
      "SELECT j.id, j.minuten, j.datum_antrag, a.kurztext, j.kommentar_antrag text,
         (SELECT u.name FROM #__users u WHERE u.id = j.gutschrift_userid) konto_gutschrift 
    	 FROM #__mgh_zb_journal j JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
       WHERE j.datum_quittung='0000-00-00' 
         AND j.admin_del='0' 
         AND a.admin_id = ".$user->id." 
       ORDER BY a.id, j.datum_antrag ASC, j.id ASC";
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
}
?>
