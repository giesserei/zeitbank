<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');
require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'check_user.php');

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
	/* Liste der Ämtli-Quittierungen ausgeben */
		echo "<h1>Zeitbank: Quittierung der Stunden der von dir verwalteten Ämtli</h1>";

		// Offene Quittungen ausgeben (Bestätigung dass Stunden beim aktuellen User ab, beim Antragsteller eingebucht werden)
		if(count($this->quittierungen) > 0 ):		
			echo "<table class=\"zeitbank\" >";
			echo "<tr class=\"head\">
				<th>Datum</th><th>Antrag von</th><th>Arbeitsgattung</th><th>Zeit</th><th width=\"250\">Kommentar</th><th style=\"text-align:right\">B-Nr.</th><th>&nbsp;</th></tr>";

			$k = 0;	// Zebra start
			
			foreach($this->quittierungen as $qt):
				// $style = $k ? "e9e2c8" : "EEE"; // Zebramuster
				$style = $k ? "even" : "odd"; // Zebramuster				
				// $ktext = shortenComment($qt->text);
				$ktext = $qt->text;
				
				echo "<tr class=\"".$style."\">
					<td>".JHTML::date($qt->datum_antrag,'d.m.Y')."</td><td>".$qt->name."</td><td>".
					$qt->kurztext."</td><td style=\"text-align:right;\">".$qt->minuten." min</td><td>".$ktext."</td><td style=\"text-align:right\">".$qt->id."</td>
					<td><input type=\"button\" value=\"bestätigen\" onclick=\"window.location.href='/index.php?option=com_chronoforms&chronoform=Zeitbank_quittieren&token=".$qt->cf_uid."&Itemid=".MENUITEM."'\"></td></tr>";
				$k = 1 - $k; 
			endforeach; 
			echo "</table>";
		else:
			echo "Keine offenen Quittierungen";
		endif;

else:
 echo ZB_BITTE_ANMELDEN;

endif;	// Userprüfung
?>
		<form><input type="button" name="back" value="Zurück zur Übersicht" onclick="window.location.href='/intern/zeitbank'" /></form>

</div>
