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
    $query = "SELECT userid, vorname, nachname
              FROM #__mgh_aktiv_mitglied
              WHERE (vorname LIKE '%".$searchMySql."%' OR nachname LIKE '%".$searchMySql."%')
                AND typ != 5
                AND userid != ".$user->id;
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
}