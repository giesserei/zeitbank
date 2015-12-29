<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BaseSimpleView', JPATH_COMPONENT . '/views/base_simple_view.php');

class ZeitbankViewArbeitsliste extends BaseSimpleView
{
    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank()) {
            return false;
        }

        $this->initView();

        return parent::display($tpl);
    }

    protected function getArbeitsliste()
    {
        return $this->getModel()->getArbeitsliste();
    }
}