<?php

defined('_JEXEC') or die('Restricted access');

//JHtml::_('behavior.keepalive');
//JHtml::_('behavior.formvalidation');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
echo ZeitbankFrontendHelper::getScriptToHideHeaderImage();

?>

<h1 style="font-weight:bold;color: #7BA428; margin-bottom:10px;padding-bottom:0px;">
  <?php echo "Stunden verschenken"; ?>
</h1>

<div class="component">
  
	<form action="<?php echo JRoute::_("index.php?option=com_zeitbank&task=stundengeschenk.save&Itemid=".$this->menuId); ?>" 
			  id="geschenkForm" name="geschenkForm" method="post" class="form-validate">
	  
		<table class="market_form">
			<tr>
			  <td class="lb"><?php echo $this->form->getLabel('empfaenger'); ?><span class="star">* </span></td>
			  <td class="value">
			    <input type="text" name="jform[empfaenger]" id="autocomplete" size="60"/>
			    <script type="text/javascript">
  			    window.addEventListener("DOMContentLoaded", function() {
  			    	$('#autocomplete').autocomplete({
  	  			        serviceUrl: '/index.php?option=com_zeitbank&task=stundengeschenk.users&format=raw',
  	  			        minChars: 3,
  	  			        onSelect: function (suggestion) {
  	  			            $('empfaenger_id').value=suggestion.data;
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
			  <td class="lb"><?php echo $this->form->getLabel('kommentar'); ?></td>
			  <td class="value"><?php echo $this->form->getInput('kommentar'); ?></td>
			</tr>
			<tr>
        <td class="lb" colspan="2" style="font-weight:normal"><span class="star">* </span> Eingabe ist obligatorisch</td>
      </tr>
    </table>	
			
		<fieldset>
			<input type="submit" value="Speichern" />
			<input type="button" value="Abbrechen" 
			       onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_zeitbank&view=zeitbank')?>'" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" value="" name="jform[empfaenger_id]" id="empfaenger_id"/>
		</fieldset>	
  </form>
</div>