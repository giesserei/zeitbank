<?php
/*
 * Created on 29.12.2012
 *
 */
defined('_JEXEC') or die('Restricted access');

$filter_order = JRequest::getVar('filter_order','nummer');
$filter_order_Dir = JRequest::getVar('filter_order_Dir','asc');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Kategorie-Daten' ); ?></legend>
		<table class="admintable">
		
			<tr><td width="100" align="right" class="key">
			<label for="nummer">Bezeichnung</label></td>
			<td><input class="text_area" type="text" name="bezeichnung" id="bezeichnung" size="20" maxlength="50" value="<?php
				if(is_object($this->kategorie)) echo $this->kategorie->bezeichnung; ?>" />
			</td>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Jahresbudget</label></td>
			<td><input class="text_area" type="text" name="gesamtbudget" id="gesamtbudget" size="5" maxlength="5" value="<?php
				if(is_object($this->kategorie)) echo $this->kategorie->gesamtbudget; ?>" /> h/Jahr (Prognose für gesamte Kategorie)
			</td></tr>


			<tr><td width="100" align="right" class="key">
			<label for="nummer">Gegenkonto</label></td>
			<td><input class="text_area" type="text" name="user_id" id="user_id" size="6" maxlength="6" value="<?php
				if(is_object($this->kategorie)) echo $this->kategorie->user_id; ?>" /> (User-Id des Joomla-Systems, Benutzer-Nummer für Bestätigung)
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Administrator</label></td>
			<td><input class="text_area" type="text" name="admin_id" id="admin_id" size="6" maxlength="6" value="<?php
				if(is_object($this->kategorie)) echo $this->kategorie->admin_id; ?>" /> (User-Id des Joomla-Systems, Benutzer-Nummer zur Administration dieser Kategorie)
			</td></tr>

			
					
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_zeitbank" />
<?php if($this->kategorie->id > 0): ?>
<input type="hidden" name="id" value="<?php echo $this->kategorie->id ?>" />
<input type="hidden" name="ordering" value="<?php echo $this->kategorie->ordering ?>" />
<?php endif; ?>
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="kategorien" />

</form>