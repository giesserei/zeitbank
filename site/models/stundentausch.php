<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Erstellen und Bearbeiten eines Antrags für einen Stundentausch.
 */
class ZeitbankModelStundentausch extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.stundentausch', 'stundentausch', $loadData);
    }

    /**
     * Prüft, ob die Eingaben korrekt sind.
     *
     * Validierungsmeldungen werden im Model gespeichert.
     *
     * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
     *
     * @inheritdoc
     */
    public function validate($form, $data, $group = NULL)
    {
        $validateResult = parent::validate($form, $data, $group);
        if ($validateResult === false) {
            return false;
        }

        $valid = 1;
        $valid &= $this->validateEmpfaenger($validateResult['belastung_userid']);

        if ((bool)$valid) {
            $valid &= $this->validateMinuten($validateResult['minuten']);
        }
        if ((bool)$valid) {
            $valid &= $this->validateDatumAntrag($validateResult['datum_antrag']);
        }

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    public function getEmpfaengerName($userId)
    {
        $query = "SELECT CONCAT(vorname, ' ', nachname) name
              FROM #__mgh_aktiv_mitglied
              WHERE userid = " . $userId;
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function validateMinuten($minuten)
    {
        if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
            JFactory::getApplication()->enqueueMessage('Bitte die Minuten eingeben.', 'warning');
            return false;
        }
        if (!is_numeric($minuten)) {
            JFactory::getApplication()->enqueueMessage('Im Feld Minuten sind nur Zahlen zulässig.', 'warning');
            return false;
        }
        $minutenInt = intval($minuten);
        if ($minutenInt <= 0) {
            JFactory::getApplication()->enqueueMessage('Die Anzahl der Minuten muss grösser 0 sein.', 'warning');
            return false;
        }

        return true;
    }

    /**
     * Liefert true, wenn der Empfänger ein aktiver Bewohner oder Gewerbe ist; sonst false.
     * Auch darf dies nicht der angemeldete Benutzer sein.
     *
     * @param $empfaengerId int User-ID des Empfängers
     *
     * @return boolean
     */
    private function validateEmpfaenger($empfaengerId)
    {
        if (!isset($empfaengerId)) {
            JFactory::getApplication()->enqueueMessage('Bitte Empfänger auswählen. Der Empfänger muss aus der Liste gewählt werden, nachdem mindestens 3 Buchstaben vom Namen eingegeben wurden.', 'warning');
            return false;
        }

        $query = "SELECT userid, vorname, nachname
              FROM #__mgh_mitglied m
              WHERE m.typ IN (1,2) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
                AND userid = " . $this->db->quote($empfaengerId) . "
                AND userid != " . $this->user->id;

        $this->db->setQuery($query);
        $count = $this->db->loadResult();

        if ($count == 0) {
            JFactory::getApplication()->enqueueMessage('Die Auswahl des Empfängers hat nicht funktioniert. Der Empfänger muss aus der Liste gewählt werden, nachdem mindestens 3 Buchstaben vom Namen eingegeben wurden. Ggf. hilft die Verwendung eines anderen Browsers.', 'warning');
            return false;
        }

        return true;
    }

}