<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Model für die Liste der quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
 * 
 * @author JAL
 * @author Steffen Förster
 */
class ZeitbankModelQuittungsliste_Amt extends JModel {
	
  var $total = null;
  var $pagination = null;

  /**
   * Konstruktor.
   */
  public function __construct() {
 	  parent::__construct();
 
	  $mainframe = JFactory::getApplication();
 
	  // Get pagination request variables
	  $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	  $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
	  // In case limit has been changed, adjust it
	  $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
	  $this->setState('limit', $limit);
	  $this->setState('limitstart', $limitstart);
  }
  
  /**
   * Liefert die Liste mit allen vom Benutzer quittierten Buchungen.
   */
  public function getQuittungsliste() {
    $db = JFactory::getDBO();
    $query = $this->buildQuery();
    return $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));	
  }

  /**
   * Liefert eine Instanz der Klasse JPagination. 
   */
  public function getPagination() {
 	  if (empty($this->pagination)) {
 	    jimport('joomla.html.pagination');
 	    $this->pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
 	  }
 	  return $this->pagination;
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
  
  /**
   * Liefert die Gesamtanzahl der quittierten Buchungen.
   */
  private function getTotal() {
    if (empty($this->total)) {
      $query = $this->buildQuery();
      $this->total = $this->_getListCount($query);
    }
    return $this->total;
  }
  
} 
?>
