<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

require_once(JPATH_BASE.'/components/com_zeitbank/models/check_user.php');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

// Lokales CSS laden
$doc = JFactory::getDocument();
$base = JURI::base(true);
$doc->addStyleSheet($base.'/components/com_zeitbank/template/giesserei_default.css');

$user = JFactory::getUser();
$model = $this->getModel();

?>

<div class="component">
<?php

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

echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid='.MENUITEM.'">Zurück zur Übersicht</a>';

if(check_user()) {
		echo "<h1>Zeitbank: Alle bestätigten Buchungen</h1>";
		
		echo '<table class="zeitbank" >';
		echo '<tr style="background-color: #7BB72B; color:white;">
			      <th>Datum</th>
	          <th>bekommen von</th>
	          <th>übergeben an</th>
	          <th>Arbeitsgattung</th>
	          <th style="text-align:right">Zeit<br />[min]</th>
	          <th style="text-align:right">B-Nr.</th>
	        </tr>';
		
		$k = 0;	// Zebra start
		$zaehler = 0;	
		
		foreach($this->journal as $jn) {
			// Der Schenker bleibt anonym & die Buchungsdetails können nicht betrachtet werden
		  $isGeschenk = $jn->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;  
		  $isFreiwillig = $jn->art === 'freiwillig';
		
		  $op_sign = ($jn->belastung_userid == $user->id ? "-" : "+");
		  $op_sign = ($isFreiwillig ? "" : $op_sign); // kein Vorzeichen bei Freiwilligenarbeit
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
			$styleMinuten = $isFreiwillig ? "color:#888888" : "";
			echo '<tr style="vertical-align:top; background-color: #'.$style.'">
				      <td>'.getBuchungsLink($isGeschenk, $jn, $user).'</td>
              <td>'.$geber_name.'</td>
              <td>'.$empf_name.'</td>
              <td>'.$jn->kurztext.'</td>
	            <td style="text-align:right;'.$styleMinuten.'">'.($isFreiwillig ? "(" : "").$op_sign.$jn->minuten.($isFreiwillig ? ")" : "").'</td>
	            <td style="text-align:right">'.$jn->id.'</td>
		        </tr>';
			$k = 1 - $k; 
		}
		echo "</table>";
}
else {
  echo ZB_BITTE_ANMELDEN;
};
?>

</div>
