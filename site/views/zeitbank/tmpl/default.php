<?php

/* Zeitbank Hauptseite 
 * 24.7.2012 jal
 * 
 * */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');
require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'check_user.php');
require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'arbeit_func.php');
require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'kategorie_func.php');

// Lokales CSS laden
$doc = JFactory::getDocument();
$base = JURI::base(true);
$doc->addStyleSheet($base.'/components/com_zeitbank/template/giesserei_default.css');


$max_journal_buchungen = 10000;

$user =& JFactory::getUser();
$model =& $this->getModel();


function shortenComment($ktext) {
	// Kommentar kürzen. falls nötig
	if(strlen($ktext) > 35):
		$ktext = substr($ktext,0,35)."...";
	endif;
	return($ktext);
} // shortenComment


?>

<div class="component">
<?php

if(check_user()):

  echo '<div style="color:red;font-size:14pt;margin-bottom:20px;border-width:1px; border-color:red; border-style:solid;padding:5px">';
  echo "Die Zeitbank ist f�r 2014 vorbereitet. Alle ab jetzt vorgenommenen Buchungen gelten f�r das Jahr 2014.";
  echo '</div>';

	// Kategorien-Administrator?
	if($kategorie=check_kat_admin(0)):
		echo "<h1>Zeitbank: Du bist Kategorien-Administrator</h1>";
		echo "<p>Status: ".show_status(get_status($kategorie))."</p>";
		echo "<p>Du kannst:";
		echo "<ul><li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Manager&Itemid=".MENUITEM."\">Deine Ämtli-Verantwortlichen verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Budget&Itemid=".MENUITEM."\">Dein Kategorienbudget verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Amt_Zuweisen&Itemid=".MENUITEM."\">Ämtli-Zuteilung anpassen</a></li></ul></p><br /><br />";
	endif;
		
	// Ämtli-Administrator?
	if(check_arbeit_admin(0,MENUITEM)):
		echo "<h1>Zeitbank: Du bist Ämtli-Administrator</h1>";
		echo "Du kannst:";
		echo "<ul><li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Amt_Manager&Itemid=".MENUITEM."\">Ämtli verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittung_amt&Itemid=".MENUITEM."\">Anträge quittieren</a> (offene Anträge: ".get_anzahl_offen().")</li>";
		echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittungsliste_amt&Itemid=".MENUITEM."\">Quittierte Buchungen anzeigen</a></li></ul><br /><br />";
	endif;
	
	/* Liste der persönlichen Zeitbankauszüge ausgeben */
		echo "<h1><a href=\"index2.php?option=com_zeitbank&Itemid=".MENUITEM."&view=zeitbank\"	target=\"_blank\">
			<img src=\"/images/M_images/printButton.png\" style=\"float: right;\"></a>Zeitbank: Dein Konto</h1>";

?>

<?php 		
		echo "<h4>Offene Quittierungen (von anderen an dich geleistete Stunden)</h4>";

		// Offene Quittungen ausgeben (Bestätigung dass Stunden beim aktuellen User ab, beim Antragsteller eingebucht werden)
		if(count($this->quittierungen) > 0 ):		
			echo "<table class=\"zeitbank\" >";
			echo "<tr class=\"head\">
				<th>Datum</th><th>Antrag von</th><th>Arbeitsgattung</th><th>Zeit<br />[min]</th><th>Kommentar</th><th style=\"text-align:right\">B-Nr.</th><th>&nbsp;</th></tr>";

			$k = 0;	// Zebra start
			
			foreach($this->quittierungen as $qt):
				// $style = $k ? "e9e2c8" : "EEE"; // Zebramuster
				$style = $k ? "even" : "odd"; // Zebramuster				
				$ktext = shortenComment($qt->text);
				echo "<tr class=\"".$style."\">
					<td>".JHTML::date($qt->datum_antrag,'d.m.Y')."</td><td>".$qt->name."</td><td>".
					$qt->kurztext."</td><td style=\"text-align:right;\">".$qt->minuten."</td><td>".$ktext."</td><td style=\"text-align:right\">".$qt->id."</td>
					<td><input type=\"button\" value=\"bestätigen\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_quittieren&token=".$qt->cf_uid."&Itemid=".MENUITEM."'\"></td></tr>";
				$k = 1 - $k; 
			endforeach; 
			echo "</table>";
		else:
			echo "Keine offenen Quittierungen";
		endif;

		echo "<br /><br /><h4>Offene Anträge (von dir geleistete, unquittierte Stunden)</h4>";
		
		// Offene Anfragen (Einträge auflisten, für welche der aktuelle User Stunden geleistet hat, aber noch nicht bestätigt wurden
		if(count($this->antraege) > 0 ):		
			echo "<table class=\"zeitbank\" >";
			echo "<tr class=\"head\">
				<th>Datum</th><th>Antrag an</th><th>Arbeitsgattung</th><th>Zeit<br />[min]</th><th>Kommentar</th><th>B-Nr.</th><th>&nbsp;</th></tr>";

			$k = 0;	// Zebra start
			
			foreach($this->antraege as $at):
				// $style = $k ? "e9e2c8" : "EEE"; // Zebramuster				
				$style = $k ? "even" : "odd"; // Zebramuster				
				$ktext = shortenComment($at->text);
				// echo "<tr style=\"vertical-align:top; background-color: #".$style."\">
				echo "<tr class=\"".$style."\">
				<td>".JHTML::date($at->datum_antrag,'d.m.Y')."</td><td>".$at->name."</td><td>".
					$at->kurztext."</td><td style=\"text-align:right;\">".$at->minuten."</td><td>".$ktext."</td><td style=\"text-align:right\">".$at->id."</td>
					<td><input type=\"button\" value=\"ändern\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_Buchung&token=".$at->cf_uid."&Itemid=".MENUITEM."'\"/>
					<input type=\"button\" value=\"löschen\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_sicher_loeschen&token=".$at->cf_uid."&Itemid=".MENUITEM."'\"/></td></tr>";
				$k = 1 - $k;   
			endforeach; 
			echo "</table>";
		else:
			echo "Keine offenen Anträge";
		endif;
		
		echo "<br /><input type=\"button\" value=\"Neuer Antrag\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_Buchung&Itemid=".MENUITEM."'\" />";

		
		// Alle verbuchten Posten aus dem Journal
		echo "<br /><br /><br /><h4>Bestätigte Buchungen des <span style=\"color:#9C2215\">laufenden</span> Jahres</h4>";
		
		if(count($this->journal) > 0 ):		
		
			$saldo = 0;
			foreach($this->journal as $jn):
				if($jn->belastung_userid == $user->id):
					$saldo -= $jn->minuten;
				else:
					$saldo += $jn->minuten;
				endif;
			endforeach;
			
			echo "<p>Dein Jahressaldo ".date('Y').": <strong>".$model->showTime($saldo)."h</strong>"; 
			echo "</p>";
			echo "<table class=\"zeitbank\" >";
			echo "<tr style=\"background-color: #7BB72B; color:white;\">
				<th style=\"text-align:right\">Datum</th><th>bekommen von</th><th>übergeben an</th><th>Arbeitsgattung</th><th>Zeit<br />[min]</th><th>Saldo<br />[h:m]</th><th>B-Nr.</th></tr>";
			
			$k = 0;	// Zebra start
			$zaehler = 0;
			
			foreach($this->journal as $jn):
				if($zaehler < $max_journal_buchungen):
					$zaehler++;
					if($jn->belastung_userid != $user->id):
						$geber_name = $model->getUserName($jn->belastung_userid);
						$empf_name = "";
						$op_sign = "+";
					else:
						$geber_name = "";
						$empf_name = $model->getUserName($jn->gutschrift_userid);
						$op_sign = "-";
					endif;
	
					$style = $k ? "e9e2c8" : "EEE"; // Zebramuster				
					echo "<tr style=\"vertical-align:top; background-color: #".$style."\">
						<td><a href=\"index.php?option=com_zeitbank&view=buchung&Itemid=".MENUITEM."&token=".$jn->cf_uid."\">".JHTML::date($jn->datum_antrag,'d.m.Y')."</a></td><td>".$geber_name."</td><td>".$empf_name."</td><td>".
						$jn->kurztext."</td><td style=\"text-align:right\">".$op_sign.$jn->minuten."</td>
						<td style=\"text-align:right;";

					if($saldo < 0) echo " color:red;"; 
					
					echo "\">".$model->showTime($saldo)."</td><td style=\"text-align:right\">".$jn->id."</td></tr>";
					$k = 1 - $k; 
					if($jn->belastung_userid != $user->id):		// Umgekehrte Sortierung!
						$saldo -= $jn->minuten; 
					else:
						$saldo += $jn->minuten;
					endif;
				endif;
			endforeach;
			echo "</table>";
		else:
			// Noch keine Buchungen diese Jahr: Darum Saldo = 0
			echo "<p>Dein Jahressaldo ".date('Y').": <strong>0 h</strong></p>";
			echo "<p>Noch keine Buchungen vorhanden</p>"; 
		endif;
		
		echo "<br /><br />Saldo des Vorjahres (".date('Y',time() - (365 * 24 * 60 * 60))."): <strong>".$model->showTime($this->saldo_vorjahr)."h</strong>";
		
		echo "<br /><br /><input type=\"button\" value=\"Alle Buchungen anzeigen\" onclick=\"window.location.href='index.php?option=com_zeitbank&view=userJournal&Itemid=".MENUITEM."'\"/>";
else:
 echo ZB_BITTE_ANMELDEN;
endif;	// Userprüfung
?>

</div>
