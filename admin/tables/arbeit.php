<?php

defined('_JEXEC') or die('Restricted access');

class ZeitbankTableArbeit extends JTable
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
    var $admin_id = null;
    var $kommentar = null;

    public function ZeitbankTableArbeit(&$db)
    {
        parent::__construct('#__mgh_zb_arbeit', 'id', $db);
    }
}
