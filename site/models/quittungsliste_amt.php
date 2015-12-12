<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Model für die Liste der quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
 * 
 * @author JAL
 * @author Steffen Förster
 */
class ZeitbankModelQuittungsliste_Amt extends JModelLegacy {
	
  /**
   * Liefert die Liste mit allen vom Benutzer quittierten Buchungen.
   * 2015-01-03 Pagination entfernt -> zu fehlerhaft
   */
  public function getQuittungsliste() {
    $db = JFactory::getDBO();
    $query = $this->buildQuery();
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    return $rows;
  }

  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  private function buildQuery() {
    $user = JFactory::getUser();
    $query =
    "SELECT SQL_CALC_FOUND_ROWS j.id, j.minuten, j.datum_antrag, a.kurztext,
         (SELECT u.name FROM #__users u WHERE u.id = j.gutschrift_userid) konto_gutschrift
    	 FROM #__mgh_zb_journal j JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
       WHERE j.datum_quittung != '0000-00-00'
         AND j.admin_del = '0'
         AND a.admin_id = ".$user->id."
       ORDER BY j.datum_antrag DESC, j.id DESC";
  
    return($query);
  }  
}
