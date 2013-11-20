<?php 
defined('_JEXEC') or die('Restricted access');

$controller = JRequest::getVar('controller','groups');

if( !is_file(JPATH_COMPONENT.DS."controllers".DS.$controller.".php") ){
	JError::raiseError('Zeitbank',JTEXT::_("CONTROLLER_DONT_EXISTS"));
}
include_once(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

$className = ucfirst("{$controller}Controller");

/**
 * Create controller
 */

$controller	= new $className();
$controller->execute(JRequest::getVar('task'));
$controller->redirect();

?>