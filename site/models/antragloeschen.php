<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model für die Ausführung eines Stundengeschenks.
 */
class ZeitbankModelAntragLoeschen extends ZeitbankModelUpdJournalBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

    /**
     * Löscht den übergebenen Antrag aus der Datenbank.
     *
     * @inheritdoc
     */
    public function delete($id)
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
}

