<?php
/*
 * Created on 28.12.2012
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableKategorien extends JTable
{
    var $id = null;
    var $ordering = null;
    var $bezeichnung = null;

    function __construct(&$db)
    {
        parent::__construct('#__mgh_zb_kategorie', 'id', $db);
    }
}

?>
