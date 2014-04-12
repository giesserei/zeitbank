<?php

defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">
 
  <h1 class="zeitbank">Zeitbank: Antrag quittieren</h1>
  
  <div>Bitte quittiere folgenden Antrag:</div>
  <ul>
    <?php if ($this->antrag->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH) { ?>
    <li>Deinem persönlichen Zeitkonto werden <strong><?php echo $this->antrag->minuten; ?></strong> Minuten abgebucht.</li>
    <?php } else { ?>
    <li>Dem Konto <strong><?php echo $this->antrag->konto_belastung; ?></strong> werden <strong><?php echo $this->antrag->minuten; ?></strong> Minuten abgebucht.</li>
    <?php } ?>
    <li>AntragstellerIn: <strong><?php echo $this->antrag->konto_gutschrift; ?></strong></li> 
    <li>Arbeitsgattung: <strong><?php echo $this->antrag->kurztext ?></strong></li>
    <?php if (!empty($this->antrag->text)) { ?>
    <li>Antragskommentar:<br/><?php echo nl2br($this->antrag->text) ?></li>
    <?php } ?>
  </ul>
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=quittung.save&Itemid=".$this->menuId); ?>" 
			  id="quittungForm" name="quittungForm" method="post">
			  
	  <table class="zb_form">
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('kommentar_quittung'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('kommentar_quittung'); ?><br/>(max. 1000 Zeichen)</td>
			</tr>
    </table>			  
			  
		<fieldset>
			<input type="submit" value="Quittieren" />
			
			<?php if ($this->isJournalAemtli()) { // wieder zurück zur Liste, da kein Stundentausch ?>
			  <input type="button" value="Abbrechen" 
			         onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=quittung_amt&Itemid='.$this->menuId)?>'" />
			<?php } else { // Stundentausch -> zurück zur Übersicht ?>
			  <input type="button" value="Abbrechen" 
			         onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid='.$this->menuId)?>'" />
			<?php } ?>
			
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" value="<?php echo $this->antrag->id; ?>" name="jform[id]" />
		</fieldset>	
  </form>
  
</div>