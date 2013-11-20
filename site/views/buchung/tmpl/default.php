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
		echo "<h1>Zeitbank: Buchungsdetail</h1>";
		echo "<ul><li>Beleg-Nummer: <strong>".$this->Buchung->id."</strong> vom <strong>".JHTML::date($this->Buchung->datum_antrag,'d.m.Y')."</strong></li>";
		echo "<li>Antrag von: <strong>".$this->Buchung->gut_name."</strong></li>";
		echo "<li>Quittiert von: <strong>".$this->Buchung->bel_name."</strong> am <strong>".JHTML::date($this->Buchung->datum_quittung,'d.m.Y')."</strong></li>";
		echo "<li>Arbeitsgattung: <strong>".$this->Buchung->kurztext."</strong></li>";
		echo "<li>Zeitbetrag: <strong>".$this->Buchung->minuten." Minuten</strong></li>";
		$akom = $model->getBelastungskommentar( $this->Buchung->id );
		if(strlen($akom) > 0) echo "<li>Antragskommentar:<br><strong>".nl2br($akom)."</strong></li>";
		$qkom = $model->getQuittierungskommentar( $this->Buchung->id );
		if(strlen($qkom) > 0) echo "<li>Quittierungskommentar:<br><strong>".nl2br($qkom)."</strong></li>";
		echo "</ul><br />";
		echo "<input type='button' name='back' value='Zurück zur Übersicht' onclick='history.back()' />";
else:
  echo ZB_BITTE_ANMELDEN;

endif;	// Userprüfung
?>

</div>
