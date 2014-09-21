<?php

defined('_JEXEC') or die('Restricted access');

// Autocompletion: https://github.com/devbridge/jQuery-Autocomplete

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<div class="component">
 
  <h1 class="zeitbank">
    <?php echo ($this->isNew() ? "Zeitbank: Neuen Antrag für Stundentausch erstellen" : "Zeitbank: Antrag für Stundentausch bearbeiten"); ?>
  </h1>
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=stundentausch.save&Itemid=".$this->menuId); ?>" 
			  id="tauschForm" name="tauschForm" method="post">
	  
		<table class="zb_form">
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('empfaenger'); ?><span class="star">* </span></td>
			  <td class="value">
			    <input type="text" name="jform[empfaenger]" id="autocomplete" size="60" value="<?php echo $this->getEmpfaengerName(); ?>" />
			    <script type="text/javascript">
  			    window.addEventListener("DOMContentLoaded", function() {
  			    	$('#autocomplete').autocomplete({
  	  			        serviceUrl: '/index.php?option=com_zeitbank&task=buchung.users&format=raw',
  	  			        minChars: 3,
  	  			        paramName: 'query',
  	  			        onSelect: function (suggestion) {
  	  			            $('#empfaenger_id').val(suggestion.data);
  	  			        }
  	  			    });
  			    }, false);
			    </script>
			  </td>
			</tr>	
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('minuten'); ?><span class="star">* </span></td>
			  <td class="value"><?php echo $this->form->getInput('minuten'); ?></td>
			</tr>	
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('kommentar_antrag'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('kommentar_antrag'); ?><br/>(max. 1000 Zeichen)</td>
			</tr>
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('datum_antrag'); ?></td>
			  <td class="value">
          <?php
          $datumAntrag = array(date('Y-m-d') => date('Y'));
          
          if (ZeitbankCalc::isLastYearAllowed()) {
            $lastYear = intval(date('Y')) - 1;
            $datumAntrag[$lastYear.'-12-31'] = (string) $lastYear;
          }
          
          $options = array();
    
          foreach($datumAntrag as $key=>$value) {
    	      $options[] = JHTML::_('select.option', $key, $value);
          }
          
          $dropdownDatumAntrag = JHTML::_('select.genericlist', $options, 'jform[datum_antrag]', 
                  array('class'=>'inputbox', 'id'=>'jform_datum_antrag'), 'value', 'text', $this->form->getValue('datum_antrag'));
    
          echo $dropdownDatumAntrag;
          ?>			  
			  </td>
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
			<input type="hidden" value="<?php echo $this->form->getValue('empfaenger_id'); ?>" name="jform[empfaenger_id]" id="empfaenger_id"/>
			<input type="hidden" value="<?php echo $this->getId(); ?>" name="jform[id]" />
			<?php echo $this->form->getInput('cf_uid'); ?>
		</fieldset>	
  </form>
  
  <div style="margin-top:15px">
    <strong>Hinweise:</strong>
    <ul>
      <li>Der Stundentausch ist erst wirksam, wenn der/die TauschparterIn deinen Antrag quittiert hat.</li>
      <li>Der/die TauschparterIn kann maximal das vorhandene Guthaben tauschen.</li>
      <li>Nach dem Jahreswechsel kannst du noch bis zum <strong>05.01.</strong> einen Antrag für das vergangene Jahr stellen.</li>
    </ul>
  </div>
</div>