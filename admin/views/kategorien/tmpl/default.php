<?php
/*
 * Created on 28.12.2012
 *
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pagination');

$filter_order = JRequest::getVar('filter_order','reihenfolge');
$filter_order_Dir = JRequest::getVar('filter_order_Dir','asc');


$db = JFactory::getDBO();

$this->pagination = new JPagination(count( $this->items ), 0, $this->items );

?>	

<form action="index.php" method="POST" name="adminForm">
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th width="20">
				<input type="checkbox" name="toggle" value=""
				       onclick="checkAll(<?php echo count( $this->items ); ?>);" />


			</th>
			<th width="25">ID</th>
			<th>Bezeichnung</th>
			<th>Gesamtbudget [h/Jahr]</th>
			<th>Gegenkonto</th>
            <th width="70">
			  <?php echo JText::_( 'ORDERING' ); ?>
           	  <?php echo JHTML::_('grid.order',  $this->items ); ?>                   
        </tr>
    </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
        $row =& $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		$link = JRoute::_(
		    'index.php?option=com_zeitbank'
			.'&controller=kategorien'
			.'&task=edit&cid[]='. $row->id );			
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td><?php echo $checked; ?></td>
			<td align="right">
				<?php echo $row->id; ?></td>
            <td><a href="<?php echo $link; ?>"><?php echo $row->bezeichnung; ?></a></td>
            <td><?php echo $row->gesamtbudget; ?></td>		
            <td><?php echo $row->user_id; ?></td>	    
            <td class="order"><span><?php echo $this->pagination->orderUpIcon( $i, ($i > 0), 'orderup', 'Auf',$row->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($i < $n ), 'orderdown', 'Ab',$row->ordering); ?></span>
				<input type="text" name="ordering[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			    
			    <!--  echo $row->reihenfolge; --> 
			    </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>

	</div>

	<input type="hidden" name="option" value="com_zeitbank" />
	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	<input type="hidden" name="view" value="kategorien" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="kategorien" />
	<input type="hidden" name="task" value="" />
	</form>
