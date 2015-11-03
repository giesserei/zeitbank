<?php

/* Funktionen für Management-Oberfläche Kategorien
 * 24.5.2013 jal
 * 
 * */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE.'/components/com_zeitbank/models/arbeit_func.php');
require_once(JPATH_BASE.'/components/com_zeitbank/models/zeitbank.php');


// Ausgeben der Ämtli einer Kategorie
function get_kat_list($menuitem) {
	$db = JFactory::getDBO();
	$user = JFactory::getUser();
	
	$laufendes_jahr = intval(date('Y'));
	
	$zb = new ZeitbankModelZeitbank();
	
	// Ämtli-Verwalter einlesen
	$query="SELECT users.id,users.name,cf_uid FROM #__mgh_zb_x_kat_arbeitadmin as kat,#__users as users WHERE kat_id='".$kategorie."' AND user_id=users.id ORDER BY users.name";
	$db->setQuery($query);
    $verwalter = $db->loadObjectList();
    
    // Ämtli einlesen
	$output ="<table class=\"zeitbank\">";
    $query = "SELECT ab.id as id,kurztext,jahressoll,kadenz,pauschale,kat.bezeichnung,kat.id as kat_id,kat.gesamtbudget as budget,users.name,ab.ordering as ordering,ab.aktiviert FROM #__mgh_zb_kategorie AS kat
				LEFT JOIN #__mgh_zb_arbeit AS ab ON kat.id = ab.kategorie_id
				LEFT JOIN #__users AS users ON ab.admin_id = users.id
    			WHERE kat.admin_id = '".$user->id."'  
    			ORDER BY ab.ordering";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    
    $output .= "<tr class=\"head\">
                  <th>Kurztext</th>
                  <th>Jahressoll</th>
                  <th>Kadenz</th>
                  <th>Buchungen ".$laufendes_jahr."</th>
                  <th>Reststunden</th>
                  <th>Pauschale</th>
    			        <th>Aktiviert</th>
                  <th>Administrator</th>
                  <th>Reihenfolge</th>
                  <th>&nbsp;</th>
                </tr>";
    $k = 0;	// Zebra start
    $summe = 0;	// Summe der Arbeiten eines Ämtlis
    $kat_summe = 0; // Summe ganzen Kategorie
    $soll_summe = 0; // Summe der Ämtli-Jahressolls
    
    if($db->getAffectedRows() > 0):
    	foreach ($rows as $row):
    		$style = $k ? "even" : "odd"; // Zebramuster				
    	
    		$summe = arbeit_summe($row->id, $laufendes_jahr);
    		$kat_summe += $summe;
    		
    		$soll_summe += $row->jahressoll;
    		
    		$output .= "<tr class=\"".$style."\">";
    		$output .= "<td>".$row->kurztext."</td>";
   			$output .= "<td align=\"right\">".$row->jahressoll." h</td>";
   			$output .="<td align=\"right\">";
   			if($row->kadenz >= 52):
   				$output .= round($row->kadenz/52,1). "/Woche";
   			elseif($row->kadenz >= 12):
   				$output .= round($row->kadenz/12,1). "/Monat";
   			else:
   				$output .= $row->kadenz."/Jahr";
   			endif;
   			$output .= "</td><td align=\"right\">".$zb->showTime($summe)." h </td><td align=\"right\"> <span style=\"";
   			if($row->jahressoll*60 - $summe < 0) $output .= "color: red";
   			$output .= "\">".$zb->showTime(($row->jahressoll*60 - $summe))." h</span></td>";
   			if($row->pauschale > 0):
   				$output .= "<td align=\"right\">".$row->pauschale." min</td>";
   			else:
   				$output .= "<td align=\"right\"> - </td>";
   			endif;
   			$output .= "<td align=\"center\">";
   			if($row->aktiviert):
   				$output .= "<img src=\"/images/on.png\"></td>";
   			else:
   				$output .= "<img src=\"/images/off.png\"></td>";
   			endif; 
   			$output .= "<td>".$row->name."</td>";
   			$output .= "<td><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Amt_Zuweisen&Itemid=".$menuitem."&amt_id=".$row->id."&order=up&kat_id=".$row->kat_id."\"><img src=\"/images/uparrow.png\" alt=\"auf\" title=\"auf\"/></a> &nbsp;
   						 <a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Amt_Zuweisen&Itemid=".$menuitem."&amt_id=".$row->id."&order=down&kat_id=".$row->kat_id."\"><img src=\"/images/downarrow.png\" alt=\"auf\" title=\"ab\"/></a></td>";
    		$output .= "<td><input type=\"button\" value=\"bearbeiten\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_arbeit_kat_admin&id=".$row->id."&kat_id=".$row->kat_id."&Itemid=".$menuitem."'\"/></td>";
   			$output .= "</tr>";

    		$k = 1 - $k;   
    	endforeach;
    	
	    $style = $k ? "even" : "odd"; // Zebramuster				
	    $output .= "<tr class=\"".$style."\"><td colspan=\"2\" align=\"right\"><strong>Total: ".$soll_summe." h</strong></td>";
	    $output .= "<td colspan=\"2\" align=\"right\"><strong>Summe: ".$zb->showTime($kat_summe)." h</strong><br />Kategoriebudget: ".$row->budget." h</td><td colspan=\"6\">Differenz zu Plansoll: ";
	    if($soll_summe*60 - $kat_summe > 0):
	    	$output .= "<span style=\"color: green\">";
	    else:
	    	$output .= "<span style=\"color: red\">";
	    endif;
	    $output .= $zb->showTime(($soll_summe*60 - $kat_summe))." h</span><br />Differenz zu Kategoriebudget: ";
	    if($row->budget*60 - $kat_summe > 0):
	    	$output .= "<span style=\"color: green\">";
	    else:
	    	$output .= "<span style=\"color: red\">";
	    endif;
	    $output .= $zb->showTime(($row->budget*60 - $kat_summe))."</td></tr>";
    endif;
    $output .= "</table>";
	return($output);
	
} // get_kat_list()

// Erstellt eine Liste aller Ämtli-Admins für ChronoForms
function get_admin_list($kategorie,$menuitem) {
	$db = JFactory::getDBO();
	
	$output ="<ul>";
    $query = "SELECT users.id,users.name,cf_uid FROM #__mgh_zb_x_kat_arbeitadmin as kat,#__users as users WHERE kat_id='".$kategorie."' AND user_id=users.id ORDER BY users.name";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    
    if($db->getAffectedRows() > 0):
    	foreach ($rows as $row):
    		$output .= "<li>".$row->name." <input type=\"button\" value=\"löschen\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_admin_sicher_loeschen&token=".$row->cf_uid."&Itemid=".$menuitem."'\"/></li>";
    	endforeach;
    endif;
    $output .= "</ul>";
	return($output);
} // get_admin_list

// Bewegt ein Amt in der Reihenfolge nach oben
function order_up($amt_id,$kat_id) {
	$db = JFactory::getDBO();
	$query="SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='".intval($kat_id)."' ORDER BY ordering";
	$db->setQuery($query);
	$rows = $db->loadObjectList();
	$prev = 0;
	
	if($db->getAffectedRows() > 0):
	foreach($rows as $row):
		if($row->id == $amt_id AND is_object($prev)):
			$query="UPDATE #__mgh_zb_arbeit SET ordering='".$row->ordering."' WHERE id='".$prev->id."'";
			$db->setQuery($query);
			if($db->Query()):
				$query="UPDATE #__mgh_zb_arbeit SET ordering='".$prev->ordering."' WHERE id='".$row->id."'";
				$db->setQuery($query);
				$db->Query();
				endif;
		endif;
		$prev = $row;
	endforeach;
	endif;
} // order up

// Bewegt ein Amt in der Reihenfolge nach unten
function order_down($amt_id,$kat_id) {
	$db = JFactory::getDBO();
	$query="SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='".intval($kat_id)."' ORDER BY ordering DESC";
	$db->setQuery($query);
	$rows = $db->loadObjectList();
	$prev = 0;
	
	if($db->getAffectedRows() > 0):
	foreach($rows as $row):
		if($row->id == $amt_id AND is_object($prev)):
			$query="UPDATE #__mgh_zb_arbeit SET ordering='".$row->ordering."' WHERE id='".$prev->id."'";
			$db->setQuery($query);
			if($db->Query()):
				$query="UPDATE #__mgh_zb_arbeit SET ordering='".$prev->ordering."' WHERE id='".$row->id."'";
				$db->setQuery($query);
				$db->Query();
				endif;
		endif;
		$prev = $row;
	endforeach;
	endif;
} // order down

function get_status($kategorie_id) {
	$db = JFactory::getDBO();
	$query="SELECT status FROM #__mgh_zb_kategorie WHERE id='".intval($kategorie_id)."'";
	$db->setQuery($query);
	$rows = $db->loadObjectList();
	if (mysql_affected_rows() > 0):
		return($rows[0]->status);
	else:
		return(false);
	endif;
} // get_status

function show_status($status) {
	switch($status) {
		case 1:
			return("<span style=\"color:red\">Jahresbudget eingeben: Grobe Abschätzung</span>");
		case 2:
			return("<span style=\"color:green\">Jahresbudget verteilt</span>");
		case 3:
			return("<span style=\"color:red\">Nachtragsphase: Endjahreskorrektur eingeben</span>");
		case 4:
			return("<span style=\"color:green\">Nachtragsphase abgeschlossen</span>");
		default:
	} // switch
} // show_status

?>