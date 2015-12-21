<?php
defined('_JEXEC') or die('Restricted access');

class ZeitbankTableKategorie extends JTable
{
    var $id = null;
    var $ordering = null;
    var $bezeichnung = null;
    var $gesamtbudget = null;
    var $nachtrag = null;
    var $user_id = null;
    var $admin_id = null;

    public function ZeitbankTableKategorie(&$db)
    {
        parent::__construct('#__mgh_zb_kategorie', 'id', $db);
    }
}

