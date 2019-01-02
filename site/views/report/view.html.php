<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

/**
 * View-Klasse für die Reportseite der Zeitbank.
 */
class ZeitbankViewReport extends JViewLegacy
{

    /**
     * @var MarketPlaceOverview
     */
    protected $overview;

    /**
     * @var int
     */
    protected $menuId;

    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank() || !ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_KEY_DATA)) {
            return false;
        }

        $this->prepareDefault();

        return parent::display($tpl);
    }

    /**
     * Liefert die Summe der verbuchten Arbeitstunden ohne den Stundentausch.
     */
    public function getSummeArbeitStunden($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getSummeArbeitStunden($vorjahr);
    }

    /**
     * Liefert die Summe der nicht quittierten Arbeitstunden ohne den Stundentausch.
     */
    public function getSummeNichtQuittierteStunden($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getSummeNichtQuittierteStunden($vorjahr);
    }

    /**
     * Liefert die durchschnittliche Wartezeit der noch unquittierten Buchungen.
     */
    public function getWartezeitUnquittierteBuchungen($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getWartezeitUnquittierteBuchungen($vorjahr);
    }

    /**
     * Liefert die Summen der verbuchten Giesserei-Stunden je Arbeitskategorie.
     */
    protected function getSummeGiessereiStundenNachKategorie($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getSummeGiessereiStundenNachKategorie($vorjahr);
    }

    /**
     * Liefert die Summen der verbuchten Sonstigen-Stunden (freiwillig, privater Stundentausch, Geschenke) je Arbeitskategorie.
     */
    protected function getSummeSonstigeStundenNachKategorie($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getSummeSonstigeStundenNachKategorie($vorjahr);
    }

    /**
     * Liefert die maximale und die durchschnittliche Dauer zwischen einer Buchung und der Quittierung.
     */
    public function getQuittungDauer($vorjahr = false)
    {
        $model = $this->getModel();
        return $model->getQuittungDauer($vorjahr);
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function prepareDefault()
    {
        $app = JFactory::getApplication();

        // Statistiken aus Datenbank laden
        //$model = $this->getModel();
        //$this->overview = $model->getOverview(5);

        ZeitbankFrontendHelper::addComponentStylesheet();

        // Menü-Id in der User-Session speichern
        $jinput = $app->input;
        $this->menuId = $jinput->get("Itemid", "0", "INT");
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);
    }


}