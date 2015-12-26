<?php

/* Prüft, ob User für Zeitbank gesperrt ist 
 * 24.7.2012 jal
 * 
 * */

defined('_JEXEC') or die('Restricted access');

// ID des Zeitbankmenüs
define("MENUITEM", "202");


if (!defined('ZB_BITTE_ANMELDEN')) define('ZB_BITTE_ANMELDEN', '<h1>Zeitbank: Benutzerprüfung</h1><p>Die Zeitbank ist nur für registrierte Mitglieder
nutzbar. Bitte melde dich mit deinem <strong>persönlichen</strong> Login an.<br>Arbeitsgruppen-, Vorstands- oder Vereinslogin funktionieren
<strong>nicht</strong>!<br /><br /><a href="/component/users/?view=login">Hier gehts zur Anmeldemaske</a></p>');


function check_user()
{
    $db = JFactory::getDBO();
    $user = JFactory::getUser();

    $query = "SELECT * FROM #__mgh_zb_gesperrte_user WHERE userid='" . $user->id . "'";
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    if ($db->getAffectedRows() <= 0 AND $user->id > 0):
        return (true);
    else:
        return (false);
    endif;

} // check_user()

// Ist ein User ein Ämtli-Administrator?
function check_arbeit_admin($kategorie)
{
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $kategorie = strval($kategorie);

    if ($kategorie > 0):
        $query = "SELECT * FROM #__mgh_zb_x_kat_arbeitadmin WHERE user_id='" . $user->id . "' AND kat_id='" . $kategorie . "'";
    else:
        $query = "SELECT * FROM #__mgh_zb_x_kat_arbeitadmin WHERE user_id='" . $user->id . "'";
    endif;

    $db->setQuery($query);
    $rows = $db->loadObjectList();

    if ($db->getAffectedRows() > 0 AND $user->id > 0):
        return (true);
    else:
        return (false);
    endif;

}

// Ist ein User ein Kategorie-Administrator?
function check_kat_admin($kategorie)
{
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $kategorie = strval($kategorie);

    if ($kategorie > 0):
        $query = "SELECT * FROM #__mgh_zb_kategorie WHERE admin_id='" . $user->id . "' AND id='" . $kategorie . "'";
    else:
        $query = "SELECT * FROM #__mgh_zb_kategorie WHERE admin_id='" . $user->id . "'";
    endif;

    $db->setQuery($query);
    $rows = $db->loadObjectList();

    if ($db->getAffectedRows() > 0 AND $user->id > 0):
        return ($rows[0]->id);
    else:
        return (false);
    endif;

}