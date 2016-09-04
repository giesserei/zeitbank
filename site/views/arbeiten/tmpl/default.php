<?php
defined('_JEXEC') or die;

// AJAX-Tools laden
JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

$currentYear = intval(date('Y'));
$lastYear = $currentYear - 1;
?>

<div class="component">

    <a href="<?php echo JRoute::_("index.php?option=com_zeitbank&view=zeitbank&Itemid=" . $this->menuId); ?>">
        Zurück zur Übersicht
    </a>

    <?php if (!empty($this->arbeiten)): ?>
    <h1 class="zeitbank">
        Zeitbank: Arbeiten verwalten
    </h1>

    <table class="zeitbank" style="width: 1000px;">
    <tr class="head">
		<th>Kurztext</th>
        <th align="right">Soll<br/>[h]</th>
        <th align="right">Ist <?php echo $currentYear; ?><br/>[hh:mm]</th>
        <th align="right">Ist <?php echo $lastYear; ?><br/>[hh:mm]</th>
        <th align="right">Pauschale<br/>[min]</th>
        <th>Administrator</th>
        <th align="center">Aktiv</th>
        <th align="center">Reihenfolge</th>
        <th>&nbsp;</th>
    </tr>

    <?php foreach ($this->arbeiten as $i => $arbeit):?>
        <tr class="<?php echo $i % 2 == 0 ? "zb_even" : "zb_odd"; ?>">
            <td>
                <?php echo ZeitbankFrontendHelper::cropText($arbeit->kurztext, 30); ?>
            </td>
            <td align="right">
                <?php echo $arbeit->jahressoll ?>
            </td>
            <td align="right">
                <?php echo ZeitbankFrontendHelper::formatTime($arbeit->ist_laufend); ?>
            </td>
            <td align="right">
                <?php echo ZeitbankFrontendHelper::formatTime($arbeit->ist_vorjahr); ?>
            </td>
            <td align="right">
                <?php echo $arbeit->pauschale ?>
            </td>
            <td>
                <?php echo ZeitbankFrontendHelper::cropText($arbeit->name, 25); ?>
            </td>
            <td align="center">
                <?php
                $image = $arbeit->aktiviert == 1 ? JRoute::_("/images/on.png") : JRoute::_("/images/off.png");
                echo "<img src='" . $image . "'/>";
                ?>
            </td>
            <td align="center">
                <?php if($arbeit->orderFlag != 'min'):
                   $link = JRoute::_("index.php?option=com_zeitbank&task=arbeit.orderUp&id=" . $arbeit->id . "&Itemid=" . $this->menuId);
                ?>
                <a href="<?php echo $link?>">Auf</a>&nbsp;
                <?php endif; ?>
                <?php if($arbeit->orderFlag != 'max'):
                   $link = JRoute::_("index.php?option=com_zeitbank&task=arbeit.orderDown&id=" . $arbeit->id . "&Itemid=" . $this->menuId);
                ?>
                <a href="<?php echo $link?>">Ab</a>
                <?php endif; ?>
            </td>
            <td>
                <?php $link = JRoute::_("index.php?option=com_zeitbank&task=arbeit.edit&id=" . $arbeit->id . "&Itemid=" . $this->menuId); ?>
                <input type="button" value="Bearbeiten"
                       onclick="window.location.href='<?php echo $link ?>'" />

            </td>
        </tr>
    <?php endforeach; ?>

    </table>

    <?php else: ?>
    Du hast keine Arbeiten erfasst.
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <a href="<?php echo JRoute::_('index.php?option=com_zeitbank&task=arbeit.edit&id=0' . '&Itemid=' . $this->menuId) ?>">
            Hinzufügen
        </a>
    </div>
</div>
