<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Ablehnen von Anträgen.
 */
class ZeitbankModelAblehnung extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.ablehnung', 'ablehnung', $loadData);
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
        $valid &= $this->validateKommentar($validateResult['kommentar_ablehnung']);

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Es muss eine Begründung eingegeben werden.
     */
    private function validateKommentar($kommentar)
    {
        if (ZeitbankFrontendHelper::isBlank($kommentar)) {
            JFactory::getApplication()->enqueueMessage('Bitte begründe die Ablehnung des Antrags.', 'warning');
            return false;
        }

        return true;
    }

}