<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Edit-Formular.
 */
class ZeitbankViewStundentausch extends BaseFormView
{

    public function display($tpl = null)
    {
        $this->initView();

        $document = JFactory::getDocument();
        $base = JURI::base(true);
        $document->addScript($base . '/components/com_zeitbank/template/js/jquery-1.8.2.min.js');
        $document->addScript($base . '/components/com_zeitbank/template/js/jquery.autocomplete.js');

        return parent::display($tpl);
    }

    protected function getEmpfaengerName()
    {
        $userId = $this->form->getValue("empfaenger_id");
        if (empty($userId)) {
            return "";
        } else {
            return $this->getModel()->getEmpfaengerName($userId);
        }
    }
}