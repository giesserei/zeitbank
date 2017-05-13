<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Edit-Formular.
 */
class ZeitbankViewUpdAngebot extends BaseFormView
{

    /**
     * @var int
     */
    protected $art;

    public function display($tpl = null)
    {
        $this->initView();
        $this->setArt();

        $model = $this->getModel();
        if (!$model->isOwner($this->getId())) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
            return false;
        }

        return parent::display($tpl);
    }

    protected function getKategorien()
    {
        $model = $this->getModel();
        return $model->getKategorien();
    }

    protected function getArbeitsgattungen()
    {
        $model = $this->getModel();
        return $model->getArbeitsgattungen();
    }

    protected function isTauschView()
    {
        return $this->getArt() == 2;
    }

    protected function isArbeitView()
    {
        return $this->getArt() == 1;
    }

    protected function getArt()
    {
        return $this->art;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Speichert die Art des Eintrag für den Aufbau der View.
     */
    private function setArt()
    {
        if ($this->isNew()) {
            $app = JFactory::getApplication();
            $this->art = $app->getUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART);
        } else {
            $this->art = $this->form->getValue('art');
        }
    }
}