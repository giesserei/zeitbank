<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Klasse stellt diverse gemeinsame Funktionalitäten für die Verbuchung von Stunden zur Verfügung.
 */
class BuchungHelper
{

    /**
     * Liefert alle aktiven Bewohner und das Gewerbe, welche mit dem Like-Operator gefunden werden.
     *
     * @param string    $search
     * @param boolean   $includeCurrentUser
     * @return string
     */
    public static function getEmpfaengerLike($search, $includeCurrentUser = false)
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $query =
            "SELECT m.userid, m.vorname, m.nachname
             FROM #__mgh_mitglied m
             WHERE m.typ IN (1,2,7) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
               AND (m.vorname LIKE '%" . $db->quote($search) . "%' OR m.nachname LIKE '%" . $db->quote($search) . "%')";
        if (!$includeCurrentUser)
        {
            $query = $query . " AND m.userid != " . $user->id;
        }

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Liefert true, wenn der übergebene User das Zeitkonto Stundenfonds ist.
     */
    public static function isStundenfonds($userid)
    {
        $db = JFactory::getDBO();
        $query = "SELECT typ
              FROM #__mgh_mitglied
              WHERE userid = " . $userid;
        $db->setQuery($query);
        $props = $db->loadObject();
        return $props->typ == 7;
    }

    /**
     * Liefert true, wenn der übergebene User das Zeitkonto eines Gewerbes ist.
     */
    public static function isGewerbe($userid)
    {
        $db = JFactory::getDBO();
        $query = "SELECT typ
              FROM #__mgh_mitglied
              WHERE userid = " . $userid;
        $db->setQuery($query);
        $props = $db->loadObject();
        return $props->typ == 2;
    }

    /**
     * Liefert die User-Id des Stundenfonds.
     */
    public static function getStundenfondsUserId()
    {
        $db = JFactory::getDBO();
        $query = "SELECT userid
              FROM #__mgh_mitglied
              WHERE typ = 7";
        $db->setQuery($query);
        $props = $db->loadObject();
        return $props->userid;
    }

}