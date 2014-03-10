<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo $this->getLinkZeitbank();

echo '<h1>Arbeitsangebote und Marktplatz für den Stundentausch</h1>';

echo '<div style="margin-top:10px">';
echo 'Du kannst:';
echo '  <ul>';

if ($this->hasEntries()) {
  echo '    <li><a href="index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$this->menuId.'">Deine Einträge verwalten</a></li>';
}

if (ZeitbankFrontendHelper::isAemtliAdmin()) {
  echo '  <li><a href="index.php?option=com_zeitbank&task=updangebot.edit&id=0&art=1&Itemid='.$this->menuId.'">Ein neues Arbeitangebot erstellen</a></li>';  
}  

echo '    <li><a href="index.php?option=com_zeitbank&task=updangebot.edit&id=0&art=2&Itemid='.$this->menuId.'">Einen neuen Eintrag für den Stundentausch erstellen</a></li>';
echo '  </ul>';
echo '</div>';

$this->renderTableArbeiten();

$this->renderTableTauschen();

//echo '<div style="margin-top:50px;color:red">Version: 0.84 - 2014-03-04</div>';

