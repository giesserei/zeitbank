<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankControllerUpdZeitbankBase', JPATH_COMPONENT . '/controllers/upd_zeitbank_base.php');

/**
 * Basis-Klasse für die Controller zum Editieren eines Journaleintrags der Zeitbank.
 */
abstract class ZeitbankControllerUpdJournalBase extends ZeitbankControllerUpdZeitbankBase
{

    /**
     * Schneidet den Kommentar auf die zulässige Länge ab.
     *
     * @param $kommentar string Kommentar
     * @return string ggf. gekürzter Kommentar
     */
    protected function cropKommentar($kommentar)
    {
        return ZeitbankFrontendHelper::cropText($kommentar, 1000);
    }

}