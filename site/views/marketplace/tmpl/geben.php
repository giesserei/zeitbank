<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo $this->getLinkOverview();

// -------------------------------------------------------------------------
// Angebote von Dienstleistungen zum Eintauschen von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableGeben();

