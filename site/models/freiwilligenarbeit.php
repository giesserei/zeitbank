<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

JLoader::register('ZeitbankModelUpdJournalBase', JPATH_COMPONENT . '/models/upd_journal_base.php');

/**
 * Model zum Erstellen und Bearbeiten eines Antrags für Freiwilligenarbeit.
 */
class ZeitbankModelFreiwilligenarbeit extends ZeitbankModelUpdJournalBase
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Liefert das Zeitkonto für die Freiwilligenarbeit.
     */
    public function getZeitkonto()
    {
        $query = "SELECT k.user_id
              FROM #__mgh_zb_kategorie k
              WHERE k.id = " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG;
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Liefert die Liste mit den Arbeiten.
     *
     * Die Liste ist eine geschachtelte Liste von Arrays. In der ersten Dimension sind die Arbeitskategorien gelistet.
     * in der zweiten Dimension sind die zugehörigen Arbeiten gelistet.
     */
    public function getArbeitsgattungen()
    {
        // Zunächst die Arbeitskategorie selektieren
        $query = "SELECT k.*
              FROM #__mgh_zb_kategorie as k
              WHERE k.id = " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG . "
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
                $groupItems[$arb->id] = $arb->kurztext;
            }
            $liste[$kat->bezeichnung]['items'] = $groupItems;
        }

        return $liste;
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_zeitbank.freiwilligenarbeit', 'freiwilligenarbeit', array(
            'control' => 'jform',
            'load_data' => $loadData
        ));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Prüft, ob die Eingaben korrekt sind.
     *
     * Validierungsmeldungen werden im Model gespeichert.
     *
     * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
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
            $valid &= $this->validateMinuten($validateResult['minuten']);
        }
        if ((bool)$valid) {
            $valid &= $this->validateDatumAntrag($validateResult['datum_antrag']);
        }

        if (!(bool)$valid) {
            return false;
        }
        return $validateResult;
    }

    // -------------------------------------------------------------------------
    // protected section
    // -------------------------------------------------------------------------

    /**
     * Im Falle einer fehlgeschlagenen Validierung werden die Eingabe-Daten aus der Session geholt.
     *
     * @see JModelForm::loadFormData()
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_DATA, array());

        if (empty($data)) {
            $data = $this->getItem();
        } else {
            // ID im State setzen, damit diese von der View ausgelesen werden kann
            $this->state->set($this->getName() . '.id', $data['id']);
        }

        return $data;
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Die gewählte Arbeitskategorie muss zur Kategorie Freiwilligenarbeit gehören.
     */
    private function validateArbeit($arbeitId)
    {
        if (empty($arbeitId) || $arbeitId <= 0) {
            JFactory::getApplication()->enqueueMessage(
                'Bitte eine Arbeitskategorie auswählen.', 'warning');
            return false;
        }

        $query = "SELECT count(*)
              FROM #__mgh_zb_arbeit
              WHERE id = " . mysql_real_escape_string($arbeitId) . "
                AND aktiviert = 1
                AND kategorie_id = " . ZeitbankConst::KATEGORIE_ID_FREIWILLIG;
        $this->db->setQuery($query);
        $count = $this->db->loadResult();

        if ($count == 0) {
            JFactory::getApplication()->enqueueMessage(
                'Die Arbeitskategorie ist nicht zulässig.', 'warning');
            return false;
        }

        return true;
    }

    private function validateMinuten($minuten)
    {
        $minutenToValidate = $minuten;

        // leere Eingaben -> 0
        if (!isset($minutenToValidate) || ZeitbankFrontendHelper::isBlank($minutenToValidate)) {
            $minutenToValidate = 0;
        }

        // Nur Zahlen sind zulässig
        if (!is_numeric($minutenToValidate)) {
            JFactory::getApplication()->enqueueMessage(
                'Im Feld Minuten sind nur Zahlen zulässig.', 'warning');
            return false;
        }

        if ($minutenToValidate <= 0) {
            JFactory::getApplication()->enqueueMessage(
                'Die Anzahl der Minuten muss grösser 0 sein.', 'warning');
            return false;
        }

        return true;
    }

}