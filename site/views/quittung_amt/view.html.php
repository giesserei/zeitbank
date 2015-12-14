<?php 
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * Diese View listet alle offenen Anträge auf, für die der Benutzer als Ämtli-Administrator registriert ist.
 * 
 * @author JAL
 */
class ZeitbankViewQuittung_Amt extends JViewLegacy {

  /**
   * @var array[]
   */
  protected $quittierungen;
  
  public function display($tpl = null) {
    $model = $this->getModel();
    $this->quittierungen = $model->getOffeneQuittierungen();
    
    ZeitbankFrontendHelper::addComponentStylesheet();
    
    return parent::display($tpl);
  }
}
