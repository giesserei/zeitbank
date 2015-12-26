<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

require_once(JPATH_COMPONENT . '/models/check_user.php');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

?>

<div class="component" style="margin-left:25px">
    <?php
    echo '<h1 style="color: #7BA428">Zeitbank: Buchungsdetails</h1>';

    if ($this->isGeschenkEmpfaenger($this->buchung->arbeit_id, $this->buchung->gutschrift_userid)) {
        echo 'Dir wurden <strong>' . $this->buchung->minuten . ' Minuten</strong> geschenkt.';
    } else {
        echo '<ul>
            <li>Beleg-Nummer: <strong>' . $this->buchung->id . '</strong> vom <strong>' . JHTML::date($this->buchung->datum_antrag, 'd.m.Y') . '</strong></li>';

        // Bei Stundengeschenken gibt es keinen Antrag
        if ($this->isGeschenk($this->buchung->arbeit_id)) {
            echo "<li>EmpfängerIn: <strong>" . $this->buchung->konto_gutschrift . "</strong></li>";
        } else {
            echo "<li>Antrag von: <strong>" . $this->buchung->konto_gutschrift . "</strong></li>";
            echo "<li>Quittiert von: <strong>" . $this->buchung->konto_belastung . "</strong> am <strong>" . JHTML::date($this->buchung->datum_quittung, 'd.m.Y') . "</strong></li>";
        }

        echo "<li>Arbeitsgattung: <strong>" . $this->buchung->kurztext . "</strong></li>";
        echo "<li>Zeitbetrag: <strong>" . $this->buchung->minuten . " Minuten</strong></li>";

        if (!empty($this->buchung->kommentar_antrag)) {
            echo "<li>Antragskommentar:<br><strong>" . nl2br($this->buchung->kommentar_antrag) . "</strong></li>";
        }

        // Bei Stundengeschenken gibt es keinen Quittierungskommentar
        if (!$this->isGeschenk($this->buchung->arbeit_id) && !empty($this->buchung->kommentar_quittung)) {
            echo "<li>Quittierungskommentar:<br><strong>" . nl2br($this->buchung->kommentar_quittung) . "</strong></li>";
        }
        echo "</ul>";
    }
    ?>

</div>
