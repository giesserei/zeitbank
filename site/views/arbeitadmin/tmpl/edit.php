<?php

defined('_JEXEC') or die('Restricted access');

// Autocompletion: https://github.com/devbridge/jQuery-Autocomplete

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">

    <a href="<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId); ?>">Zurück zur Übersicht</a>

    <h1 class="zeitbank">
        Zeitbank: Ämtli-Administratoren verwalten
    </h1>

    <form
        action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=arbeitadmin.save&Itemid=" . $this->menuId); ?>"
        id="arbeitAdminForm" name="arbeitAdminForm" method="post">

        <table class="zb_form">
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('user_id'); ?></td>
                <td class="value">
                    <input type="text" name="jform[admin_name]" id="autocomplete" size="60"/>
                    <script type="text/javascript">
                        window.addEventListener("DOMContentLoaded", function () {
                            $('#autocomplete').autocomplete({
                                serviceUrl: '<?php echo JRoute::_("index.php?option=com_zeitbank&task=buchung.users&format=raw&includeCurrentUser=1"); ?>',
                                minChars: 3,
                                paramName: 'query',
                                onSelect: function (suggestion) {
                                    $('#user_id').val(suggestion.data);
                                }
                            });
                        }, false);
                    </script>
                </td>
            </tr>
        </table>

        <fieldset>
            <input type="submit" value="Hinzufügen"/>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]"/>
            <?php echo $this->form->getInput('kat_id', null, $this->kategorieId); ?>
            <input type="hidden" value="" name="jform[user_id]" id="user_id"/>
        </fieldset>
    </form>

    <ul>
    <?php
    $admins = $this->getAdministratoren();
    foreach ($admins as $admin):
        ?>
        <li>
            <?php echo $admin->name; ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" value="Löschen"
                   onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&task=arbeitadmin.delete&id=' . $admin->id . '&Itemid=' . $this->menuId) ?>'"/>
        </li>
        <?php
    endforeach;
    ?>
    </ul>

    <div style="margin-top:15px">
        <strong>Hinweise:</strong>
        <ul>
            <li>Auf dieser Seite kannst du Administratoren für die Ämtli deiner Kategorie hinzufügen und löschen.</li>
            <li>Nach dem Löschen eines Administrators bleiben bereits durchgeführte Zuweisungen zu einem Ämtli bestehen und müssen im Bearbeitungsdialog entfernt werden.</li>
        </ul>
    </div>
</div>