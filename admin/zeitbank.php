<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('zeitbank');
$input = JFactory::getApplication()->input;
$controller->execute($input->get('task'));
$controller->redirect();