<?php

defined('_JEXEC') or die('Restricted access');

// Autocompletion: https://github.com/devbridge/jQuery-Autocomplete

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">
 
  <h1 class="zeitbank">
    <?php echo ($this->isNew() ? "Zeitbank: Neuen Antrag für Giessereistunden erstellen" : "Zeitbank: Antrag für Giessereistunden bearbeiten"); ?>
  </h1>
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=eigenleistungen.save&Itemid=".$this->menuId); ?>" 
			  id="eigenleistungenForm" name="eigenleistungenForm" method="post">
	  
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
      <li>Auf dieser Seite kannst du einen Antrag auf Gutschrift von Giessereistunden stellen.</li>
      <li>Wenn du die Arbeitsgattung nicht kennst, kontaktiere bitte den Ressortverantwortlichen, welcher dir den Arbeitsauftrag gegeben hat.</li>
      <li>Die beantragten Minuten werden deinem Konto gutgeschrieben, sobald der Ämtli-Verantwortliche deinen Antrag quittiert hat.</li>
      <li>Für die Arbeitskategorien mit einer Pauschale kannst du das Feld Minuten frei lassen.</li>
    </ul>
  </div>
</div>