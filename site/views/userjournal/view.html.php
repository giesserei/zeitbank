<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BaseSimpleView', JPATH_COMPONENT . '/views/base_simple_view.php');

class ZeitbankViewUserjournal extends BaseSimpleView
{

    protected $journal;

    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank()) {
            return false;
        }

        $this->initView();

        $model = $this->getModel();
        $this->journal = $model->getUserJournal();

        return parent::display($tpl);
    }

    protected function getUserName($userId)
    {
        return $this->getModel()->getUserName($userId);
    }
}
