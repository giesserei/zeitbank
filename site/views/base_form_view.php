<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Basisklasse für die View-Klassen.
 */
class BaseFormView extends JViewLegacy
{

    /**
     * @var int
     */
    protected $menuId;

    /**
     * @var JObject
     */
    protected $state;

    /**
     * @var JForm
     */
    protected $form;

    /**
     * Inialisiert die View.
     *
     * @throws Exception
     */
    protected function initView()
    {
        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->form = $this->get('Form');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode('\n', $errors));
        }

        ZeitbankFrontendHelper::addComponentStylesheet();

        // Menü-Id wird in View im Form-Action gesetzt
        $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    }

    protected function getId()
    {
        return (int)$this->state->get($this->getModel()->getName() . '.id');
    }

    protected function isNew()
    {
        return $this->getId() == 0;
    }
}