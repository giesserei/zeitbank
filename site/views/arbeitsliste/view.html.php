<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

class ZeitbankViewArbeitsliste extends JViewLegacy
{
    function display($tpl = null)
    {
        ZeitbankFrontendHelper::addComponentStylesheet();
        return parent::display($tpl);
    }
}
