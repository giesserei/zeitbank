<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

require_once(JPATH_BASE.'/components/com_zeitbank/models/check_user.php');

?>

<div class="component">

<?php

echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid='.MENUITEM.'">Zurück zur Übersicht</a><p/>';

if (check_user()) {
		echo "<h1>Zeitbank: Liste aller bestätigten Ämtli-Buchungen</h1>";
		
		echo '<table class="zeitbank">';
		echo '<tr class="head">
				    <th>Datum</th>
		        <th>übergeben an</th>
		        <th>Arbeitsgattung</th>
		        <th style="text-align:right">Zeit<br />[min]</th>
		        <th style="text-align:right">B-Nr.</th>
		      </tr>';
		
		$k = 0;
		foreach($this->quittungsliste as $jn) {
			$style = $k ? "zb_even" : "zb_odd";	
			echo '<tr class="'.$style.'">
				      <td>'.ZeitbankFrontendHelper::getLinkBuchung($jn->id, JHTML::date($jn->datum_antrag,'d.m.Y')).'</td>
		          <td>'.$jn->konto_gutschrift.'</td>
		          <td>'.$jn->kurztext.'</td>
		          <td style="text-align:right">'.$jn->minuten.'</td>
 			        <td style="text-align:right">'.$jn->id.'</td>
		        </tr>';
			$k = 1 - $k; 
		}
		echo "</table><br/>";
		echo "<form>".$this->pagination->getListFooter();
		$itemid = JRequest::getVar('Itemid');
		echo '<input type="hidden" name="option" value="com_zeitbank" />';
		echo '<input type="hidden" name="Itemid" value="'.$itemid.'" />';
		echo '<input type="hidden" name="view" value="userJournal" /></form>';
}
else {
  echo ZB_BITTE_ANMELDEN;
}
?>

</div>
