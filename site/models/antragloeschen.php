<?php

defined('_JEXEC') or die;

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Löschen eines Antrags.
 */
class ZeitbankModelAntragLoeschen extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

}

