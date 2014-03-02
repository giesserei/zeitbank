<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo '<div style="margin-top:10px">';
echo '  <ul>';

if ($this->hasEntries()) {
  echo '    <li><a href="index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$this->menuId.'">Verwaltung deiner Einträge</a></li>';
}

if (ZeitbankFrontendHelper::isAemtliAdmin()) {
  echo '  <li><a href="index.php?option=com_zeitbank&task=updangebot.edit&id=0&art=1&Itemid='.$this->menuId.'">Neues Arbeitangebots erstellen</a></li>';  
}  

echo '    <li><a href="index.php?option=com_zeitbank&task=updangebot.edit&id=0&art=2&Itemid='.$this->menuId.'">Neuen Eintrag für Stundentausch erstellen</a></li>';
echo '  </ul>';
echo '</div>';

$this->renderTableArbeiten();

$this->renderTableTauschen();

echo '<div style="margin-top:50px;color:red">Version: 0.83 - 2014-03-02</div>';

