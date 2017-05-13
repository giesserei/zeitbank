<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Erstellen und Bearbeiten eines Antrags für Eigenleistungen.
 */
class ZeitbankModelEigenleistungen extends ZeitbankModelUpdJournalBase
{

    public function getForm($data = array(), $loadData = true)
    {
        return $this->createForm('com_zeitbank.eigenleistungen', 'eigenleistungen', $loadData);
    }

    /**
     * Prüft, ob die Eingaben korrekt sind. Validierungsmeldungen werden im Model gespeichert.
     *
     * @return mixed Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
     *
     * @inheritdoc
     */
    public function validate($form, $data, $group = NULL)
    {
        $validateResult = parent::validate($form, $data, $group);
        if ($validateResult === false) {
            return false;
        }

        $valid = 1;
        $valid &= $this->validateArbeit($validateResult['arbeit_id']);

        if ((bool)$valid) {
            $valid &= $this->validateMinuten($validateResult['minuten'], $validateResult['arbeit_id']);
        }
        if ((bool)$valid) {
            $valid &= $this->validateDatumAntrag($validateResult['datum_antrag']);
        }

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    /**
     * Liefert bei einer Arbeitskategorie mit einer Pauschalen die Pauschale; ansonsten die beantragten Minuten.
     *
     * @param $minuten int beantragte Minuten
     * @param $arbeitId int Arbeitskategorie
     *
     * @return int siehe Beschreibung
     */
    public function getMinuten($minuten, $arbeitId)
    {
        $query = "SELECT pauschale
              FROM #__mgh_zb_arbeit a
              WHERE a.id = " . $this->db->quote($arbeitId);
        $this->db->setQuery($query);
        $pauschale = $this->db->loadResult();

        return empty($pauschale) ? $minuten : $pauschale;
    }

    /**
     * Liefert das Zeitkonto für die übergebene Arbeitskategorie.
     *
     * @param $arbeitId int Arbeitskategorie
     *
     * @return int User-Id des Zeitkontos
     */
    public function getZeitkonto($arbeitId)
    {
        $query = "SELECT k.user_id
              FROM #__mgh_zb_arbeit a, #__mgh_zb_kategorie k
              WHERE a.id = " . $this->db->quote($arbeitId) . "
                AND a.kategorie_id = k.id";
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Liefert die Liste mit den Eigenleistungs-Arbeiten.
     *
     * Die Liste ist eine geschachtelte Liste von Arrays. In der ersten Dimension sind die Arbeitskategorien gelistet.
     * in der zweiten Dimension sind die zugehörigen Arbeiten gelistet.
     *
     * @return array siehe Beschreibung
     */
    public function getArbeitsgattungen()
    {
        // Zunächst alle relevanten Arbeitskategorien selektieren
        $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.id != " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG . "
                AND k.id NOT IN (
                  SELECT a.kategorie_id FROM #__mgh_zb_arbeit a
                  WHERE a.id IN (" . ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK . "," . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . ")
                )
              ORDER BY k.ordering";
        $this->db->setQuery($query);
        $kategorien = $this->db->loadObjectList();

        // Für jede Kategorie nun die Arbeiten laden
        $liste = array();

        // Hint hinzufügen
        $liste[""] = array();
        $hintItems = array();
        $hintItems[-1] = "---- Arbeitsgattung auswählen ----";
        $liste[""]['items'] = $hintItems;

        foreach ($kategorien as $kat) {
            $liste[$kat->bezeichnung] = array();

            $query = "SELECT a.*
                FROM #__mgh_zb_arbeit as a
                WHERE a.kategorie_id = " . $kat->id . "
                  AND a.aktiviert = '1'
                ORDER BY a.ordering";
            $this->db->setQuery($query);
            $arbeiten = $this->db->loadObjectList();

            $groupItems = array();
            foreach ($arbeiten as $arb) {
                $groupItems[$arb->id] = $arb->kurztext . (empty($arb->pauschale) ? "" : " (" . $arb->pauschale . " min.)");
            }
            $liste[$kat->bezeichnung]['items'] = $groupItems;
        }

        return $liste;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Die gewählte Arbeitskategorie darf nicht zur Kategorie Freiwilligenarbeit gehören.
     * Auch darf es sich nicht um ein Stundengeschenk oder einen Stundentausch handeln.
     *
     * @param $arbeitId int ID des Ämtli
     *
     * @return boolean
     */
    private function validateArbeit($arbeitId)
    {
        if (empty($arbeitId) || $arbeitId <= 0) {
            JFactory::getApplication()->enqueueMessage('Bitte eine Arbeitskategorie auswählen.', 'warning');
            return false;
        }

        $query = "SELECT count(*)
              FROM #__mgh_zb_arbeit
              WHERE id = " . $this->db->quote($arbeitId) . "
                AND aktiviert = 1
                AND kategorie_id != " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG . "
                AND id NOT IN (" . ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK . "," . ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH . ")";
        $this->db->setQuery($query);
        $count = $this->db->loadResult();

        if ($count == 0) {
            JFactory::getApplication()->enqueueMessage('Die Arbeitskategorie ist nicht zulässig.', 'warning');
            return false;
        }

        return true;
    }

    private function validateMinuten($minuten, $arbeitId)
    {
        $minutenToValidate = $minuten;

        // leere Eingaben -> 0
        if (!isset($minutenToValidate) || ZeitbankFrontendHelper::isBlank($minutenToValidate)) {
            $minutenToValidate = 0;
        }

        // Nur Zahlen sind zulässig
        if (!is_numeric($minutenToValidate)) {
            JFactory::getApplication()->enqueueMessage('Im Feld Minuten sind nur Zahlen zulässig.', 'warning');
            return false;
        }

        // Bei einer Arbeitskategorie mit einer Pauschale, die Pauschale holen
        $minutenToValidate = $this->getMinuten($minutenToValidate, $arbeitId);
        if ($minutenToValidate <= 0) {
            JFactory::getApplication()->enqueueMessage('Die Anzahl der Minuten muss grösser 0 sein.', 'warning');
            return false;
        }

        return true;
    }
}