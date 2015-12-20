<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.filesystem.path');

require_once JPATH_COMPONENT . '/helpers/zeitbank.php';

/**
 * Kategorie Model.
 *
 * @since  1.6
 */
class ZeitbankModelKategorie extends JModelAdmin
{
  /**
   * The type alias for this content type.
   *
   * @var      string
   * @since    3.4
   */
  public $typeAlias = 'com_zeitbank.kategorie';

  /**
   * Kategorien können nicht gelöscht werden => bestehende Ämtli wären dann keiner Kategorie mehr zugeordnet
   *
   * @param object  $record  A record object.
   *
   * @return boolean
   */
  protected function canDelete($record)
  {
    return false;
  }

  /**
   * Method to check if you can save a record.
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   */
  protected function canSave($data = array(), $key = 'id')
  {
    return JFactory::getUser()->authorise('core.edit', $this->option);
  }

  /**
   * Method to get the row form.
   *
   * @param   array    $data      Data for the form.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  mixed  A JForm object on success, false on failure
   */
  public function getForm($data = array(), $loadData = true)
  {
    $options = array (
        'control' => 'jform',
        'load_data' => $loadData
    );
    $form = $this->loadForm('com_zeitbank.kategorie', 'kategorie', $options);

    if (empty($form)) {
      return false;
    }

    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return  mixed  The data for the form.
   */
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = array_merge((array) $this->getItem(), (array) JFactory::getApplication()->getUserState('com_zeitbank.edit.kategorie.data', array()));

    $this->preprocessData('com_zeitbank.kategorie', $data);

    return $data;
  }

  /**
   * Method to get a kategorie.
   *
   * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
   *
   * @return  mixed  Kategorie data object on success, false on failure.
   */
  public function getItem($pk = null)
  {
    return parent::getItem($pk);
  }

  /**
   * Returns a Table object, always creating it
   *
   * @param   string  $type    The table type to instantiate.
   * @param   string  $prefix  A prefix for the table class name. Optional.
   * @param   array   $config  Configuration array for model. Optional.
   *
   * @return  JTable    A database object.
   *
   * @since   1.6
   */
  public function getTable($type = 'Kategorie', $prefix = 'ZeitbankTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

}
