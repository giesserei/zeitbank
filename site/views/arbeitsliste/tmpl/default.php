<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'check_user.php');
require_once(JPATH_BASE .DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'arbeit_func.php');

// Lokales CSS laden
$doc = JFactory::getDocument();
$base = JURI::base(true);
$doc->addStyleSheet($base.'/components/com_zeitbank/template/giesserei_default.css');

?>

<div class="component">
<?php

if(check_user()):
	echo "<h2>Zeitbank: Ämtli-Liste</h2>";
	echo "<p>Du erhältst hier eine Übersicht, wer für welches Ämtli zuständig ist und worum es geht.";
	echo get_arbeitsliste_enduser();
else:
  echo ZB_BITTE_ANMELDEN;

endif;	// Userprüfung
?>

</div>
