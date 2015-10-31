<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankControllerUpdBase', JPATH_COMPONENT . '/controllers/upd_base.php');

jimport('joomla.application.component.controllerform');

/**
 * Controller zum Editieren einer Kategorie.
 *
 * @author Steffen Förster
 */
class ZeitbankControllerKategorie extends ZeitbankControllerUpdBase {

  protected function checkGeneralPermission() {
    return ZeitbankAuth::checkAuthZeitbank();
  }

  protected function clearSessionData() {
    $app = JFactory::getApplication();
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, null);
  }

  protected function saveDataInSession($data) {
    $app = JFactory::getApplication();
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, $data);
  }

  protected function filterFormFields($data) {
    $dataAllowed = array();
    $dataAllowed['id'] = $data['id'];
    $dataAllowed['gesamtbudget'] = $data['gesamtbudget'];
    return $dataAllowed;
  }

  protected function isSaveDataInSession() {
    return true;
  }

  protected function getViewName() {
    return "kategorie";
  }
  
  /**
   * Liefert true, wenn der Benutzer die Kategorie bearbeiten darf.
   */
  protected function isEditAllowed($id) {
    if ($id == 0 || !ZeitbankAuth::isKategorieAdmin($id)) {
      JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.','warning');
      return false;
    }
    return true;
  }
  
}