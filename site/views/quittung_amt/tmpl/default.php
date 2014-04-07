<?php
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE.'/components/com_zeitbank/models/check_user.php');
JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
?>

<div class="component">

<?php

echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid='.MENUITEM.'">Zurück zur Übersicht</a><p/>';

if(check_user()) {
		echo '<h1 class="zeitbank">Zeitbank: Quittierung der Stunden der von dir verwalteten Ämtli</h1>';

		// Offene Quittungen ausgeben (Bestätigung dass Stunden beim aktuellen User ab, beim Antragsteller eingebucht werden)
		if (!empty($this->quittierungen)) {	
			echo '<table class="zeitbank" >';
			echo '<tr class="head">
				      <th>Datum</th>
              <th>Antrag von</th>
              <th>Arbeitsgattung</th>
              <th style="text-align:right;">Zeit<br/>[min]</th>
              <th width="250">Kommentar</th>
              <th style="text-align:right">B-Nr.</th>
              <th>&nbsp;</th>
            </tr>';

			$k = 0;
			foreach($this->quittierungen as $qt) {
				$style = $k ? "even" : "odd";	
				
				echo '<tr class="'.$style.'">
					      <td>'.JHTML::date($qt->datum_antrag,'d.m.Y').'</td>
                <td>'.$qt->konto_gutschrift.'</td>
                <td>'.$qt->kurztext.'</td>
                <td style="text-align:right;">'.$qt->minuten.'</td>
			          <td>'.ZeitbankFrontendHelper::cropText($qt->text, 30, true).'</td>
				        <td style="text-align:right">'.$qt->id.'</td>
					      <td>
				          <input type="button" value="bestätigen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=quittung.edit&id='.$qt->id.'&Itemid='.MENUITEM.'\'">
                </td>
              </tr>';
				$k = 1 - $k; 
			} 
			echo '</table>';
		}
		else {
			echo "Keine offenen Quittierungen";
		}
}
else {
  echo ZB_BITTE_ANMELDEN;
}
?>
</div>
