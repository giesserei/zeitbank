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

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

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

/**
 * Buchungslink bei einem Geschenk nur anzeigen, wenn es die eigene Buchung ist.
 */
function getBuchungsLink($isGeschenk, $jn, $user) {
  $belastung = $jn->belastung_userid == $user->id;
  
  if ($isGeschenk && !$belastung) {
    return JHTML::date($jn->datum_antrag,'d.m.Y');
  }
  else {
    return '<a href="index.php?option=com_zeitbank&view=buchung&Itemid='.MENUITEM.'&token='.$jn->cf_uid.'">'.JHTML::date($jn->datum_antrag,'d.m.Y').'</a>';
  }
}

echo '<div class="component">';

if(check_user()):

  //echo '<div style="color:red;font-size:14pt;margin-bottom:20px;border-width:1px; border-color:red; border-style:solid;padding:5px">';
  //echo "Die Zeitbank ist für das Jahr 2014 vorbereitet. Alle ab jetzt vorgenommenen Buchungen gelten für das Jahr 2014.";
  //echo '</div>';

	// Kategorien-Administrator?
	if($kategorie=check_kat_admin(0)):
		echo "<h1>Zeitbank: Du bist Kategorien-Administrator</h1>";
		echo "<p>Status: ".show_status(get_status($kategorie))."</p>";
		echo "<p>Du kannst:";
		echo "<ul><li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Manager&Itemid=".MENUITEM."\">Deine Ämtli-Verantwortlichen verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Budget&Itemid=".MENUITEM."\">Dein Kategorienbudget verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Kategorie_Amt_Zuweisen&Itemid=".MENUITEM."\">Ämtli-Zuteilung anpassen</a></li></ul></p><br />";
	endif;
		
	// Ämtli-Administrator?
	if(check_arbeit_admin(0,MENUITEM)):
		echo "<h1>Zeitbank: Du bist Ämtli-Administrator</h1>";
		echo "Du kannst:";
		echo "<ul><li><a href=\"/index.php?option=com_chronoforms&chronoform=Zeitbank_Amt_Manager&Itemid=".MENUITEM."\">Ämtli verwalten</a></li>";
		echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittung_amt&Itemid=".MENUITEM."\">Anträge quittieren</a> (offene Anträge: ".get_anzahl_offen().")</li>";
		echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittungsliste_amt&Itemid=".MENUITEM."\">Quittierte Buchungen anzeigen</a></li></ul><br />";
	endif;
	
	// Berechtigung für Reports?
	if (ZeitbankFrontendHelper::hasReportPermission()) {
    echo "<h1>Zeitbank: Du hast Zugriff auf die Zeitbank-Reports</h1>";
    echo "Du kannst:";
    echo '<ul><li><a href="index.php?option=com_zeitbank&view=report&Itemid='.MENUITEM.'">Reports erstellen</a></li></ul><br />';
  }
	
  /* Allgemeine Funktionen*/
	echo "<h1>Zeitbank: Allgemeine Funktionen</h1>";
	echo '<ul>
		      <li><a href="/index.php?option=com_zeitbank&Itemid='.MENUITEM.'&view=marketplace">Arbeitsangebote und Angebote zum Stundentausch</a> <span style="color:red">NEU</span></li>
		      <li><a href="/index.php?option=com_zeitbank&Itemid='.MENUITEM.'&view=arbeitsliste">Liste mit allen Ämtli und Zuständigkeiten</a></li>
		    </ul>
		    <br /><br />';
	
	/* Liste der persönlichen Zeitbankauszüge ausgeben */
	/*
  echo "<h1><a href=\"index2.php?option=com_zeitbank&Itemid=".MENUITEM."&view=zeitbank\"	target=\"_blank\">
			<img src=\"/images/M_images/printButton.png\" style=\"float: right;\"></a>Zeitbank: Dein Konto</h1>";
	*/

		
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
		
		
		echo '<br />
          <fieldset>
            <input type="button" value="Neuer Antrag" onclick="window.location.href=\'/index.php?option=com_chronoforms&chronoform=Zeitbank_Buchung&Itemid='.MENUITEM.'\'" />
		        <input type="button" value="Stunden verschenken" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=stundengeschenk.edit&Itemid='.MENUITEM.'\'" /><span style="color:red"> NEU</span>
		      </fieldset>';

		
		// Alle verbuchten Posten aus dem Journal
		// TODO (SF) Code benötigt dringend Refactoring -> Für jede Buchung gibt es eine DB-Anfrage, um den Namen des Benutzers zu holen
		echo "<br /><br /><h4>Bestätigte Buchungen des <span style=\"color:#9C2215\">laufenden</span> Jahres</h4>";
		
		if(count($this->journal) > 0 ):		
		
			$saldo = $this->getSaldo();
			
			echo '<div style="margin-bottom:10px">
			        Dein Jahressaldo '.date('Y').': <strong>'.ZeitbankFrontendHelper::formatTime($saldo).'h</strong><br/>
			        Dein Stundensoll '.date('Y').': <strong>'.ZeitbankFrontendHelper::formatTime($this->getSoll()).'h</strong>
			      </div>';
			
			echo "<table class=\"zeitbank\" >";
			echo '<tr style="background-color: #7BB72B; color:white;">
				      <th>Datum</th>
		          <th>bekommen von</th>
		          <th>übergeben an</th>
		          <th>Arbeitsgattung</th>
		          <th style="text-align:right">Zeit<br />[min]</th>
		          <th style="text-align:right">Saldo<br />[h:m]</th>
		          <th style="text-align:right">B-Nr.</th>
		        </tr>';
			
			$k = 0;	// Zebra start
			$zaehler = 0;
			
			foreach($this->journal as $jn) {
				if($zaehler < $max_journal_buchungen):
					$zaehler++;
				  // Der Schenker bleibt anonym & die Buchungsdetails können nicht betrachtet werden
				  $isGeschenk = $jn->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;  
				
				  $op_sign = ($jn->belastung_userid == $user->id ? "-" : "+");
          $geber_name = "";
          $empf_name = "";
          
          if ($isGeschenk) {
            if ($jn->belastung_userid == $user->id) {
              $empf_name = $model->getUserName($jn->gutschrift_userid);
            }
          }
					else {
  					if ($jn->belastung_userid != $user->id) {
  						$geber_name = $model->getUserName($jn->belastung_userid);
  				  }
  					else {
  						$empf_name = $model->getUserName($jn->gutschrift_userid);
  					}
          }
	
					$style = $k ? "e9e2c8" : "EEE"; // Zebramuster				
					echo '<tr style="vertical-align:top; background-color: #'.$style.'">
						      <td>'.getBuchungsLink($isGeschenk, $jn, $user).'</td>
		              <td>'.$geber_name.'</td>
		              <td>'.$empf_name.'</td>
		              <td>'.$jn->kurztext.'</td>
			            <td style="text-align:right">'.$op_sign.$jn->minuten.'</td>
						      <td style="text-align:right;'.($saldo < 0 ? 'color:red;"' : '"').'>'.ZeitbankFrontendHelper::formatTime($saldo).'</td>
			            <td style="text-align:right">'.$jn->id.'</td>
				        </tr>';
					$k = 1 - $k; 
					if($jn->belastung_userid != $user->id) {
						$saldo -= $jn->minuten; 
					}
					else {
						$saldo += $jn->minuten;
					}
				endif;
			}
			echo "</table>";
		else:
			// Noch keine Buchungen diese Jahr: Darum Saldo = 0
			echo "<p>Dein Jahressaldo ".date('Y').": <strong>0 h</strong></p>";
			echo "<p>Noch keine Buchungen vorhanden</p>"; 
		endif;
		
		echo "<br /><br />Saldo des Vorjahres (".date('Y',time() - (365 * 24 * 60 * 60))."): <strong>".ZeitbankFrontendHelper::formatTime($this->saldo_vorjahr)."h</strong>";
		
		echo "<br /><br /><input type=\"button\" value=\"Alle Buchungen anzeigen\" onclick=\"window.location.href='index.php?option=com_zeitbank&view=userJournal&Itemid=".MENUITEM."'\"/>";
else:
 echo ZB_BITTE_ANMELDEN;
endif;	// Userprüfung

echo '<div style="color:#888888;margin-top:10px;text-align:right">Release: '.ZeitbankConst::RELEASE.'</div>';

echo "</div>";
