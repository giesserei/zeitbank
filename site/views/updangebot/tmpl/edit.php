<?php

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

$isNew = $this->isNew();
$titelArbeit = $isNew ? "Arbeitsangebot erstellen" : "Arbeitsangebot bearbeiten";
$titelTausch = $isNew ? "Eintrag erstellen" : "Eintrag bearbeiten";

?>

<h1 style="font-weight:bold;color: #7BA428; margin-bottom:10px;padding-bottom:0px;">
  <?php echo ($this->isArbeitView() ? $titelArbeit : $titelTausch); ?>
</h1>

<div class="component">
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=updangebot.save&Itemid=".$this->menuId); ?>" 
			  id="angebotForm" name="angebotForm" method="post" class="form-validate">
	  
	  <!-- Art des Eintrags ist fixiert -->		
		<input type="hidden" value="<?php echo $this->getArt(); ?>" name="jform[art]" />	
			
		<table class="market_form">
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('titel'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('titel'); ?></td>
			</tr>	
			<?php if($this->isArbeitView()) { ?>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('kategorie_id'); ?></td>
			  <td class="value">
          <?php
          $kategorien = array();
          foreach($this->getKategorien() as $kat) {
            $kategorien[$kat->id] = $kat->bezeichnung;
          }
          
          $options = array();
          foreach($kategorien as $key=>$value) {
    	      $options[] = JHTML::_('select.option', $key, $value);
          }
          
          $dropdownKategorien = JHTML::_('select.genericlist', $options, 'jform[kategorie_id]', 
                  array('class'=>'inputbox', 'id'=>'jform_kategorie_id]'), 'value', 'text', $this->form->getValue('kategorie_id'));
    
          echo $dropdownKategorien;
          ?>			  
			  </td>
			</tr>		
			<?php } ?>
			<?php if($this->isTauschView()) { ?>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('richtung'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('richtung'); ?></td>
			</tr>
			<?php } ?>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('status'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('status'); ?></td>
			</tr>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('ablauf'); ?></td>
			  <td class="value">
          <?php
          $ablauf = array(
              ZeitbankFrontendHelper::getMySqlDateInFuture(7) => '7 Tagen ('.ZeitbankFrontendHelper::getViewDateInFuture(7).')', 
              ZeitbankFrontendHelper::getMySqlDateInFuture(14) => '14 Tagen ('.ZeitbankFrontendHelper::getViewDateInFuture(14).')',
              ZeitbankFrontendHelper::getMySqlDateInFuture(21) => '21 Tagen ('.ZeitbankFrontendHelper::getViewDateInFuture(21).')',
              ZeitbankFrontendHelper::getMySqlDateInFuture(28) => '28 Tagen ('.ZeitbankFrontendHelper::getViewDateInFuture(28).')');
          $options = array();
    
          foreach($ablauf as $key=>$value) {
    	      $options[] = JHTML::_('select.option', $key, $value);
          }
          
          $dropdownAblauf = JHTML::_('select.genericlist', $options, 'jform[ablauf]', 
                  array('class'=>'inputbox', 'id'=>'jform_ablauf'), 'value', 'text', $this->form->getValue('ablauf'));
    
          echo $dropdownAblauf;
          ?>			  
			  </td>
			</tr>
			<tr>
        <td class="lb" colspan="2"><span class="star">* </span> Eingabe ist obligatorisch</td>
      </tr>
    </table>	
    
    <?php echo $this->form->getInput('beschreibung'); ?>
			
		<fieldset>
			<input type="submit" value="Speichern" />
			<input type="button" value="Abbrechen" 
			       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=marketplace&layout=meine')?>'" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]" />
		</fieldset>	
  </form>
</div>