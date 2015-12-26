<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

/**
 * Basisklasse für die Model-Klassen, mit denen Datenbank-Objekte erstellt oder bearbeitet werden können.
 */
abstract class ZeitbankModelUpdBase extends JModelAdmin
{

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * @var JUser
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->db = JFactory::getDBO();
        $this->user = JFactory::getUser();
    }

    /**
     * Eigene Implementierung der save-Methode.
     *
     * @param $data array Zu speichernde Daten
     * @param $id ID des Datensatzes
     * @return true, wenn das Speichern erfolgreich war, sonst false
     *
     * @see JModelAdmin::save()
     */
    public function saveItem($data, $id)
    {
        $table = $this->getTable();

        try {
            // Daten in die Tabellen-Instanz laden
            $table->load($id);

            // Properties mit neuen Daten überschreiben
            if (!$table->bind($data, 'id')) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
                return false;
            }

            // Tabelle kann vor dem Speichern letzte Datenprüfung vornehmen
            if (!$table->check()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
                return false;
            }

            // Jetzt Daten speichern
            if (!$table->store()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
                return false;
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR);
            JFactory::getApplication()->enqueueMessage('Speichern fehlgeschlagen!', 'error');
            return false;
        }

        return true;
    }

    /**
     * Löscht den übergebenen Antrag aus der Datenbank.
     */
    public function deleteItem($id)
    {
        $table = $this->getTable();

        try {
            if (!$table->delete($id)) {
                JLog::add($table->getError(), JLog::ERROR);
                JFactory::getApplication()->enqueueMessage('Löschen fehlgeschlagen!', 'error');
                return false;
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR);
            JFactory::getApplication()->enqueueMessage('Löschen fehlgeschlagen!', 'error');
            return false;
        }
        return true;
    }

    /**
     * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
     */
    protected function loadFormData()
    {
        $data = $this->getDataFromSession();

        if (empty($data)) {
            $data = $this->getItem();
        } else {
            // ID im State setzen, damit diese von der View ausgelesen werden kann
            $this->state->set($this->getName() . '.id', $data['id']);
        }

        return $data;
    }

    /**
     * Liefert die Formulardaten aus der Session oder ein leeres array, wenn keine Daten gespeichert sind.
     *
     * @return array $data Daten aus der Session
     */
    abstract protected function getDataFromSession();

    /**
     * Hilfsmethode zum Laden des Forms.
     *
     * @param bool $loadData
     * @param string $name
     * @param string $source
     * @return bool|mixed
     */
    protected function createForm($name, $source, $loadData = true)
    {
        $form = $this->loadForm($name, $source, array(
            'control' => 'jform',
            'load_data' => $loadData
        ));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

}