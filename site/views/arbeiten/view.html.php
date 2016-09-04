<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

/**
 * View-Klasse für die Auflistung aller Arbeiten zu einer Kategorie.
 */
class ZeitbankViewArbeiten extends JViewLegacy
{

    /**
     * @var int
     */
    protected $menuId;

    /**
     * @var mixed
     */
    protected $arbeiten;

    function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank() || !ZeitbankAuth::isKategorieAdmin()) {
            return false;
        }

        $app = JFactory::getApplication();

        // Form-Daten aus Session löschen -> User hat die letzte Eingabe vielleicht nicht abgeschlossen
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, null);

        // Marktplatz aus Model laden
        $model = $this->getModel();
        $this->arbeiten = $model->getArbeiten(ZeitbankAuth::getKategorieId());

        ZeitbankFrontendHelper::addComponentStylesheet();

        // Menü-Id in der User-Session speichern
        $jinput = $app->input;
        $this->menuId = $jinput->get("Itemid", "0", "INT");
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);

        return parent::display($tpl);
    }
}