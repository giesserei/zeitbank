<?php

defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">

    <h1 class="zeitbank">
        Zeitbank: Kategorie bearbeiten
    </h1>

    <form
        action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=kategorie.save&Itemid=" . $this->menuId); ?>"
        id="kategorieForm" name="kategorieForm" method="post">

        <table class="zb_form">
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('gesamtbudget'); ?></td>
                <td class="value"><?php echo $this->form->getInput('gesamtbudget'); ?></td>
            </tr>
        </table>

        <fieldset>
            <input type="submit" value="Speichern"/>
            <input type="button" value="Abbrechen"
                   onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId) ?>'"/>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]"/>
        </fieldset>
    </form>

    <div style="margin-top:15px">
        <strong>Hinweise:</strong>
        <ul>
            <li>Auf dieser Seite kannst du das Gesamtbudget für deine Kategorie ändern.</li>
        </ul>
    </div>
</div>