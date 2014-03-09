<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ZeitbankModelBuchung extends JModel {
    function getBuchung($token) {
        $db =& JFactory::getDBO();
        $query = "SELECT journal.id as id,minuten,busers.name as bel_name,gusers.name as gut_name,arbeit.kurztext,datum_antrag,datum_quittung,journal.arbeit_id
            FROM #__mgh_zb_arbeit as arbeit,#__mgh_zb_journal as journal
            LEFT JOIN #__users as busers ON journal.belastung_userid = busers.id
            LEFT JOIN #__users as gusers ON journal.gutschrift_userid = gusers.id
            WHERE cf_uid='".$token."' AND journal.arbeit_id=arbeit.id";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
    return($rows[0]);
    }  // getBuchung

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

}

?>
