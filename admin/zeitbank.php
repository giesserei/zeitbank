<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

jimport('joomla.application.component.controller');

if (!JFactory::getUser()->authorise('core.manage', 'com_zeitbank')) {
    throw new Exception("Zugriff nicht erlaubt");
}

$controller = JControllerLegacy::getInstance('Zeitbank');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();