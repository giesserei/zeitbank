<?php

defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">

    <h1 class="zeitbank">Zeitbank: Antrag ablehnen</h1>

    <div>Du möchtest folgenden Antrag <span style="color:red">ablehnen</span>:</div>
    <ul>
        <?php if ($this->antrag->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH) { ?>
            <li>Deinem persönlichen Zeitkonto werden <strong><?php echo $this->antrag->minuten; ?></strong> Minuten
                abgebucht.
            </li>
        <?php } else { ?>
            <li>Dem Konto <strong><?php echo $this->antrag->konto_belastung; ?></strong> werden
                <strong><?php echo $this->antrag->minuten; ?></strong> Minuten abgebucht.
            </li>
        <?php } ?>
        <li>AntragstellerIn: <strong><?php echo $this->antrag->konto_gutschrift; ?></strong></li>
        <li>Arbeitsgattung: <strong><?php echo $this->antrag->kurztext ?></strong></li>
        <?php if (empty($this->antrag->text)) { ?>
            <li>Antragskommentar:<br/><?php echo $this->antrag->text ?></li>
        <?php } ?>
    </ul>

    <form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=ablehnung.save&Itemid=" . $this->menuId); ?>"
          id="ablehnungForm" name="ablehnungForm" method="post">

        <table class="zb_form">
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('kommentar_ablehnung'); ?></td>
                <td class="value"><?php echo $this->form->getInput('kommentar_ablehnung'); ?><br/>(max. 1000 Zeichen)
                </td>
            </tr>
        </table>

        <fieldset>
            <input type="submit" value="Antrag ablehnen"/>

            <?php if ($this->isJournalAemtli()) { // wieder zurück zur Liste, da kein Stundentausch ?>
                <input type="button" value="Abbrechen"
                       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=quittung_amt&Itemid=' . $this->menuId) ?>'"/>
            <?php } else { // Stundentausch -> zurück zur Übersicht ?>
                <input type="button" value="Abbrechen"
                       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId) ?>'"/>
            <?php } ?>

            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" value="<?php echo $this->antrag->id; ?>" name="jform[id]"/>
        </fieldset>
    </form>

    <div style="margin-top:15px">
        <strong>Hinweise:</strong>
        <ul>
            <li>Gebe bitte eine aussagekräftige Begründung für die Ablehnung des Antrags ein.</li>
            <li>Nach der Ablehnung sieht der/die AntragstellerIn deine Begründung in der Zeitbankübersicht.</li>
            <li>Der Antrag bleibt auch nach der Ablehnung bestehen, bis der/die AntragstellerIn den Antrag gelöscht oder
                bearbeitet hat.
            </li>
            <li>Du kannst bei fehlerhaften Anträgen auch weiterhin direkt Kontakt zum/zur AntragstellerIn aufnehmen,
                ohne den Antrag vorgängig ablehnen zu müssen.
            </li>
        </ul>
    </div>

</div>