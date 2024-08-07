<?php

defined('_JEXEC') or die('Restricted access');

// Autocompletion: https://github.com/devbridge/jQuery-Autocomplete

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
//echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">

    <h1 class="zeitbank">
        <?php echo($this->isNew() ? "Zeitbank: Neuen Antrag für Giessereistunden erstellen" : "Zeitbank: Antrag für Giessereistunden bearbeiten"); ?>
    </h1>

    <form
        action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=eigenleistungen.save&Itemid=" . $this->menuId); ?>"
        id="eigenleistungenForm" name="eigenleistungenForm" method="post" style="margin-top: 20px">

        <table class="zb_form">
            <tr>
                <td class="lb"><label for="filter_arbeit_gattung">Suche:</label></td>
                <td class="value">
                    <input id="filter_arbeit_gattung" type="text" oninput="filterArbeitGattungen()" placeholder="Arbeitsgattung suchen"/>
                    &nbsp; <span style="vertical-align: top; position: relative; top: 3px">(filtert die Auswahl im Arbeitsgattungs-Feld)</span>
                </td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('arbeit_id'); ?></td>
                <td class="value">
                    <?php
                    $arbeiten = $this->getArbeitsgattungen();

                    $dropdownArbeiten = JHTML::_('select.groupedlist', $arbeiten, 'jform[arbeit_id]',
                        array('class' => 'inputbox', 'id' => 'jform_arbeit_id', 'group.items' => 'items',
                            'list.select' => $this->form->getValue('arbeit_id'))
                    );

                    echo $dropdownArbeiten;
                    ?>
                    &nbsp; <span style="vertical-align: top; position: relative; top: 3px">*</span>
                </td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('minuten'); ?></td>
                <td class="value"><?php echo $this->form->getInput('minuten'); ?> &nbsp; *</td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('kommentar_antrag'); ?></td>
                <td class="value">
                    <?php echo $this->form->getInput('kommentar_antrag'); ?>
                    &nbsp; <span style="vertical-align: top">(max. 1000 Zeichen)</span>
                    </td>
            </tr>
            <tr>
                <td class="lb"><?php echo $this->form->getLabel('datum_antrag'); ?></td>
                <td class="value">
                    <?php

                    $datumAntrag = array();

                    if (ZeitbankCalc::isCurrentYearAllowed()) {
                        $datumAntrag[date('Y-m-d')] = date('Y');
                    }
                    if (ZeitbankCalc::isLastYearAllowed()) {
                        $lastYear = intval(date('Y')) - 1;
                        $datumAntrag[$lastYear . '-12-31'] = (string)$lastYear;
                    }

                    $options = array();

                    foreach ($datumAntrag as $key => $value) {
                        $options[] = JHTML::_('select.option', $key, $value);
                    }

                    $dropdownDatumAntrag = JHTML::_('select.genericlist', $options, 'jform[datum_antrag]',
                        array('class' => 'inputbox'), 'value', 'text', $this->form->getValue('datum_antrag'), 'jform_datum_antrag');

                    echo $dropdownDatumAntrag;
                    ?>
                    &nbsp; *
                </td>
            </tr>
            <tr>
                <td class="lb" colspan="2" style="font-weight:normal">* Eingabe ist obligatorisch
                </td>
            </tr>
        </table>

        <fieldset style="margin-top: 10px">
            <input type="submit" value="Speichern"/>
            <input type="button" value="Abbrechen"
                   onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId) ?>'"/>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]"/>
            <?php echo $this->form->getInput('cf_uid'); ?>
        </fieldset>
    </form>

    <script>
        "use strict";
        function groupHasVisibleOptions(parentNode) {
            let nodes = parentNode.childNodes;
            for (let i = 0; i < nodes.length; i++) {
                if (nodes[i].nodeName.toLowerCase() === 'option' &&
                    nodes[i].style.display === '') {
                    // mindestens eine sichtbare option
                    return true;
                }
            }
            return false;
        }

        function filterArbeitGattungen() {

            let filterElement = document.getElementById('filter_arbeit_gattung');
            let arbeitElement = document.getElementById('jform_arbeit_id');

            // Verstecke options welche filter kriterien nicht ensprechen
            let filter = filterElement.value.toUpperCase();
            let arbeitOptions = arbeitElement.options;
            for (let i = 0; i < arbeitOptions.length; i++) {
                let node = arbeitOptions[i];
                let optionText = node.textContent.toUpperCase();
                // finde Eintrag: ---- Arbeitsgattung auswählen ----
                let isSpecial = node.value < 0;

                let filterMatch = optionText.indexOf(filter) > -1;
                node.style.display = (filterMatch || isSpecial) ? '' : 'none';
            }

            // Verstecke leer optionGroups
            let childNodes = arbeitElement.childNodes;
            for (let i = 0; i < childNodes.length; i++) {
                let node = childNodes[i];
                if (node instanceof HTMLOptGroupElement) {
                    let shouldShow = groupHasVisibleOptions(node)
                    node.style.display = shouldShow ? '' : 'none';
                }
            }

            // Auswahlbox öffnen wenn Filter aktiv
            if (filter) {
                arbeitElement.size = 10;
                arbeitElement.style.width = "320px";
            } else {
                arbeitElement.size = 0;
                arbeitElement.style.width = "";
            }
        }
    </script>

    <div style="margin-top:15px">
        <strong>Hinweise:</strong>
        <ul>
            <li>Auf dieser Seite kannst du einen Antrag auf Gutschrift von Giessereistunden stellen.</li>
            <li>Wenn du die Arbeitsgattung nicht kennst, kontaktiere bitte den Ressortverantwortlichen, welcher dir den
                Arbeitsauftrag gegeben hat.
            </li>
            <li>Die beantragten Minuten werden deinem Konto gutgeschrieben, sobald der Ämtli-Verantwortliche deinen
                Antrag quittiert hat.
            </li>
            <li>Für die Arbeitskategorien mit einer Pauschale kannst du das Feld Minuten frei lassen. Es wird
                automatisch die Pauschle gutgeschrieben.
            </li>
            <li>Nach dem Jahreswechsel kannst du noch bis zum <strong>05.01.</strong> einen Antrag für das vergangene
                Jahr stellen.
            </li>
        </ul>
    </div>
</div>
