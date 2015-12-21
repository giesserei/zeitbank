<?php

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$app = JFactory::getApplication();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$ordering = ($listOrder == 'a.ordering');
$canOrder = $user->authorise('core.edit.state', 'com_zeitbank');
$saveOrder = ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_zeitbank&task=kategorien.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_zeitbank&view=kategorien'); ?>" method="post" name="adminForm"
      id="adminForm">
    <?php
    if (!empty($this->sidebar)) {
        echo '<div id="j-sidebar-container" class="span2">';
        echo $this->sidebar;
        echo '</div>';
        echo '<div id="j-main-container" class="span10">';
    } else {
        echo '<div id="j-main-container">';
    }
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this), null, array('debug' => false));
    ?>
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
        <table class="table table-striped" id="kategorienList">
            <thead>
            <tr>
                <th width="1%">
                    <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                </th>
                <th width="1%" class="center">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width="1%" class="center">
                    <?php echo JHtml::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <th width="20%" class="title">
                    <?php echo JHtml::_('searchtools.sort', 'Bezeichnung', 'a.bezeichnung', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap">
                    Gesamtbudget [h/Jahr]
                </th>
                <th width="10%">
                    Zeitbankkonto
                </th>
                <th width="10%">
                    Administrator
                </th>
                <th width="1%">
                    Reihenfolge
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="7">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>

            <tbody>
            <?php foreach ($this->items as $i => $item) {
                $canEdit = $user->authorise('core.edit', 'com_zeitbank');
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="order nowrap center">
                        <?php
                        $iconClass = '';

                        if (!$saveOrder) {
                            $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                        }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
                        <?php if ($saveOrder) : ?>
                            <input type="text" style="display:none" name="order[]" size="5"
                                   value="<?php echo $orderkey + 1; ?>"/>
                        <?php endif; ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="center">
                        <?php echo $item->id; ?>
                    </td>
                    <td class="nowrap">
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_zeitbank&task=kategorie.edit&id=' . (int)$item->id); ?>">
                                <?php echo $this->escape($item->bezeichnung); ?></a>
                        <?php else : ?>
                            <?php echo $this->escape($item->bezeichnung); ?>
                        <?php endif; ?>
                    </td>
                    <td class="nowrap">
                        <?php echo $item->gesamtbudget; ?>
                    </td>
                    <td>
                        <?php echo $item->user_id; ?>
                    </td>
                    <td>
                        <?php echo $item->admin_name; ?>
                    </td>
                    <td>
                        <?php echo $item->ordering; ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo JHtml::_('form.token'); ?>

</form>


