<?php
defined('_JEXEC') or die('Restricted access');

class ZeitbankTableArbeitAdmin extends JTable
{
    var $id = null;
    var $user_id = null;
    var $kat_id = null;

    public function __construct(&$db)
    {
        parent::__construct('#__mgh_zb_x_kat_arbeitadmin', 'id', $db);
    }
}
