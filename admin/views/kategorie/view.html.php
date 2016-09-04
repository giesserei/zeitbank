<?php
defined('_JEXEC') or die;

/**
 * The HTML Zeitbank Kategorie View.
 */
class ZeitbankViewKategorie extends JViewLegacy
{
    /**
     * @var  JForm
     */
    protected $form;

    /**
     * @var  object
     */
    protected $item;

    /**
     * @var  JObject
     */
    protected $state;

    /**
     * Display the view
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $this->canDo = JHelperContent::getActions('com_zeitbank');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        parent::display($tpl);
        $this->addToolbar();
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $canDo = $this->canDo;

        JToolbarHelper::title(JText::_($isNew ? 'Kategorie anlegen' : 'Kategorie bearbeiten'));

        // If a new item, can save the item.  Allow users with edit permissions to apply changes to prevent returning to grid.
        if ($isNew && $canDo->get('core.create')) {
            if ($canDo->get('core.edit')) {
                JToolbarHelper::apply('kategorie.apply');
            }

            JToolbarHelper::save('kategorie.save');
        }

        if (!$isNew && $canDo->get('core.edit')) {
            JToolbarHelper::apply('kategorie.apply');
            JToolbarHelper::save('kategorie.save');
        }

        if ($isNew) {
            JToolbarHelper::cancel('kategorie.cancel');
        } else {
            JToolbarHelper::cancel('kategorie.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
