<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo $this->getLinkOverview();

// -------------------------------------------------------------------------
// Angebote zum Bezug von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableBeziehen();

