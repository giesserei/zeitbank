<?php
/*
 * Created on 27.12.2010
 *
 */
defined('_JEXEC') or die('Restricted access');

/*
if(JRequest::getVar('act',NULL) == 'blibla'):
	jimport('joomla.user.helper');
	$db = JFactory::getDBO();
	echo "Blibla";	
else:
*/
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
			<th width="5">ID</th>
            <th>Minuten</th>
			<th>Sender</th>
			<th>Empfänger</th>
			<th>Antragsdatum</th>
			<th>Quittungsdatum</th>
			<th>Arbeitsgattung</th>
        </tr>
    </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
        $row =& $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		if($row->admin_del >=1) $durchgestr = "text-decoration: line-through; color: red;"; else $durchgestr="";
//		$link = JRoute::_(
//		    'index.php?option=com_zeitbank'
//			.'&controller=zeitbank'
//			.'&task=edit&cid[]='. $row->id );
?>
			<tr>
			<td><?php echo $checked; ?></td>
			<td style="<?php echo $durchgestr ?>"><?php echo $row->id; ?></td>
            <td style="<?php echo $durchgestr ?>"><a href="<?php echo $link; ?>">
			    <?php echo $row->minuten; ?></a></td>
            <td style="<?php echo $durchgestr ?>"><a href="<?php echo $link; ?>"><?php echo JFactory::getUser($row->belastung_userid)->name; ?></a></td>
            <td style="<?php echo $durchgestr ?>"><?php echo JFactory::getUser($row->gutschrift_userid)->name; ?></td>
            <td style="<?php echo $durchgestr ?>"><?php echo $row->datum_antrag; ?></td>
            <td style="<?php echo $durchgestr ?>"><?php echo $row->datum_quittung; ?></td>
            <td style="<?php echo $durchgestr ?>"><?php echo $row->arbeit_id; ?></td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>
<input type="hidden" name="option" value="com_zeitbank" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="zeitbank" />
</form>
<?php 
// endif;
?>