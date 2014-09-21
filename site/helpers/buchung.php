<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Klasse stellt diverse gemeinsame Funktionalitäten für die Verbuchung von Stunden zur Verfügung.
 *
 * @author Steffen Förster
 */
class BuchungHelper {

  /**
   * Liefert alle aktiven Bewohner und das Gewerbe mit Ausnahme des angemeldeten Benutzers,
   * welche mit dem Like-Operator gefunden werden.
   */
  public static function getEmpfaengerLike($search) {
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $searchMySql = mysql_real_escape_string($search);
    $query = "SELECT m.userid, m.vorname, m.nachname
              FROM #__mgh_mitglied m
              WHERE m.typ IN (1,2,7) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
                AND (m.vorname LIKE '%".$searchMySql."%' OR m.nachname LIKE '%".$searchMySql."%')
                AND m.userid != ".$user->id;
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  /**
   * Liefert true, wenn der übergebene User das Zeitkonto Stundenfonds ist.
   */
  public static function isStundenfonds($userid) {
    $db = JFactory::getDBO();
    $query = "SELECT typ
              FROM #__mgh_mitglied
              WHERE userid = ".$userid;
    $db->setQuery($query);
    $props = $db->loadObject();
    return $props->typ == 7;
  }
  
  /**
   * Liefert die User-Id des Stundenfonds.
   */
  public static function getStundenfondsUserId() {
    $db = JFactory::getDBO();
    $query = "SELECT userid
              FROM #__mgh_mitglied
              WHERE typ = 7";
    $db->setQuery($query);
    $props = $db->loadObject();
    return $props->userid;
  }
  
}