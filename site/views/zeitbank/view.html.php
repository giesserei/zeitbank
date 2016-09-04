<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');
JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('BuchungHelper', JPATH_COMPONENT . '/helpers/buchung.php');

/**
 * View der Einstiegsseite zur Zeitbank.
 */
class ZeitbankViewZeitbank extends JViewLegacy
{

    protected $quittierungen;

    protected $antraege;

    protected $journal;

    protected $menuId;

    function display($tpl = null)
    {
        // Menu in Session speichern
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $this->menuId = $jinput->get("Itemid", "0", "INT");
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);

        $model = $this->getModel();
        $this->quittierungen = $model->getOffeneQuittierungen();
        $this->antraege = $model->getOffeneAntraege();
        $this->journal = $model->getUserJournal();

        ZeitbankFrontendHelper::addComponentStylesheet();

        parent::display($tpl);
    }

    /**
     * Liefert das Soll für den Bewohner.
     */
    protected function getSoll()
    {
        $user = JFactory::getUser();
        return ZeitbankCalc::getSollBewohner($user->id);
    }

    /**
     * Liefert den Saldo für den Bewohner/das Gewerbe.
     */
    protected function getSaldo()
    {
        $user = JFactory::getUser();
        return ZeitbankCalc::getSaldo($user->id);
    }

    protected function getSaldoFreiwilligenarbeit()
    {
        $user = JFactory::getUser();
        return ZeitbankCalc::getSaldoFreiwilligenarbeit($user->id);
    }

    protected function getSaldoVorjahr()
    {
        $user = JFactory::getUser();
        return ZeitbankCalc::getSaldoVorjahr($user->id);
    }

    protected function getSaldoStundenfonds()
    {
        $userId = BuchungHelper::getStundenfondsUserId();
        return ZeitbankCalc::getSaldo($userId);
    }

    protected function getSaldoStundenfondsVorjahr()
    {
        $userId = BuchungHelper::getStundenfondsUserId();
        return ZeitbankCalc::getSaldoVorjahr($userId);
    }

    protected function isGewerbe()
    {
        $user = JFactory::getUser();
        $model = $this->getModel();
        return $model->isGewerbe($user->id);
    }

    protected function getKategorieItem($id)
    {
        return $this->getModel()->getKategorieItem($id);
    }

    protected function getAnzahlOffeneQuittierungen()
    {
        $user = JFactory::getUser();
        return $this->getModel()->getAnzahlOffeneQuittierungen($user->id);
    }
}
