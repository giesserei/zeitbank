<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdZeitbankBase', JPATH_COMPONENT . '/controllers/upd_zeitbank_base.php');

/**
 * Controller zum Editieren einer Arbeit.
 */
class ZeitbankControllerArbeit extends ZeitbankControllerUpdZeitbankBase
{

    public function orderUp()
    {
        $id = $this->getId();

        if ($this->isEditAllowed($id)) {
            $this->getModel()->orderUp($this->getId());
        }

        $this->redirectSuccessView($id);
        return true;
    }

    public function orderDown()
    {
        $id = $this->getId();

        if ($this->isEditAllowed($id)) {
            $this->getModel()->orderDown($this->getId());
        }

        $this->redirectSuccessView($id);
        return true;
    }

    protected function filterFormFields($data)
    {
        $dataAllowed = array();
        $dataAllowed['id'] = $data['id'];
        $dataAllowed['kurztext'] = $data['kurztext'];
        $dataAllowed['beschreibung'] = $data['beschreibung'];
        $dataAllowed['jahressoll'] = $data['jahressoll'];
        $dataAllowed['pauschale'] = $data['pauschale'];
        $dataAllowed['aktiviert'] = $data['aktiviert'];
        $dataAllowed['kommentar'] = $data['kommentar'];
        $dataAllowed['admin_id'] = $data['admin_id'];
        $dataAllowed['ordering'] = $data['ordering'];
        return $dataAllowed;
    }

    protected function modifyDataBeforeSave($data)
    {
        $data['kategorie_id'] = ZeitbankAuth::getKategorieId();

        if (!isset($data['aktiviert'])) {
            $data['aktiviert'] = 0;
        }

        if (intval($data['ordering']) == 0) {
            $data['ordering'] = $this->getModel()->getNextOrdering();
        }

        return $data;
    }

    protected function isSaveDataInSession()
    {
        return true;
    }

    protected function getViewName()
    {
        return "arbeit";
    }

    /**
     * Liefert true, wenn der Benutzer eine Arbeit bearbeiten will und er der passende Kategorien-Administrator ist.
     *
     * @inheritdoc
     */
    protected function isEditAllowed($id)
    {
        if (!ZeitbankAuth::isKategorieAdmin()) {
            JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt.', 'warning');
            return false;
        }

        if ($id == 0) {
            return true;
        }
        else {
            $model = $this->getModel();
            if (!$model->isOwner($id)) {
                JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
                return false;
            }
        }

        return true;
    }

    protected function isSaveAllowed($id, $data)
    {
        return $this->isEditAllowed($id);
    }

    /**
     * Arbeiten dürfen nicht gelöscht werden, da die Journal-Einträge an diesen hängen und sonst inkonsistente Daten
     * entstehen. Vielleicht können irgendwann die anonymisierten Journal-Einträge gelöscht werden. Dann kann man
     * auch die nicht mehr benötigten Arbeiten löschen.
     *
     * @inheritdoc
     */
    protected function isDeleteAllowed($id)
    {
        JFactory::getApplication()->enqueueMessage('Das Löschen ist nicht zulässig.', 'warning');
        return false;
    }

    protected function redirectSuccessView($id)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $this->setRedirect(
            JRoute::_('index.php?option=com_zeitbank&view=arbeiten&Itemid=' . $menuId, false)
        );
    }

}