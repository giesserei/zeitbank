<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Edit-Formular "Arbeit-Administratoren"
 */
class ZeitbankViewArbeitAdmin extends BaseFormView
{

    /**
     * @var int
     */
    protected $kategorieId;

    public function display($tpl = null)
    {
        $this->initView();

        // Automcomplete-Script hinzufügen
        $document = JFactory::getDocument();
        $base = JURI::base(true);
        $document->addScript($base . '/components/com_zeitbank/template/js/jquery-1.8.2.min.js');
        $document->addScript($base . '/components/com_zeitbank/template/js/jquery.autocomplete.js');

        $this->kategorieId = ZeitbankAuth::getKategorieId();

        return parent::display($tpl);
    }

    protected function getAdministratoren()
    {
        $model = $this->getModel();
        return $model->getAdministratoren($this->kategorieId);
    }
}