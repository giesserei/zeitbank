<?php 
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

jimport('joomla.application.component.view');

class ZeitbankViewArbeitsliste extends JViewLegacy {
  function display($tpl = null) {
    ZeitbankFrontendHelper::addComponentStylesheet();
    return parent::display($tpl);
  }
}
