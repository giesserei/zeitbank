<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * View zeigt alle Buchungen an, die der Benutzer quittiert hat.
 */
class ZeitbankViewQuittungsliste_Amt extends JViewLegacy
{

    /**
     * @var array[]
     */
    protected $quittungsliste;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->quittungsliste = $model->getQuittungsliste();

        ZeitbankFrontendHelper::addComponentStylesheet();

        return parent::display($tpl);
    }
}
