<?php
/*
 * Created on 28.12.2012
 *
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pagination');

$filter_order = JRequest::getVar('filter_order','reihenfolge');
$filter_order_Dir = JRequest::getVar('filter_order_Dir','asc');

$model = & $this->getModel();
$db =& JFactory::getDBO();

$this->pagination = new JPagination(count( $this->items ), 0, $this->items );

function printTableHead($th) {
?>
<table class="adminlist">
    <thead>
        <tr>
            <th width="20">
				<input type="checkbox" name="toggle" value=""
				       onclick="checkAll(<?php echo count( $th->items ); ?>);" />
			</th>
			<th width="25">ID</th>
			<th width="30">Aktiv</th>
			<th>Kurztext</th>
			<th>Administration</th>
			<th width="30">Kadenz /Jahr</th>
			<th width="30">Kadenz /Monat</th>
			<th width="30">Kadenz /Woche</th>
			<th width="30">Pauschale (min.)</th>
			<th width="30">Abweichung</th>
			<th width="40">Jahressoll</th>
			<th width="40">Verbucht</th>
            <th width="80">
			  <?php echo JText::_( 'Reihenfolge' ); ?>
           	  <?php echo JHTML::_('grid.order',  $th->items ); ?>                   
        </tr>
    </thead>

<?php 
} // printTableHead

?>	

<form action="index.php" method="POST" name="adminForm">
<div id="editcell">
<?php 
	$k = 0;
	$kategorie = 0;
	$prev_entry = & $this->items[0];;
	
    for ($i=0, $n=count( $this->items ); $i < $n; $i++):
    $row =& $this->items[$i];
    
    // Neue Kategorie?
	if($row->kategorie_id != $kategorie):
		if($kategorie > 0) echo "</table><br />";
		$kategorie = $row->kategorie_id;
		echo "<h2>".$row->kategorie_bez."</h2>";
		printTableHead($this);
		$k = 0;   // Zebra reset
	endif;
	
	if($i < count($this->items)-1): 
		$next_entry = $this->items[$i + 1]->kategorie_id;
	endif;
	
   // Rest der Einträge ausgeben	
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		$link = JRoute::_(
		    'index.php?option=com_zeitbank'
			.'&controller=arbeiten'
			.'&task=edit&cid[]='. $row->id );		
				
		// Vergleich gebuchte Stunden und Sollzeit
		$saldo = $model->getArbeitSaldo($row->id,$row->pauschale);
		$soll = date('z') * ($row->pauschale > 0?$row->pauschale*$row->kadenz:$row->jahressoll*60) / 365;	

		// Alarm gelb, wenn noch zu wenig getan wurde
		$alarm="";
		if($saldo * 1.2 < $soll):
			$alarm = "background-color: yellow";
		endif;
		
		// Alarm rot, wenn zu viel getan wurde
		if($saldo * 0.8 > $soll):
			$alarm = "background-color: red; color: white;";
		endif;

		$abweichung = $model->getArbeitAbweichung($row->id,$row->pauschale);

		// Aktiviert oder nicht?
		if($row->aktiviert):
			$aktiv = "<img src=\"images/tick.png\" />";
		else:
			$aktiv = "<img src=\"images/publish_x.png\" />";
		endif;
		
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td><?php echo $checked; ?></td>
			<td align="right">
				<?php echo $row->id; ?></td>
			<td><?php echo $aktiv; ?> </td>
            <td><a href="<?php echo $link; ?>"><?php echo $row->kurztext; ?></a></td>
            <td><?php echo JFactory::getUser($row->admin_id)->name; ?></td>			    		    
			<td align="center"><?php if($row->kadenz < 12) echo $row->kadenz; ?></td>			    
			<td align="center"><?php if($row->kadenz >= 12 AND $row->kadenz < 52) echo round($row->kadenz/12,1); ?></td>			    
			<td align="center"><?php if($row->kadenz >= 52) echo round($row->kadenz/52,1); ?></td>			    
            <td align="right"><?php echo $row->pauschale; ?>'</td>			    
            <td align="right"><?php echo $abweichung>0?$abweichung."'":"-"; ?></td>		
            <td align="right"><?php echo $row->pauschale > 0?round($row->pauschale*$row->kadenz/60,1):$row->jahressoll; ?> h</td>		
            <td align="right" style="<?php echo $alarm?>"><?php echo round($saldo/60,1); ?>h</td>	    
            <td class="order"><span><?php echo $this->pagination->orderUpIcon( $i, ($prev_entry->kategorie_id == $row->kategorie_id), 'orderup', 'Auf',$row->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($next_entry == $row->kategorie_id ), 'orderdown', 'Ab',$row->ordering); ?></span>
				<input type="text" name="ordering[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			    
			    <!--  echo $row->reihenfolge; --> 
			    </td>
        </tr>
        <?php
        $prev_entry = $row;
        $k = 1 - $k;
    endfor;
    ?>
    </table>

	</div>

	<input type="hidden" name="option" value="com_zeitbank" />
	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	<input type="hidden" name="view" value="arbeiten" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="arbeiten" />
	<input type="hidden" name="task" value="" />
	</form>
