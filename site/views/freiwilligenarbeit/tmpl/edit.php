<?php

defined('_JEXEC') or die('Restricted access');

// Autocompletion: https://github.com/devbridge/jQuery-Autocomplete

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">
 
  <h1 class="zeitbank">
    <?php echo ($this->isNew() ? "Zeitbank: Neuen Antrag für Freiwilligenarbeit erstellen" : "Zeitbank: Antrag für Freiwilligenarbeit bearbeiten"); ?>
  </h1>
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=freiwilligenarbeit.save&Itemid=".$this->menuId); ?>" 
			  id="freiwilligenarbeitForm" name="freiwilligenarbeitForm" method="post">
	  
		<table class="zb_form">
		  <tr>
			  <td class="lb"><?php echo $this->form->getLabel('arbeit_id'); ?><span class="star">* </span> </td>
			  <td class="value">
          <?php
          $arbeiten = $this->getArbeitsgattungen();

          $dropdownArbeiten = JHTML::_('select.groupedlist', $arbeiten, 'jform[arbeit_id]', 
                  array('class'=>'inputbox', 'id'=>'jform_arbeit_id]', 'group.items'=>'items', 
                        'list.select'=>$this->form->getValue('arbeit_id'))
          );
    
          echo $dropdownArbeiten;
          ?>			  
			  </td>
			</tr>		
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('minuten'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('minuten'); ?></td>
			</tr>	
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('kommentar_antrag'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('kommentar_antrag'); ?></td>
			</tr>
			<tr>
        <td class="lb" colspan="2" style="font-weight:normal"><span class="star">* </span> Eingabe ist obligatorisch</td>
      </tr>
    </table>	
			
		<fieldset>
			<input type="submit" value="Speichern" />
			<input type="button" value="Abbrechen" 
			       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid='.$this->menuId)?>'" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]" />
			<?php echo $this->form->getInput('cf_uid'); ?>
		</fieldset>	
  </form>
  
  <div style="margin-top:15px">
    <strong>Hinweise:</strong>
    <ul>
      <li>Auf dieser Seite kannst du einen Antrag auf Gutschrift von Freiwilligenstunden stellen.</li>
      <li>Freiwilligenstunden haben keinen Einfluss auf dein Zeitkonto.</li>
    </ul>
  </div>
</div>