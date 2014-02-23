<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo '<div style="margin-top:10px">
        <a href="index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$this->menuId.'">Verwaltung deiner Angebote</a>
      </div>';

// -------------------------------------------------------------------------
// Angebote zum Bezug von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableBeziehen();

// -------------------------------------------------------------------------
// Angebote von Dienstleistungen zum Eintauschen von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableTauschen();

