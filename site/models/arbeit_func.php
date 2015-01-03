<?php

/* Funktionen für Management-Oberfläche Ämtli 
 * 22.5.2013 jal
 * 
 * */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'zeitbank.php');

// Stellt die Liste aller Ämtli für den Ämtli-Administrator dar
function get_arbeitsliste($menuitem) {
	$db =& JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$laufendes_jahr = intval(date('Y'));
	
	$zb = new ZeitbankModelZeitbank();
	
	$output ="<table class=\"zeitbank\">";
    $query = "SELECT *,ab.id as id,kat.bezeichnung,kat.id as kat_id FROM #__mgh_zb_arbeit AS ab,#__mgh_zb_kategorie AS kat WHERE ab.admin_id='".$user->id."' AND kategorie_id = kat.id ORDER BY kat.ordering,ab.ordering";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    
    $output .= "<tr class=\"head\">
                  <th>Kurztext</th>
                  <th>Jahressoll</th>
                  <th>Kadenz</th>
                  <th>Buchungen ".$laufendes_jahr."</th>
                  <th>Pauschale</th>
                  <th>Kategorie</th>
                  <th>Aktiviert</th>
                  <th> &nbsp; </th>
                </tr>";
    $k = 0;	// Zebra start
    
    if($db->getAffectedRows() > 0):
    	foreach ($rows as $row):
    		$style = $k ? "even" : "odd"; // Zebramuster				
    	
    		$summe = arbeit_summe($row->id, $laufendes_jahr);
    		
    		$output .= "<tr class=\"".$style."\">";
    		$output .= "<td>".$row->kurztext."</td>";
   			$output .= "<td align=\"right\">".$row->jahressoll."h</td>";
   			$output .= "<td align=\"right\">".$row->kadenz."/Jahr</td>";
   			$output .= "<td align=\"right\">".$zb->showTime($summe)."h &nbsp; <span style=\"";
   			if($row->jahressoll*60 - $summe < 0) $output .= "color: red";
   			$output .= "\">(".$zb->showTime(($row->jahressoll*60 - $summe))."h)</span></td>";
   			if($row->pauschale > 0):
   				$output .= "<td align=\"right\">".$row->pauschale." min</td>";
   			else:
   				$output .= "<td align=\"right\"> - </td>";
   			endif;
   			$output .= "<td>".$row->bezeichnung."</td>";
   			$output .= "<td align=\"center\">";
   			if($row->aktiviert):
   				$output .= "<img src=\"/images/on.png\">";
   			else:
   				$output .= "<img src=\"/images/off.png\">";
   			endif;
   			$output .= "</td>";
    		$output .= "<td><input type=\"button\" value=\"bearbeiten\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_arbeit_edit&id=".$row->id."&kat_id=".$row->kat_id."&Itemid=".$menuitem."'\"/></td>";
    		$output .= "</tr>";
    		
    		$k = 1 - $k;   
    	endforeach;
    endif;
    $output .= "</table>";
	return($output);
} // get_arbeitsliste


// Stellt die Liste aller Ämtli für die Endbenutzer dar
function get_arbeitsliste_enduser() {
	$db =& JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$laufendes_jahr = intval(date('Y'));
	$lastYear = $laufendes_jahr - 1;
	
	$zb = new ZeitbankModelZeitbank();
	$output = "";
	
    $query = "SELECT bezeichnung,id FROM #__mgh_zb_kategorie ORDER BY ordering";
    $db->setQuery($query);
    $kategorien = $db->loadObjectList();
	
    if($db->getAffectedRows() > 0):
    	foreach ($kategorien as $kat):
    		$query = "SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='".$kat->id."' AND aktiviert='1' ORDER BY ordering";
    		$db->setQuery($query);
    		$arbeiten = $db->loadObjectList();
    		$k = 0;	// Zebra start
    		
    		if($db->getAffectedRows() > 0):
	    		$output .= "<h3>Kategorie: ".$kat->bezeichnung."</h3>";
				$output .="<table class=\"zeitbank\">";
	    		$output .= "<tr class=\"head\">
	    		              <th width=\"300\">Kurztext (wie im Auswahlmenü)<br />und Kommentar</th>
	    		              <th width=\"150\">Zuständig</th>
	    					        <th width=\"70\" align=\"right\">Jahressoll</th>
	    		              <th width=\"100\" align=\"right\">Kadenz</th>
	    					        <th width=\"100\" align=\"right\">Buchungen ".$laufendes_jahr."</th>
	    					        <th width=\"100\" align=\"right\">Buchungen ".$lastYear."</th>
	    					        <th width=\"70\" align=\"right\">Pauschale</th>
	    					      </tr>";
			
    			foreach($arbeiten as $ab):
    				$style = $k ? "even" : "odd"; // Zebramuster				
    				$output .= "<tr class=\"".$style."\"><td><strong>".$ab->kurztext."</strong></td>";
	    			$output .= "<td>".JFactory::getUser($ab->admin_id)->name."</td>";
	    			$output .= "<td align=\"right\">".$ab->jahressoll." h</td>";
	    			if($ab->kadenz > 0):
	    				$output .= "<td align=\"right\">".$ab->kadenz." Einsätze/Jahr</td>";
	    			else:
	    				$output .="<td align=\"right\">-</td>";
	    			endif;
	    			$output .= "<td align=\"right\">".round(arbeit_summe($ab->id, $laufendes_jahr)/60,0)." h</td>";
	    			$output .= "<td align=\"right\">".round(arbeit_summe($ab->id, $lastYear)/60,0)." h</td>";
	    			$output .= "<td align=\"right\">".($ab->pauschale > 0 ? $ab->pauschale.' min' : '-')."</td>";
	    			$output .= "</tr>";
	    			if(strlen($ab->beschreibung) > 1):
	    				$output .= "<tr class=\"".$style."\"><td colspan=\"7\"> &nbsp; &raquo; ".$ab->beschreibung."</td></tr>";
	    			endif;
	    			$k = 1 - $k;
    			endforeach;
				$output .= "</table><br />";
    		endif;
    	endforeach;
    endif;
    
	return($output);
} //get_arbeitsliste_enduser


// Ermittelt die Summe der Stunden eines bestimmten Ämtlis während des laufenden Kalenderjahres
function arbeit_summe($id, $jahr) {
	$db =& JFactory::getDBO();
	
	$query = "SELECT COALESCE(sum(minuten),0) minuten FROM #__mgh_zb_journal 
	          WHERE datum_quittung != '0000-00-00' 
	            AND (datum_antrag BETWEEN '".$jahr."-01-01' AND '".$jahr."-12-31')
	            AND admin_del = 0 
	            AND arbeit_id = ".$id;
	
	$db->setQuery($query);
  return $db->loadResult();
}

// Ermittelt die Anzahl noch zu quittierenden Anträge für einen Admin
function get_anzahl_offen() {
	$db =& JFactory::getDBO();
	$user =& JFactory::getUser();
	
    $query = "SELECT journal.id,journal.cf_uid as cf_uid,minuten,users.name as name,datum_antrag,arbeit.kurztext,
    		kat.user_id as gegenkonto,kommentar.text as text
    		FROM #__users as users, #__mgh_zb_arbeit as arbeit, #__mgh_zb_kategorie as kat,
    		#__mgh_zb_antr_kommentar as kommentar RIGHT JOIN #__mgh_zb_journal AS journal ON kommentar.journal_id = journal.id
    		WHERE datum_quittung='0000-00-00' AND admin_del='0' AND users.id = gutschrift_userid AND arbeit.id = journal.arbeit_id
    		AND kat.id = arbeit.kategorie_id AND arbeit.admin_id ='".$user->id."' ORDER BY datum_antrag ASC,journal.id ASC";
    $db->setQuery($query);
    $db->loadObjectList();
    
    return($db->getAffectedRows());
	
} // get_anzahl_offen()


// Ermittelt den User für die Gegenbuchung
function get_gegenkonto($kategorie) {
	$db =& JFactory::getDBO();

	$query = "SELECT user_id FROM #__mgh_zb_kategorie WHERE id='".strval($kategorie)."'";
	
	$db->setQuery($query);
    $rows = $db->loadObjectList();
    
    if(mysql_affected_rows() > 0):
 		return($rows[0]->user_id);
	else:
		return(NULL);
	endif;	
} // get_gegenkonto

// Prüft ob User berechtigt ist, einen Eintrag zu quittieren
function user_ok($token) {
	$db =& JFactory::getDBO();
	$user =& JFactory::getUser();

	// Ist User selbst zuständig?
	$query = "SELECT belastung_userid FROM #__mgh_zb_journal WHERE cf_uid = '".$token."' AND datum_quittung='0000-00-00'";
	
	$db->setQuery($query);
    $rows = $db->loadObjectList();
    
    if($rows[0]->belastung_userid == $user->id):
 		return($rows[0]->belastung_userid);
	else:
		// Ist User Administrator des Ämtlis?
		$gegenkonto = $rows[0]->belastung_userid;
		$query = "SELECT ab.id FROM #__mgh_zb_arbeit as ab,#__mgh_zb_kategorie as kat WHERE ab.admin_id = '".$user->id."' 
			AND kat.user_id = '".$gegenkonto."' AND kat.id = ab.kategorie_id";

		$db->setQuery($query);
    	$rows = $db->loadObjectList();
    	
		if(mysql_affected_rows() > 0):
			return($gegenkonto);
		else:
			return(NULL);
		endif;
	endif;	
	
} // user_ok()

// Bestimmt den Wert für das Feld reihenfolge, damit neues Ämtli am Schluss eingefügt wird
function get_ende_reihenfolge() {
		$db =& JFactory::getDBO();
		
		$query = "SELECT ordering FROM #__mgh_zb_arbeit ORDER BY ordering DESC";
		$db->setQuery($query);
    	$rows = $db->loadObjectList();

    	if(mysql_affected_rows() > 0):
			return($rows[0]->ordering + 1);
		else:
			return(1);
		endif;
    	
		
} // get_ende_reihenfolge()

?>