<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'check_user.php');

$user =& JFactory::getUser();
$model =& $this->getModel();


?>

<div class="component">
<?php

if(check_user()):
		echo "<h1>Zeitbank: Alle bestätigten Buchungen</h1>";
		echo "<form>".$this->pagination->getListFooter();
		$itemid = JRequest::getVar('Itemid');
		echo '<input type="hidden" name="option" value="com_zeitbank" />';
		echo '<input type="hidden" name="Itemid" value="'.$itemid.'" />';
		echo '<input type="hidden" name="view" value="userJournal" /></form>';
		
		echo "<table class=\"zeitbank\" >";
		echo "<tr style=\"background-color: #7BB72B; color:white;\">
				<th style=\"text-align:right\">Datum</th><th>bekommen von</th><th>übergeben an</th><th>Arbeitsgattung</th><th>Zeit<br />[min]</th><th>B-Nr.</th></tr>";
		
		$k = 0;	// Zebra start
		$zaehler = 0;	
		
		foreach($this->journal as $jn):
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
				$jn->kurztext."</td><td style=\"text-align:right\">".$op_sign.$jn->minuten."</td>";

			echo "<td style=\"text-align:right\">".$jn->id."</td></tr>";

			$k = 1 - $k; 

		endforeach;
		echo "</table>";
		echo "<form>".$this->pagination->getListFooter();
		$itemid = JRequest::getVar('Itemid');
		echo '<input type="hidden" name="option" value="com_zeitbank" />';
		echo '<input type="hidden" name="Itemid" value="'.$itemid.'" />';
		echo '<input type="hidden" name="view" value="userJournal" /><br />
		<input type="button" name="back" value="Zurück zur Übersicht" onclick="window.location.href=\'/intern/zeitbank\'" />
		</form>';
else:
  echo ZB_BITTE_ANMELDEN;

endif;	// Userprüfung
?>

</div>
