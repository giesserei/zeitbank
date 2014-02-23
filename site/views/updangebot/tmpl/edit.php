<?php

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<h1 style="font-weight:bold;color: #7BA428; margin-bottom:10px;padding-bottom:0px;">
  Angebot bearbeiten
</h1>

<div class="component">
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=updangebot.save&Itemid=".$this->menuId); ?>" 
			  id="angebotForm" name="angebotForm" method="post" class="form-validate">
			
		<table class="market_form">
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('titel'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('titel'); ?></td>
			</tr>	
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('art'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('art'); ?></td>
			</tr>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('kategorie_id'); ?></td>
			  <td class="value">
          <?php
          $kategorien = array(-1 => '-');
          foreach($this->getKategorien() as $kat) {
            $kategorien[$kat->id] = $kat->bezeichnung;
          }
          
          $options = array();
          foreach($kategorien as $key=>$value) {
    	      $options[] = JHTML::_('select.option', $key, $value);
          }
          
          $dropdownKategorien = JHTML::_('select.genericlist', $options, 'kategorie_id', 
                  array('class'=>'inputbox'), 'value', 'text', $this->form->getValue('kategorie_id'));
    
          echo $dropdownKategorien;
          ?>			  
			  </td>
			</tr>		
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('richtung'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('richtung'); ?></td>
			</tr>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('status'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('status'); ?></td>
			</tr>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('ablauf'); ?></td>
			  <td class="value">
          <?php
          $ablauf = array(
              ZeitbankFrontendHelper::getMySqlDateInFuture(7) => '7 Tagen ('.ZeitbankFrontendHelper::getMySqlDateInFuture(7).')', 
              ZeitbankFrontendHelper::getMySqlDateInFuture(14) => '14 Tagen ('.ZeitbankFrontendHelper::getMySqlDateInFuture(14).')',
              ZeitbankFrontendHelper::getMySqlDateInFuture(21) => '21 Tagen ('.ZeitbankFrontendHelper::getMySqlDateInFuture(21).')',
              ZeitbankFrontendHelper::getMySqlDateInFuture(28) => '28 Tagen ('.ZeitbankFrontendHelper::getMySqlDateInFuture(28).')');
          $options = array();
    
          foreach($ablauf as $key=>$value) {
    	      $options[] = JHTML::_('select.option', $key, $value);
          }
          
          $dropdownAblauf = JHTML::_('select.genericlist', $options, 'filter_quality', 
                  array('class'=>'inputbox'), 'value', 'text', $this->form->getValue('ablauf'));
    
          echo $dropdownAblauf;
          ?>			  
			  </td>
			</tr>
    </table>	
    
    <?php echo $this->form->getInput('beschreibung'); ?>
			
		<fieldset>
			<input type="submit" value="Speichern"></button>
			<input type="button" value="Abbrechen" 
			       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_giesserei&view=profil&layout=view')?>'" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>	
  </form>
</div>