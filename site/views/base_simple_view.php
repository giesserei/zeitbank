<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Basisklasse für die Views, welche nur eine einfache Seite ohne Formular darstellen.
 */
class BaseSimpleView extends JViewLegacy
{

    /**
     * @var int
     */
    protected $menuId;

    /**
     * Inialisiert die View.
     *
     * @throws Exception
     */
    protected function initView()
    {
        ZeitbankFrontendHelper::addComponentStylesheet();

        $app = JFactory::getApplication();
        $jinput = $app->input;
        $this->menuId = $jinput->get("Itemid", "0", "INT");
    }
}