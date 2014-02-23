<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

// -------------------------------------------------------------------------
// Deine Einträge
// -------------------------------------------------------------------------

$this->renderTableMeineAngebote();

// -------------------------------------------------------------------------
// Angebote zum Bezug von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableBeziehen();

// -------------------------------------------------------------------------
// Angebote von Dienstleistungen zum Eintauschen von Giessereistunden
// -------------------------------------------------------------------------

$this->renderTableTauschen();

