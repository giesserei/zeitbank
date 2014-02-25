<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Helperklasse.
 *
 * @author Steffen Förster
 */
class ZeitbankFrontendHelper {

  /**
   * Prüft ob der Benutzer angemeldet ist und ob der Benutzer ein Bewohner oder ein Gewerbe der Giesserei ist.
   * Wenn nicht, wird eine Systemmeldung hinzugefügt und false geliefert.
   */
  public static function checkAuthMarket() {
    $user = JFactory::getUser();
    if ($user->guest) {
      JFactory::getApplication()->enqueueMessage('Die Registrierung ist abgelaufen. Bitte neu anmelden.');
      return false;
    } 
    else {
      $db = JFactory::getDBO();
      $query = "SELECT mgl.id FROM #__mgh_mitglied AS mgl 
                WHERE mgl.typ IN (1,2) 
                  AND (mgl.austritt = '0000-00-00' OR mgl.austritt > NOW()) 
                  AND mgl.userid=" . $user->id;
      $db->setQuery($query);
      $row = $db->loadObject();
      if ($db->getAffectedRows() == 0) {
        JFactory::getApplication()->enqueueMessage('Zutritt nur für Bewohner und Gewerbe der Giesserei.');
        return false;
      }
      
      return true;
    }
  }
  
  /**
   * Fügt das Stylesheet dieser Komponente zum Dokument hinzu.
   */
  public static function addComponentStylesheet() {
    ZeitbankFrontendHelper::addStylesheet('giesserei_default.css');
  }
  
  /**
   * Kürzt den übergebenen Text, wenn erforderlich.
   */
  public static function cropText($text, $maxLength) {
    $result = $text;
    if(strlen($text) > $maxLength) {
      $result = substr($text, 0, $maxLength)."...";
    }
    return $result;
  }
  
  /**
   * Liefert den Javascript-Code, welcher das Header-Image ausblendet.
   */
  public static function getScriptToHideHeaderImage() {
    return '<!-- Header-Images ausblenden -->'
        . '<script type="text/javascript">'
        . 'document.getElementById("header-image").style.display = "none";'
        . '</script>';
  }
  
  /**
   * Liefert ein Datum, welches $days Tage in der Zukunft liegt.
   */
  public static function getMySqlDateInFuture($days) {
    $date = new DateTime();
    $date->add(new DateInterval('P'.$days.'D'));
    return $date->format('Y-m-d');
  }
  
  /**
   * Liefert ein Datum, welches $days Tage in der Zukunft liegt.
   */
  public static function getViewDateInFuture($days) {
    $date = new DateTime();
    $date->add(new DateInterval('P'.$days.'D'));
    return $date->format('d.m.Y');
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  private static function addStylesheet($stylesheetName) {
    $doc = JFactory::getDocument();
    $base = JURI::base(true);
    $doc->addStyleSheet($base . '/components/com_zeitbank/template/' . $stylesheetName);
  }

}