<?php
/*
 * Created on 27.12.2010
 *

 */
defined('_JEXEC') or die('Restricted access');

class TableJournal extends JTable
{
    var $id = null;
    var $minuten = null;
    var $belastung_userid = null;
    var $gutschrift_userid = null;
    var $datum_antrag = null;
    var $datum_quittung = null;
    var $admin_del = null;
    var $arbeit_id = null;
    var $kommentar_antrag = null;
    var $kommentar_quittung = null;
    var $kommentar_ablehnung = null;
    var $abgelehnt = null;

    function __construct(&$db)
    {
        parent::__construct('#__mgh_zb_journal', 'id', $db);
    }
}

?>
