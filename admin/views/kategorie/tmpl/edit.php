<?php
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.core');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

// ohne dieses Script funktionieren die Buttons der Toolbar nicht
JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById("item-form"));
	};
');

?>

<form action="<?php echo JRoute::_('index.php?option=com_zeitbank&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

  <div class="form-horizontal">

    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('Details', true)); ?>
    <div class="row-fluid">
      <div class="span3">
        <?php
        $this->fields = array(
            'bezeichnung',
            'gesamtbudget',
            'user_id',
            'admin_id',
            'ordering'
        );
        ?>
        <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
  </div>

  <input type="hidden" name="task" value="" />
  <?php echo $this->form->getInput('component_id'); ?>
  <?php echo JHtml::_('form.token'); ?>
  <input type="hidden" id="fieldtype" name="fieldtype" value="" />
</form>