<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo $this->getLinkOverview();


$this->renderTableTauschen();

