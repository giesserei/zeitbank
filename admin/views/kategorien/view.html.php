<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankHelper', JPATH_COMPONENT . '/helpers/zeitbank.php');

/**
 * The HTML Zeitbank Kategorien Items View.
 */
class ZeitbankViewKategorien extends JViewLegacy
{
    /**
     * @var  mixed
     */
    protected $items;

    /**
     * @var  JPagination
     */
    protected $pagination;

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
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        ZeitbankHelper::addSubmenu('kategorien');

        $this->ordering = array();

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {
        JToolbarHelper::title('Zeitbank - Kategorien');
        JToolbarHelper::addNew('kategorie.add');
        JToolbarHelper::editList('kategorie.edit');

        $user = JFactory::getUser();

        if ($user->authorise('core.manage', 'com_zeitbank')) {
            JToolBarHelper::preferences('com_zeitbank');
        }
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'a.id' => 'ID',
            'a.bezeichnung' => 'Bezeichnung'
        );
    }
}