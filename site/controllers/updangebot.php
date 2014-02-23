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
    $dataAllowed['email'] = $data['email'];
    $dataAllowed['telefon'] = $data['telefon'];
    $dataAllowed['telefon_frei'] = $data['telefon_frei'];
    $dataAllowed['handy'] = $data['handy'];
    $dataAllowed['handy_frei'] = $data['handy_frei'];
    $dataAllowed['birthdate'] = $data['birthdate'];
    
    return $dataAllowed;
  }
  
  /**
   * Daten ggf. in die DB-Darstellung umformatieren.
   */
  protected function formatData($data) {
    // Checkboxen auf 0 setzen, wenn nicht abgefüllt
    if (empty($data['telefon_frei'])) {
      $data['telefon_frei'] = 0;
    }
    if (empty($data['handy_frei'])) {
      $data['handy_frei'] = 0;
    }
  
    // Geburtstag formatieren
    $data['birthdate'] = GiessereiFrontendHelper::viewDateToMySqlDate($data['birthdate']);
  
    return $data;
  }
  
}