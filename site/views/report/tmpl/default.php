<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');


echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid='.$this->menuId.'">Zurück zur Zeitbank</a><p/>';

echo '<h1>Zeitbank: Kennzahlen und Reports</h1><p/>';

// Reports
echo '<div style="margin-top:10px">';
echo '  <ul>';
if (ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_DOWNLOAD_SALDO)) {
  echo '    <li><a href="index.php?option=com_zeitbank&task=report.kontosaldo&format=raw">Download: Aktuelle Saldos aller Mitglieder</a></li>';
  echo '    <li><a href="index.php?option=com_zeitbank&task=report.kontosaldoVorjahr&format=raw">Download: Saldos aller Mitglieder des Vorjahres</a></li>';
}
echo '  </ul>';
echo '</div>';

// Kennzahlen
echo '<h3 style="margin-bottom:10px">Kennzahlen</h3>';

echo '<table class="zeitbank" style="width:700px">';
echo '<tr class="head">
				<th>Kennzahl</th>
        <th>Wert</th>
      </tr>';
echo '<tr class="zb_odd">
        <td>Summe der verbuchten Arbeitsstunden (ohne privaten Stundentausch)</td>
        <td>'.$this->getSummeArbeitStunden().' Stunden</td>
		  </tr>';
echo '<tr class="zb_even">
        <td>Summe der nicht quittierten Arbeitsstunden (ohne privaten Stundentausch)</td>
        <td>'.$this->getSummeNichtQuittierteStunden().' Stunden</td>
		  </tr>';
echo '<tr class="zb_odd">
        <td>Durchschnittliche Dauer bis zur Quittierung</td>
        <td>'.$this->getQuittungDauer()->avg_dauer.' Tage</td>
		  </tr>';
echo '<tr class="zb_even">
        <td>Durchschnittliche Wartezeit der bis heute nicht quittierten Buchungen</td>
        <td>'.$this->getWartezeitUnquittierteBuchungen().' Tage</td>
		  </tr>';
echo "</table>";

echo '<p/>';

echo '<h3 style="margin-bottom:10px">Verbuchte Stunden je Kategorie</h3>';

echo '<table class="zeitbank" style="width:700px">';
echo '<tr class="head">
				<th>Kategorie</th>
        <th>Jahresbudget</th>  
        <th>Budget pro rata temporis</th>  
        <th>Stunden verbucht</th>    
      </tr>';

$i = 0;

$giessereiStundenJeKategorie = $this->getSummeGiessereiStundenNachKategorie();
$totalBudget_G = 0;
$totalProRata_G = 0;
$totalSaldo_G = 0;

$sonstigeStundenJeKategorie = $this->getSummeSonstigeStundenNachKategorie();
$totalBudget_S = 0;
$totalProRata_S = 0;
$totalSaldo_S = 0;

foreach($giessereiStundenJeKategorie as $kat) {
  $style = $i % 2 == 0 ? "zb_even" : "zb_odd";
  echo '<tr class="'.$style.'">
          <td>'.$kat->bezeichnung.'</td>
          <td>'.$kat->gesamtbudget.'</td>
          <td>'.$kat->budget_pro_rata.'</td>
          <td>'.$kat->saldo.'</td>
				</tr>';
  $i ++;
  
  $totalBudget_G += $kat->gesamtbudget;
  $totalProRata_G += $kat->budget_pro_rata;
  $totalSaldo_G += $kat->saldo;
}

$style = $i % 2 == 0 ? "zb_even" : "zb_odd";
echo '<tr class="'.$style.'">
        <td><strong>Summe Giessereistunden</strong></td>
        <td><strong>'.$totalBudget_G.'</strong></td>
        <td><strong>'.$totalProRata_G.'</strong></td>
        <td><strong>'.$totalSaldo_G.'</strong></td>
      </tr>';
$i ++;

foreach($sonstigeStundenJeKategorie as $kat) {
  $style = $i % 2 == 0 ? "zb_even" : "zb_odd";
  echo '<tr class="'.$style.'">
          <td>'.$kat->bezeichnung.'</td>
          <td>'.$kat->gesamtbudget.'</td>
          <td>'.$kat->budget_pro_rata.'</td>
          <td>'.$kat->saldo.'</td>
				</tr>';
  $i ++;

  $totalBudget_S += $kat->gesamtbudget;
  $totalProRata_S += $kat->budget_pro_rata;
  $totalSaldo_S += $kat->saldo;
}

/*
$style = $i % 2 == 0 ? "zb_even" : "zb_odd";
echo '<tr class="'.$style.'">
        <td><strong>Summe sonstige Stunden</strong></td>
        <td><strong>'.$totalBudget_S.'</strong></td>
        <td><strong>'.$totalProRata_S.'</strong></td>
        <td><strong>'.$totalSaldo_S.'</strong></td>
      </tr>';
$i ++;

$style = $i % 2 == 0 ? "zb_even" : "zb_odd";
echo '<tr class="'.$style.'">
        <td><strong>Total</strong></td>
        <td><strong>'.($totalBudget_G+$totalBudget_S).'</strong></td>
        <td><strong>'.($totalProRata_G+$totalProRata_S).'</strong></td>
        <td><strong>'.($totalSaldo_G+$totalSaldo_S).'</strong></td>
      </tr>';
*/

echo "</table>";