<?php

defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
//echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">

    <h1 class="zeitbank">
        Zeitbank: <?php echo ($this->isNew() ? 'Arbeit anlegen' : 'Arbeit bearbeiten'); ?>
    </h1>

    <form
        action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=arbeit.save&Itemid=" . $this->menuId); ?>"
        id="arbeitForm" name="arbeitForm" method="post">

        <table class="zb_form">
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('kurztext'); ?></td>
                <td class="value"><?php echo $this->form->getInput('kurztext'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('beschreibung'); ?></td>
                <td class="value"><?php echo $this->form->getInput('beschreibung'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('admin_id'); ?></td>
                <td class="value"><?php echo $this->form->getInput('admin_id'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('jahressoll'); ?></td>
                <td class="value"><?php echo $this->form->getInput('jahressoll'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('pauschale'); ?></td>
                <td class="value"><?php echo $this->form->getInput('pauschale'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('kommentar'); ?></td>
                <td class="value"><?php echo $this->form->getInput('kommentar'); ?></td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('aktiviert'); ?></td>
                <td class="value"><?php echo $this->form->getInput('aktiviert'); ?></td>
            </tr>
        </table>

        <fieldset>
            <input type="submit" value="Speichern"/>
            <input type="button" value="Abbrechen"
                   onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=arbeiten&Itemid=' . $this->menuId) ?>'"/>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]"/>
            <?php echo $this->form->getInput('ordering'); ?>
        </fieldset>
    </form>

    <div style="margin-top:15px">
        <strong>Hinweise:</strong>
        <ul>
            <li>Wenn du keinen Administrator auswählen kannst,
                <a href="<?php echo JRoute::_('index.php?option=com_zeitbank&task=arbeitadmin.edit&id=0&Itemid=' . $this->menuId) ?>">weise</a>
                deiner Kategorie zunächst mindestens einen Administrator zu.</li>
        </ul>
    </div>
</div>