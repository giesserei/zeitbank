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

        return parent::display($tpl);
    }

    protected function getEmpfaengerName()
    {
        $userId = $this->form->getValue("belastung_userid");
        if (empty($userId)) {
            return "";
        } else {
            return $this->getModel()->getEmpfaengerName($userId);
        }
    }
}