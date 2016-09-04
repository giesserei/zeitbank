<?php
defined('_JEXEC') or die;

/**
 * Kategorie Item List Model for Kategorien.
 */
class ZeitbankModelKategorien extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'bezeichnung', 'a.bezeichnung',
                'gesamtbudget', 'a.gesamtbudget',
                'user_id', 'a.user_id',
                'ordering, a.ordering'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string $ordering An optional ordering field.
     * @param   string $direction An optional direction (asc|desc).
     *
     * @return  void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Component parameters.
        $params = JComponentHelper::getParams('com_zeitbank');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.bezeichnung', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string  A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * Builds an SQL query to load the list data.
     *
     * @return  JDatabaseQuery    A query object.
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select all fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                $db->quoteName(
                    array(
                        'a.id', 'a.bezeichnung', 'a.gesamtbudget', 'a.user_id', 'a.ordering'
                    ),
                    array(
                        null, null, null, null, null
                    )
                )
            )
        );
        $query->from($db->quoteName('#__mgh_zb_kategorie') . ' AS a');

        // Join over the users
        $query->select('u.name AS admin_name')
            ->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id = a.admin_id');

        // Filter by search in bezeichnung, or id
        if ($search = trim($this->getState('filter.search'))) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(' . 'a.bezeichnung LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        return $query;
    }
}