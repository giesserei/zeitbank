<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BaseSimpleView', JPATH_COMPONENT . '/views/base_simple_view.php');

/**
 * View zeigt alle Buchungen an, die der Benutzer quittiert hat.
 */
class ZeitbankViewQuittungsliste_Amt extends BaseSimpleView
{

    const TAGE = 450;

    /**
     * @var array
     */
    protected $quittungsliste;

    public function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthZeitbank()) {
            return false;
        }

        $this->initView();
        $this->quittungsliste = $this->getModel()->getQuittungsliste(self::TAGE);

        return parent::display($tpl);
    }

    protected function getTage()
    {
        return self::TAGE;
    }
}