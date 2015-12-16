<?php

defined('_JEXEC') or die('Restricted access');

$listDirn = JRequest::getVar('filter_order', 'reihenfolge');
$listOrder = JRequest::getVar('filter_order_Dir', 'asc');

?>

<div id="kategorien" class="clearfix">
  <form action="index.php" method="POST" name="adminForm" class="form-inline">
    <table class="table table-striped" id="kategorienList">
      <thead>
      <tr>
        <th width="20" class="center">
          <?php echo JHtml::_('grid.checkall'); ?>
        </th>
        <th width="25" class="center">
          <?php echo JHtml::_('grid.sort', 'ID', 'id', $listDirn, $listOrder); ?>
        </th>
        <th class="nowrap">
          <?php echo JHtml::_('grid.sort', 'Bezeichnung', 'bezeichnung', $listDirn, $listOrder); ?>
        </th>
        <th class="nowrap">
          Gesamtbudget [h/Jahr]
        </th>
        <th class="nowrap">
          Gegenkonto
        </th>
      </tr>
      </thead>
      <?php
      for ($i = 0, $n = count($this->items); $i < $n; $i++) {
        $item = $this->items[$i];
        $checked = JHTML::_('grid.id', $i, $item->id);
        $link = JRoute::_('index.php?option=com_zeitbank' . '&controller=kategorien' . '&task=edit&cid[]=' . $item->id);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
          <td class="center">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
          </td>
          <td class="center">
            <?php echo $item->id; ?>
          </td>
          <td class="nowrap">
            <a href="<?php echo $link; ?>"><?php echo $item->bezeichnung; ?></a>
          </td>
          <td class="nowrap">
            <?php echo $item->gesamtbudget; ?>
          </td>
          <td>
            <?php echo $item->user_id; ?>
          </td>
        </tr>
        <?php
      }
      ?>
    </table>

    <input type="hidden" name="option" value="com_zeitbank"/>
    <input type="hidden" name="filter_order" value="<?php echo $listDirn; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="view" value="kategorien"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="controller" value="kategorien"/>
    <input type="hidden" name="task" value=""/>
  </form>

</div>
