<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Modellklasse für Darstellung aller Arbeiten zu einer Kategorie.
 */
class ZeitbankModelArbeiten extends JModelLegacy
{

    private $db;

    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->db = JFactory::getDBO();
        $this->user = JFactory::getUser();
    }

    /**
     * Liefert alle Arbeiten der übergebenen Kategorie.
     *
     * @param int $katId
     * @return mixed
     */
    public function getArbeiten($katId)
    {
        $db = JFactory::getDBO();
        $query =
            "SELECT a.*, u.name,
             (COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend
                               WHERE arbeit_id = a.id), 0)) ist_laufend,
             (COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_vorjahr
                               WHERE arbeit_id = a.id), 0)) ist_vorjahr
             FROM #__mgh_zb_arbeit a
             LEFT OUTER JOIN #__users u ON a.admin_id = u.id
             WHERE a.kategorie_id=" . $katId ."
             ORDER BY a.ordering";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

}