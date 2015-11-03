<?php
/*
 *  "Zentralbank": Verteilen der Pflichstunden etc.
 *  31.05.2013 jal
 */

defined('_JEXEC') or die('Restricted access');

// require_once(JPATH_BASE.DS.'components'.DS.'com_mitgliederliste'.DS.'models'.DS.'belegungsliste.php');

define ("GEWERBE_PAUSCHALE","180");	// Wieviele Quadratmeter gelten als "eine Person"
define ("HAUPTTOPF","593");
define ("AUTOARBEIT","2");  // Arbeits-ID der automatisch verbuchten Arbeitsstunden 
define ("MENUITEM","176");



// Gibt alle Bewohner einer Wohnung aus
// TODO: Auf neuer Website muss diese Funktion den neuen Gegebenheiten angepasst werden
function getBewohner($whg_nr) {
	
} // getBewohner

// Liste aller MieterInnen
// TODO: Auf neuer Website muss diese Funktion den neuen Gegebenheiten angepasst werden
function getBelegung( ) {
    $db = JFactory::getDBO();
/*    $query = "SELECT res.whg_nr,vorname,nachname,email,vr.id as vid,joomla_user_id 
    		FROM #__mgh_reservation as res,#__mgh_vorreservation as vr,#__mgh_mitgliederliste as mgl
    		LEFT JOIN #__mgh_x_vorres_mitglied as vm ON mgl.id = vm.m_id
			WHERE vm.v_id = vr.id AND res.vorres_id = vr.id AND whg_nr < 3000 AND whg_nr > 2000
			ORDER BY whg_nr";
*/
    
    $query = "SELECT * FROM #__mgh_mitglied as mgl
	    		LEFT JOIN #__mgh_x_mitglied_mietobjekt AS xmo ON mgl.userid = xmo.userid 
				ORDER BY objektid";
    $db->setQuery($query);
	$pers = $db->loadObjectList();
    return( $pers );
    
    //return($rows);
} // end getReservation


// Bestimmen der Anzahl Arbeitstage über alle Bewohner unter Berücksichtigung der Ein- und Auszugsdaten
// z.B. 5 Bewohner x 365 (ganzes Jahr) + 1 x 61 (Einzug am 1.11.) = 1886 Tage
// TODO: Auf neuer Website muss diese Funktion den neuen Gegebenheiten angepasst werden
function summeBewohnerTage() {
	$db = JFactory::getDBO();
	$summeTage = 0;
global $mainframe;			// für Fehlerausgabe
	
	$mieter = getBelegung();
	$summe = 0;
	
	foreach($mieter as $person):
/*		$query = "SELECT * FROM #__mgh_whg_mietvertrag WHERE whg_nr = '".$person->whg_nr."'"; */	
		$query = "SELECT * FROM #__mgh_mietobjekt WHERE id = '".$person->objektid."'";	
		$db->setQuery( $query );
		if($rows = $db->loadObjectList()):
			$vstart = new DateTime($rows[0]->mietvertrag_beginn);
			$jahr = new DateTime(date('Y').'-12-31');
			
			// $differenz = date_diff($vstart,$jahr);
			// Funktioniert erst ab PHP 5.30
			$differenz = $jahr->diff($vstart)->format('%a');

			// Länger als ein Jahr?
			if($differenz > 365) $differenz=365;
			
			// Wohnung?
			if($rows[0]->gewerbe_flaeche <= 0):
				$summe += $differenz;
			endif;
		endif;

// echo "Name: ".$person->vorname." ".$person->nachname." ".$person->whg_nr.", Tage/Jahr: ".$differenz->format('%a')."<br>";
	endforeach;

// $mainframe->close();
	$db->close();
	return($summe);
} // summeBewohnerTage

function getAnzahlPersonen() {
	return (count(getBelegung()));
} // getAnzahlPersonen

function summeGewerbeTage() {
	$db = JFactory::getDBO();
	$summeTage = 0;
	$summe = 0;
	$mieter = getBelegung();
	
	foreach($mieter as $person):
/*		$query = "SELECT * FROM #__mgh_whg_mietvertrag WHERE whg_nr = '".$person->whg_nr."'"; */	
		$query = "SELECT * FROM #__mgh_mietobjekt WHERE id = '".$person->objektid."'";	
		$db->setQuery( $query );
		if($rows = $db->loadObjectList()):
			$vstart = new DateTime($rows[0]->mietvertrag_beginn);
			$jahr = new DateTime(date('Y').'-12-31');

			// $differenz = date_diff($vstart,$jahr);
			// Funktioniert erst ab PHP 5.30
			$differenz = $jahr->diff($vstart)->format('%a');

			// Länger als ein Jahr?
			if($differenz > 365) $differenz=365;
			
			// Gewerbe?
			if($rows[0]->gewerbe_flaeche > 0):
				$summe += $differenz*$rows[0]->gewerbe_flaeche/GEWERBE_PAUSCHALE;
			endif;
		endif;
	endforeach;
	
	return($summe);
} // summeGewerbeTage


// Summiert die alle Kategorienbudgets 
function summeKategorien() {
	$db = JFactory::getDBO();
	$summe = 0;
	
	$query = "SELECT * FROM #__mgh_zb_kategorie";	
	$db->setQuery( $query );
	if($rows = $db->loadObjectList()):
		if($db->getAffectedRows() > 0):
			foreach($rows as $kat):
				$summe += ($kat->gesamtbudget + $kat->nachtrag);
			endforeach;
		endif;
		return($summe);
	else:
		return(-1);
	endif;	
} // summeKategorien

// Bucht aus dem Haupttopf die Kategorienbudgets um
function buchenKategorien() {
	$db = JFactory::getDBO();
	$summe = 0;
	
	$query = "SELECT * FROM #__mgh_zb_kategorie";	
	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if($db->getAffectedRows() > 0):
		foreach($rows as $kat):
			$query = "INSERT INTO #__mgh_zb_journal (minuten,belastung_userid,gutschrift_userid,datum_antrag,datum_quittung) 
				VALUES ('".$kat->gesamtbudget."','".HAUPTTOPF."','".$kat->user_id."',NOW(),NOW())";
			$db->setQuery( $query );
			if($kat->gesamtbudget > 0) $db->query();				
		endforeach;
	endif;
}

// Umbuchen der budgetierten Stunden aus dem Haupttopf an die Kategorien-User
function buchenJahresbudget($jahressoll,$totalPflichtstunden) {
	$db = JFactory::getDBO();
	$mieter = getBelegung();
global $mainframe;			// für Fehlerausgabe
	
	foreach($mieter as $person):
/*		$query = "SELECT * FROM #__mgh_whg_mietvertrag WHERE whg_nr = '".$person->whg_nr."'"; */	
		$query = "SELECT * FROM #__mgh_mietobjekt WHERE id = '".$person->objektid."'";	
		$db->setQuery( $query );
		if($rows = $db->loadObjectList()):
			$vstart = new DateTime($rows[0]->vertragsbeginn);
			$jahr = new DateTime(date('Y').'-12-31');
			
			$differenz = date_diff($vstart,$jahr);
			// Funktioniert erst ab PHP 5.30
			// $differenz = $jahr->diff($vstart);
			
			$pflichtstunden = ceil($jahressoll*$differenz->format('%a')/365);

			$kommentar="Berechnungsgrundlagen:<br />Deine Wohnungsnummer: ".$rows[0]->whg_nr."<br />Dein Mietvertragsbeginn: ".$vstart->format('d.m.Y')."<br />".
				"Daraus errechnete Anzahl Tage pro Jahr: ".$differenz->format('%a')." d/j<br /><br />Pro Bewohner errechnete Jahresleistung: ".$jahressoll." h<br />".
				"Total der Pflichstunden aller BewohnerInnen: ".$totalPflichtstunden." h<br />Ergibt deine Pflichtstunden für das laufende Jahr: ".$pflichtstunden." h";
			
			// Einmalige cf_uid erzeugen
			$uid_existiert = 1;
			$salt = 0;
			while($uid_existiert > 0):
				$cf_uid = md5($pflichtstunden.$person->joomla_user_id.time().$salt);
				$query = "SELECT * FROM #__mgh_zb_journal WHERE cf_uid='".$cf_uid."'";
				$db->setQuery( $query );
				$db->query();
				$uid_existiert = $db->getAffectedRows();
				$salt = rand(0,32767);
			endwhile;
			
			// Wohnung?
			if($rows[0]->gewerbe_flaeche <= 0):
				$query = "INSERT INTO #__mgh_zb_journal (minuten,belastung_userid,gutschrift_userid,datum_antrag,datum_quittung,arbeit_id,cf_uid) 
					VALUES ('".($pflichtstunden*60)."','".$person->joomla_user_id."','".HAUPTTOPF."',NOW(),NOW(),'".AUTOARBEIT."','".$cf_uid."')";
				$db->setQuery( $query );
				$db->query();
				if($db->getAffectedRows() > 0):	
					$query = "INSERT INTO #__mgh_zb_quit_kommentar (journal_id,text) VALUES ('".$db->insertid()."','".$kommentar."')";
					$db->setQuery( $query );
					$db->query();
				endif;	
			endif;
		endif;
	endforeach;
} // buchenJahresbudget


?>