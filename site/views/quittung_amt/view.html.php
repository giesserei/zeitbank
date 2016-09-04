<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BaseSimpleView', JPATH_COMPONENT . '/views/base_simple_view.php');

/**
 * Diese View listet alle offenen Anträge auf, für die der Benutzer als Ämtli-Administrator registriert ist.
 */
class ZeitbankViewQuittung_Amt extends BaseSimpleView
{

    /**
     * @var array
     */
    protected $quittierungen;

    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank()) {
            return false;
        }

        $this->initView();
        $this->quittierungen = $this->getModel()->getOffeneQuittierungen();

        return parent::display($tpl);
    }
}
