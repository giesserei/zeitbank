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
  
  /**
   * Prüft ob der Benutzer angemeldet ist und ob der Benutzer Zugriff auf die Reports der Zeitbank hat.
   * 
   * TODO: Verwendung der ACL statt fester User-IDs.
   */
  public static function checkAuthReports() {
    $user = JFactory::getUser();
    if ($user->guest) {
      JFactory::getApplication()->enqueueMessage('Die Registrierung ist abgelaufen. Bitte neu anmelden.');
      return false;
    } 
    else {
      // Zugriff für Steffen
      if ($user->id != 134) {
        JFactory::getApplication()->enqueueMessage('Du bist nicht berechtigt.');
        return false;
      }
      
      return true;
    }
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
  
  /**
   * Fügt das Stylesheet dieser Komponente zum Dokument hinzu.
   */
  public static function addComponentStylesheet() {
    ZeitbankFrontendHelper::addStylesheet('giesserei_default.css');
  }
  
  /**
   * Kürzt den übergebenen Text, wenn erforderlich.
   */
  public static function cropText($text, $maxLength, $addDots = true) {
    $result = $text;
    if(strlen($text) > $maxLength) {
      $result = substr($text, 0, $maxLength).($addDots ? "..." : "");
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
  
  /**
   * Erstellt mit den übergebenen Parametern einen Mailto-Link. Wenn die Mail-Adresse mit "kein.mail" beginnt,
   * wird einfach nur der Name (ohne Link) zurückgeliefert,
   */
  public static function getEmailLink($vorname, $nachname, $email) {
      $name = $vorname . " " . $nachname;
      $link = $name;
    
      if (substr($email, 0, 11) != "kein.email.") {
        $link = '<a href="mailto:'.$email.'?subject=Marktplatz&body=Liebe/Lieber '.$vorname.'">'.$name.'</a>';
      }
      
      return $link;
  }
  
  /**
   * Liefert true, wenn der übergebene String-Wert NULL ist oder eine leere Zeichenkette ist. 
   * Alle Whitespaces werden für den Test entfernt.
   * 
   * @param string $value
   */
  public static function isBlank($value) {
    if (empty($value)) {
      return true;
    }
    $trimedValue = trim($value);
    return empty($trimedValue);
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