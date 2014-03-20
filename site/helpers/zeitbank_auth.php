<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Helperklasse für die Berechtigungsprüfung.
 *
 * @author Steffen Förster
 */
class ZeitbankAuth {

  const ACTION_REPORT_DOWNLOAD_SALDO = "report.download.saldo";
  
  const ACTION_REPORT_KEY_DATA = "report.key.data";
  
  /**
   * Folgende Bedingungen müssen erfüllt sein, damit der Benutzer Zugriff auf die Zeitbank hat:
   * 
   * - Der Benutzer muss angemeldet sein 
   * - Der Benutzer muss ein Bewohner oder ein Gewerbe der Giesserei sein
   * - Der Zugriff auf die Zeitbank darf nicht gesperrt sein
   * 
   * Ist eine der Bedingungen nicht erfüllt, wird eine Systemmeldung hinzugefügt und false geliefert
   */
  public static function checkAuthZeitbank() {
    $user = JFactory::getUser();
    if (!self::checkSignedIn($user) || !self::checkBewohnerGewerbe($user)) {
      return false;
    }
    
    $db = JFactory::getDBO();
    $query = "SELECT count(*) FROM #__mgh_zb_gesperrte_user 
              WHERE userid=".$user->id;
    $db->setQuery($query);
    $count = $db->loadResult();
    if ($count > 0) {
      JFactory::getApplication()->enqueueMessage('Der Zugriff wurde gesperrt.');
      return false;
    }
    
    return true;
  }
  
  /**
   * Prüft ob der Benutzer angemeldet ist und ob der Benutzer ein Bewohner oder ein Gewerbe der Giesserei ist.
   * Wenn nicht, wird eine Systemmeldung hinzugefügt und false geliefert.
   */
  public static function checkAuthMarket() {
    $user = JFactory::getUser();
    return (self::checkSignedIn($user) && self::checkBewohnerGewerbe($user));
  }
  
  /**
   * Prüft ob der Benutzer angemeldet ist und ob der Benutzer eine Berechtigung für die übergebene Action hat. 
   */
  public static function hasAccess($action) {
    $user = JFactory::getUser();
    if (!self::checkSignedIn($user)) {
      return false;
    }
    return $user->authorise($action, 'com_zeitbank');
  }
  
  /**
   * Liefert true, wenn der Benutzer Ämtli-Administrator ist.
   */
  public static function isAemtliAdmin() {
    $user = JFactory::getUser();
    $db = JFactory::getDBO();
    $query = "SELECT count(*)
              FROM #__mgh_zb_x_kat_arbeitadmin AS a 
              WHERE a.user_id = " . $user->id;
    $db->setQuery($query);
    return $db->loadResult() > 0;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------

  /**
   * Liefert true, wenn der Benutzer angemeldet ist; sonst false.
   * Wenn false, dann wird eine Systemmeldung hinzugefügt.
   */
  private static function checkSignedIn($user) {
    if ($user->guest) {
      JFactory::getApplication()->enqueueMessage('Die Registrierung ist abgelaufen. Bitte neu anmelden.');
      return false;
    }
    return true;
  }
  
  /**
   * Liefert true, wenn der User ein Bewohner oder ein Gewerbe ist; sonst false.
   * Wenn false, dann wird eine Systemmeldung hinzugefügt.
   */
  private static function checkBewohnerGewerbe($user) {
    $db = JFactory::getDBO();
    $query = "SELECT count(*) FROM #__mgh_aktiv_mitglied AS mgl
              WHERE mgl.userid=" . $user->id;
    $db->setQuery($query);
    $count = $db->loadResult();
    if ($count == 0) {
      JFactory::getApplication()->enqueueMessage('Zutritt nur für Bewohner und Gewerbe der Giesserei.');
      return false;
    }
    return true;
  }
}