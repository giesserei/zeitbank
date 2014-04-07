<?php

defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">
 
  <h1 class="zeitbank">Zeitbank: Sicherheitsabfrage - Antrag löschen</h1>
  
  <ul>
    <li>Folgende Buchungsnummer würde gelöscht: <strong><?php echo $this->antrag->id; ?></strong></li>
    <li>Buchungsantrag an <strong><?php echo $this->antrag->konto_belastung; ?></strong> über <strong><?php echo $this->antrag->minuten; ?></strong> Minuten</li>
    <li>Buchungsdatum: <strong><?php echo JHTML::date($this->antrag->datum_antrag,"d.m.Y"); ?></strong></li> 
    <li>Arbeitsgattung: <strong><?php echo $this->antrag->kurztext ?></strong></li>
    <?php if (empty($this->antrag->text)) { ?>
    <li>Antragskommentar:<br/><?php echo $this->antrag->text ?></li>
    <?php } ?>
  </ul>
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=antragloeschen.delete&Itemid=".$this->menuId); ?>" 
			  id="deleteForm" name="deleteForm" method="post">
		<fieldset>
			<input type="submit" value="Wirklich löschen" />
			<input type="button" value="Abbrechen" 
			       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid='.$this->menuId)?>'" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" value="<?php echo $this->antrag->id; ?>" name="id" />
		</fieldset>	
  </form>
  
</div>