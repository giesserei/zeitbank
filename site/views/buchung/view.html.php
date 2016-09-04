<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * View zeigt die Details für eine Buchung an.
 */
class ZeitbankViewBuchung extends JViewLegacy
{

    /**
     * @var stdClass
     */
    protected $buchung;

    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank()) {
            return false;
        }

        $model = $this->getModel();
        $app = JFactory::getApplication();
        $id = $jinput = $app->input->get("id");

        if (!$this->isViewAllowed($id)) {
            return false;
        }

        $this->buchung = $model->getBuchung($id);

        ZeitbankFrontendHelper::addComponentStylesheet();

        return parent::display($tpl);
    }

    protected function isGeschenk($arbeitId)
    {
        return $arbeitId == ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;
    }

    protected function isGeschenkEmpfaenger($arbeitId, $gutschriftUserId)
    {
        $user = JFactory::getUser();
        return $this->isGeschenk($arbeitId) && $user->id == $gutschriftUserId;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function isViewAllowed($id)
    {
        $model = $this->getModel();
        if (!$model->isViewAllowed($id)) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diese Buchung zu sehen.', 'warning');
            return false;
        }
        return true;
    }
}
