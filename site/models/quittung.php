<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Quittieren von Anträgen.
 */
class ZeitbankModelQuittung extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.quittung', 'quittung', $loadData);
    }

    /**
     * Prüft, ob die Eingaben korrekt sind. Validierungsmeldungen werden im Model gespeichert.
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
        $valid &= $this->validateMinuten($validateResult['id']);

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Beim privaten Stundentausch muss ein entsprechendes Guthaben vorhanden sein.
     * Sonst gibt es keine weitere Validierung.
     *
     * @param $journalId int ID der Buchung
     *
     * @return boolean True, wenn die Validierung erfolgreich war
     */
    private function validateMinuten($journalId)
    {
        $buchung = $this->getItem($journalId);

        if ($buchung->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH) {
            $saldo = ZeitbankCalc::getSaldo($this->user->id);

            if ($buchung->minuten > $saldo) {
                JFactory::getApplication()->enqueueMessage(
                    'Der Stundentausch übersteigt dein aktuelles Guthaben (' . $saldo . ' Minuten).', 'warning');
                return false;
            }
        }

        return true;
    }

}