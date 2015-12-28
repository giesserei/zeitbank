<?php

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . '/components/com_zeitbank/models/arbeit_func.php');
require_once(JPATH_BASE . '/components/com_zeitbank/models/zeitbank.php');

// Bewegt ein Amt in der Reihenfolge nach oben
function order_up($amt_id, $kat_id)
{
    $db = JFactory::getDBO();
    $query = "SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='" . intval($kat_id) . "' ORDER BY ordering";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    $prev = 0;

    if ($db->getAffectedRows() > 0):
        foreach ($rows as $row):
            if ($row->id == $amt_id AND is_object($prev)):
                $query = "UPDATE #__mgh_zb_arbeit SET ordering='" . $row->ordering . "' WHERE id='" . $prev->id . "'";
                $db->setQuery($query);
                if ($db->Query()):
                    $query = "UPDATE #__mgh_zb_arbeit SET ordering='" . $prev->ordering . "' WHERE id='" . $row->id . "'";
                    $db->setQuery($query);
                    $db->Query();
                endif;
            endif;
            $prev = $row;
        endforeach;
    endif;
} // order up

// Bewegt ein Amt in der Reihenfolge nach unten
function order_down($amt_id, $kat_id)
{
    $db = JFactory::getDBO();
    $query = "SELECT * FROM #__mgh_zb_arbeit WHERE kategorie_id='" . intval($kat_id) . "' ORDER BY ordering DESC";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    $prev = 0;

    if ($db->getAffectedRows() > 0):
        foreach ($rows as $row):
            if ($row->id == $amt_id AND is_object($prev)):
                $query = "UPDATE #__mgh_zb_arbeit SET ordering='" . $row->ordering . "' WHERE id='" . $prev->id . "'";
                $db->setQuery($query);
                if ($db->Query()):
                    $query = "UPDATE #__mgh_zb_arbeit SET ordering='" . $prev->ordering . "' WHERE id='" . $row->id . "'";
                    $db->setQuery($query);
                    $db->Query();
                endif;
            endif;
            $prev = $row;
        endforeach;
    endif;
} // order down
