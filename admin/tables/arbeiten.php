<?php
/*
 * Created on 28.12.2012
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableArbeiten extends JTable
{
    var $id = null;
    var $kurztext = null;
    var $beschreibung = null;
    var $jahressoll = null;
    var $kadenz = null;
    var $pauschale = null;
    var $kategorie_id = null;
    var $ordering = null;
    var $user_id = null;
    var $kommentar = null;

    // Hilfsvariablen
    var $_gebuchte_zeit = 0;

    function TableArbeiten(&$db)
    {
        parent::__construct('#__mgh_zb_arbeit', 'id', $db);
    }
}

?>
