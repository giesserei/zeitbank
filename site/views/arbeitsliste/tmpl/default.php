<?php
defined('_JEXEC') or die('Restricted access');

?>

<div class="component">
    <?php

    echo '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId . '">Zurück zur Übersicht</a>';

    echo "<h2>Zeitbank: Ämtli-Liste</h2>";
    echo "<p>Du erhältst hier eine Übersicht, wer für welches Ämtli zuständig ist und worum es geht.";

    echo $this->getArbeitsliste();

    ?>
</div>