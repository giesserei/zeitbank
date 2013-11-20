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
		<legend><?php echo JText::_( 'Ämtli-Daten' ); ?></legend>
		<table class="admintable">
		
			<tr><td width="100" align="right" class="key">
			<label for="nummer">Kurztext</label></td>
			<td><input class="text_area" type="text" name="kurztext" id="kurztext" size="25" maxlength="25" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->kurztext; ?>" />
			</td></tr>

			
			<tr><td width="100" align="right" class="key">
			<label for="nummer">Kategorie</label></td>
			<td>
<?php 
			$kategorien = array();
			$kategorien[] = JHTML::_('select.option',0, '--- Kategorie auswählen ---');
			
			foreach($this->kategorien as $kategorie):
				if($this->arbeit->kategorie_id == $kategorie->id) $sel = $kategorie->id;
				$kategorien[] = JHTML::_('select.option',$kategorie->id, $kategorie->bezeichnung);
			endforeach;

			echo JHTML::_( 'select.genericlist', $kategorien, 'kategorie_id', '','value','text',$sel ).'<br />';			
?>
			</td></tr>
			
			<tr><td width="100" align="right" class="key">
			<label for="nummer">Beschreibung</label></td>
			<td><input class="text_area" type="text" name="beschreibung" id="beschreibung" size="40" maxlength="200" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->beschreibung; ?>" />
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Kadenz</label></td>
			<td><input class="text_area" type="text" name="kadenz" id="kadenz" size="5" maxlength="5" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->kadenz; ?>" /> (Anzahl Einsätze <strong>pro Jahr</strong>; 1 Jahr = 52 Wochen = 12 Monate)
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Pauschale</label></td>
			<td><input class="text_area" type="text" name="pauschale" id="pauschale" size="5" maxlength="5" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->pauschale; ?>" /> Minuten pro Einsatz; keine Pauschalierung = 0
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Jahressoll</label></td>
			<td><input class="text_area" type="text" name="jahressoll" id="jahressoll" size="5" maxlength="5" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->jahressoll; ?>" /> h/Jahr (Prognose für <em>nicht</em> pauschalierte Arbeiten, Pauschale hat Priorität!)
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Administrator</label></td>
			<td><input class="text_area" type="text" name="admin_id" id="admin_id" size="6" maxlength="6" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->admin_id; ?>" /> (User-Id des Joomla-Systems, Benutzer-Nummer zur Administration dieses Ämtlis)
			</td></tr>

			<tr><td width="100" align="right" class="key">
			<label for="nummer">Kommentar</label></td>
			<td><input class="text_area" type="text" name="kommentar" id="kommentar" size="40" maxlength="250" value="<?php
				if(is_object($this->arbeit)) echo $this->arbeit->kommentar; ?>" /> (freies Feld)
			</td></tr>
			
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_zeitbank" />
<?php if($this->arbeit->id > 0): ?>
<input type="hidden" name="id" value="<?php echo $this->arbeit->id ?>" />
<input type="hidden" name="ordering" value="<?php echo $this->arbeit->ordering ?>" />
<?php endif; ?>
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="arbeiten" />

</form>