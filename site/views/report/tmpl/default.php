<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid='.$this->menuId.'">Zurück zur Zeitbank</a><p/>';

echo '<h1>Zeitbank: Reports</h1><p/>';

echo '<div style="margin-top:10px">';
echo '  <ul>';
echo '    <li><a href="index.php?option=com_zeitbank&task=report.kontosaldo&format=raw">Download: Aktuelle Saldos aller Mitglieder</a></li>';
echo '  </ul>';
echo '</div>';