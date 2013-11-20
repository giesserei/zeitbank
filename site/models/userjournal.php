<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelUserjournal extends JModel {
  var $_total = null;
  var $_pagination = null;


  function __construct()
  {
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

  function _buildQuery() {
    $user =& JFactory::getUser();
  	$query = "SELECT SQL_CALC_FOUND_ROWS journal.id as id,journal.cf_uid,minuten,belastung_userid,gutschrift_userid,datum_antrag,arbeit.kurztext
    		FROM #__mgh_zb_journal AS journal,#__mgh_zb_arbeit as arbeit
    		WHERE datum_quittung != '0000-00-00' AND admin_del='0' AND arbeit_id = arbeit.id 	
    		AND (gutschrift_userid ='".$user->id."' OR belastung_userid ='".$user->id."') ORDER BY datum_antrag DESC,journal.id DESC";
  	return($query);
  }  // buildQuery
  
  function getUserJournal() {
    $db =& JFactory::getDBO();
    $query = $this->_buildQuery();
    $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));	
	return $this->_data;
//    $db->setQuery($query,$lim0,$lim);
//    $rows = $db->loadObjectList();
//    return($rows);
  } // getUserJournal

function getTotal()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_total)) {
 	    $query = $this->_buildQuery();
 	    $this->_total = $this->_getListCount($query);	
 	}
 	return $this->_total;
  }  // getTotal



  function getPagination()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_pagination)) {
 	    jimport('joomla.html.pagination');
 	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
 	}
 	return $this->_pagination;
  }  // getPagination

  
  
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
  
  
} // class ZeitbankModeluserJournal
?>
