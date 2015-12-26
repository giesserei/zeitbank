<?php
defined('_JEXEC') or die('Restricted access');

/**
 * JHTML::_('behavior.framework');
 * JHTML::_('behavior.modal');
 */

require_once(JPATH_COMPONENT . '/models/check_user.php');
require_once(JPATH_COMPONENT . '/models/arbeit_func.php');

?>

<div class="component">
    <?php

    echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid=' . MENUITEM . '">Zurück zur Übersicht</a>';

    if (check_user()) {
        echo "<h2>Zeitbank: Ämtli-Liste</h2>";
        echo "<p>Du erhältst hier eine Übersicht, wer für welches Ämtli zuständig ist und worum es geht.";
        echo get_arbeitsliste_enduser();
    } else {
        echo ZB_BITTE_ANMELDEN;
    }

    ?>
</div>