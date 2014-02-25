<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankControllerUpdAngebotBase', JPATH_COMPONENT . '/controllers/upd_angebot_base.php');

jimport('joomla.application.component.controllerform');

/**
 * Controller zum Editieren eines Angebots.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerUpdAngebot extends ZeitbankControllerUpdAngebotBase {
  
  // -------------------------------------------------------------------------
  // protected section
  // -------------------------------------------------------------------------
  
  /**
   * @see ZeitbankControllerUpdAngebotBase::getViewName()
   */
  protected function getViewName() {
    return "updangebot";
  }
  
  /**
   * @see ZeitbankControllerUpdAngebotBase::saveDataInSession()
   */
  protected function saveDataInSession() {
    return true;
  }
  
  /**
   * @see ZeitbankControllerUpdAngebotBase::filterFormFields()
   */
  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    $dataAllowed['titel'] = $data['titel'];
    $dataAllowed['beschreibung'] = $data['beschreibung'];
    $dataAllowed['art'] = $data['art'];
    $dataAllowed['richtung'] = $data['richtung'];
    $dataAllowed['kategorie_id'] = $data['kategorie_id'];
    $dataAllowed['status'] = $data['status'];
    $dataAllowed['ablauf'] = $data['ablauf'];
    
    return $dataAllowed;
  }
  
  /**
   * @see ZeitbankControllerUpdAngebotBase::redirectSuccessView()
   */
  protected function redirectSuccessView() {
    $app = JFactory::getApplication();
    $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    $this->setRedirect(
        JRoute::_('index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$menuId, false)
    );
  }
  
}