<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('BuchungHelper', JPATH_COMPONENT . '/helpers/buchung.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model für die Ausführung eines Stundengeschenks.
 */
class ZeitbankModelStundenGeschenk extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.stundengeschenk', 'stundengeschenk', $loadData);
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
        $valid &= $this->validateEmpfaenger($validateResult['empfaenger_id']);

        if ((bool)$valid) {
            $valid &= $this->validateDatumAntrag($validateResult['datum_antrag']);
        }
        if ((bool)$valid) {
            $year = substr($validateResult['datum_antrag'], 0, 4);
            $lastYear = strcmp($year, date('Y')) != 0;
            $valid &= $this->validateMinuten($validateResult['minuten'], $validateResult['empfaenger_id'], $lastYear);
        }

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    /**
     * Es werden keine Buchungen bearbeitet, damit muss auch nichts aus der DB geladen werden.
     * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
     */
    protected function loadFormData()
    {
        return $this->getDataFromSession();
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Die verschenkte Zeit darf das vorhandene Guthaben nicht übersteigen.
     * Es kann Zeit maximal bis zur Erreichung des Stundensolls des Empfängers verschenkt werden.
     *
     * 2014-09-21 Es wird berücksichtigt, dass einige Tage nach dem Jahreswechsel auch noch auf das letzte Jahr gebucht werden darf.
     */
    private function validateMinuten($minuten, $empfaengerId, $lastYear)
    {
        if (!isset($minuten) || ZeitbankFrontendHelper::isBlank($minuten)) {
            JFactory::getApplication()->enqueueMessage(
                'Bitte die Zeit eingeben, die du verschenken möchtest.', 'warning');
            return false;
        }
        if (!is_numeric($minuten)) {
            JFactory::getApplication()->enqueueMessage(
                'Im Feld Minuten sind nur Zahlen zulässig.', 'warning');
            return false;
        }
        $minutenInt = intval($minuten);
        if ($minutenInt <= 0) {
            JFactory::getApplication()->enqueueMessage(
                'Die Anzahl der Minuten muss grösser 0 sein.', 'warning');
            return false;
        }

        $saldo = $lastYear ? ZeitbankCalc::getSaldoVorjahr($this->user->id) : ZeitbankCalc::getSaldo($this->user->id);

        if ($minutenInt > $saldo) {
            JFactory::getApplication()->enqueueMessage(
                'Du kannst maximal dein aktuelles Guthaben verschenken (' . $saldo . ' Minuten).', 'warning');
            return false;
        }

        // Prüfung des Empfängersolls nicht bei Stundenfonds nötig - beim Gewerbe wird zur Vereinfachung bisher auf die
        // Prüfung verzichtet
        if (!BuchungHelper::isStundenfonds($empfaengerId) && !BuchungHelper::isGewerbe($empfaengerId)) {
            $saldoEmpfaenger = $lastYear
                ? ZeitbankCalc::getSaldoVorjahr($empfaengerId)
                : ZeitbankCalc::getSaldo($empfaengerId);

            // Dispensation wird nicht berücksichtigt (geschenkte Stunden können so eine Zahlung der
            // Hauswartspauschale verhindern) => kleine Unschärfe: Jugendliche in Erstausbildung sind dispensiert, zahlen
            // jedoch keine Hauswartentschädigung
            $sollEmpfaenger = ZeitbankCalc::getSollBewohner($empfaengerId, false);

            if ($saldoEmpfaenger >= $sollEmpfaenger) {
                JFactory::getApplication()->enqueueMessage(
                    'Der Empfänger benötigt keine Stunden mehr.', 'warning');
                return false;
            } else if ($saldoEmpfaenger + $minutenInt > $sollEmpfaenger) {
                JFactory::getApplication()->enqueueMessage(
                    'Der Empfänger benötigt nur noch ' . ($sollEmpfaenger - $saldoEmpfaenger)
                    . ' Minuten zur Erreichung des Stundensolls.', 'warning');
                return false;
            }
        }

        return true;
    }

    /**
     * Liefert true, wenn der Empfänger ein aktiver Bewohner oder der Stundenfonds ist; sonst false.
     * Auch darf dies nicht der angemeldete Benutzer sein.
     *
     * @param $empfaengerId int User-ID, des Empfängers
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
              WHERE m.typ IN (1,2,7) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
                AND userid = " . mysql_real_escape_string($empfaengerId) . "
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