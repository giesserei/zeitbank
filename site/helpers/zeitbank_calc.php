<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('Rules', JPATH_COMPONENT . '/helpers/rules.php');
JLoader::register('Rules2018', JPATH_COMPONENT . '/helpers/rules_2018.php');

/**
 * Klasse zum Berechnen diverser Ergebnisse in der Zeitbank.
 */
class ZeitbankCalc
{

    private static $rules;

    public static function getSollBasis()
    {
        $rules = self::getRules();

        return $rules->getStundenSollBewohner();
    }

    /**
     * Liefert den Saldo (Minuten) des laufenden Jahres für das übergebene Mitglied.
     *
     * @param $userId int
     * @return int
     */
    public static function getSaldo($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend
                               WHERE gutschrift_userid = " . $userId . "), 0) -
                     COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend
                               WHERE belastung_userid = " . $userId . "), 0)";
        $db->setQuery($query);
        $saldo = $db->loadResult();
        $saldoInt = intval($saldo);
        return $saldoInt;
    }

    /**
     * Liefert den Saldo (Minuten) des Vorjahres für das übergebene Mitglied.
     *
     * @param $userId int
     * @return int
     */
    public static function getSaldoVorjahr($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_vorjahr
                               WHERE gutschrift_userid = " . $userId . "), 0) -
                     COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_vorjahr
                               WHERE belastung_userid = " . $userId . "), 0)";
        $db->setQuery($query);
        $saldo = $db->loadResult();
        $saldoInt = intval($saldo);
        return $saldoInt;
    }

    /**
     * Liefert den Saldo der Freiwilligenarbeit des laufenden Jahres für das übergebene Mitglied.
     *
     * @param $userId int
     * @return int
     */
    public static function getSaldoFreiwilligenarbeit($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend_inkl_freiw
                               WHERE gutschrift_userid = " . $userId . "
                                 AND arbeit_id IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1)), 0) -
                     COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_laufend_inkl_freiw
                               WHERE belastung_userid = " . $userId . "
                                 AND arbeit_id IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1)), 0)";
        $db->setQuery($query);
        $saldo = $db->loadResult();
        $saldoInt = intval($saldo);
        return $saldoInt;
    }

    /**
     * Liefert den Saldo der Freiwilligenarbeit des  Vorjahres für das übergebene Mitglied.
     *
     * @param $userId int
     * @return int
     */
    public static function getSaldoFreiwilligenarbeitVorjahr($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_vorjahr_inkl_freiw
                               WHERE gutschrift_userid = " . $userId . "
                                 AND arbeit_id IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1)), 0) -
                     COALESCE((SELECT SUM(minuten) FROM #__mgh_zb_journal_quittiert_vorjahr_inkl_freiw
                               WHERE belastung_userid = " . $userId . "
                                 AND arbeit_id IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1)), 0)";
        $db->setQuery($query);
        $saldo = $db->loadResult();
        $saldoInt = intval($saldo);
        return $saldoInt;
    }

    public static function getDebugValue($userId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT einzug, dispension_grad, zb_freistellung, typ, zb_ausbildung_bis
              FROM #__mgh_mitglied
              WHERE userid = " . $userId;
        $db->setQuery($query);
        $props = $db->loadObject();

        return self::computeMonateAusbildung(12, $props->zb_ausbildung_bis, false);

        //return $props->zb_ausbildung_bis;
    }

    /**
     * Berechnet das persönliche Soll an Eigenleistungen (Minuten) für das übergebene Mitglied.
     * Mit dem Parameter $inklDispensation kann definiert werden, ob eine Dispensation berücksichtigt werden soll.
     *
     * Vorbedingungen:
     * - Mitglied ist ein Bewohner
     * - Einzugsdatum ist in der DB erfasst
     *
     * @param $userId int
     * @param $inklDispensation boolean
     * @param $vorjahr boolean
     * @return int
     */
    public static function getSollBewohner($userId, $inklDispensation = true, $vorjahr = false)
    {
        $db = JFactory::getDBO();
        $query = "SELECT einzug, dispension_grad, zb_freistellung, typ, zb_ausbildung_bis
              FROM #__mgh_mitglied
              WHERE userid = " . $userId;
        $db->setQuery($query);
        $props = $db->loadObject();

        // Vorbedingung prüfen
        if ($props->typ != 1 || !!$props->einzug) {
            return 0;
        }

        $monate = self::computeMonate($props->einzug, $props->zb_freistellung, $vorjahr);

        // Ausbildung berücksichtigen
        $monateAusbildung = 0.0;
        if ($props->zb_ausbildung_bis) {
            $monateAusbildung = self::computeMonateAusbildung($monate, $props->zb_ausbildung_bis, $vorjahr);
        }
        $monateOhneAusbildung = $monate - $monateAusbildung;

        $rules = self::getRules();

        $stundenSoll = $monateOhneAusbildung * ($rules->getStundenSollBewohner() / 12)
            + $monateAusbildung * ($rules->getStundenSollAusbildung() / 12);

        // Dispensionsgrad berücksichtigen
        if ($inklDispensation && $props->dispension_grad > 0) {
            $stundenSollMin = $monate * ($rules->getStundenSollMinBewohner() / 12);
            $stundenSollReduziert = ($stundenSoll * (1 - ($props->dispension_grad / 100)));

            // Wenn die reduzierten Stunden das Minimum unterschreiten -> gilt das Minimum
            $stundenSoll = max($stundenSollMin, $stundenSollReduziert);
        }

        return $stundenSoll * 60;
    }

    /**
     * Liefert true, wenn eine Buchung für das vergangene Jahr erlaubt ist.
     * Aktuelles Datum muss zwischen dem 01.01. und dem 05.01. liegen.
     *
     * @return boolean
     */
    public static function isLastYearAllowed()
    {
        $dateLower = new DateTime(date('Y') . '-01-01');
        $dateUpper = new DateTime(date('Y') . '-01-05');
        $today = new DateTime(date('Y-m-d'));

        return ($today >= $dateLower && $today <= $dateUpper);
    }

    /**
     * Liefert true, wenn eine Buchung für das aktuelle Jahr erlaubt ist.
     * Aktuelles Datum muss >= dem 11.01. sein.
     */
    public static function isCurrentYearAllowed()
    {
        $dateLower = new DateTime(date('Y') . '-01-11');
        $today = new DateTime(date('Y-m-d'));

        return ($today >= $dateLower);
    }

    /**
     * Liefert true, wenn keine Buchungen durchgeführt werden können.
     * Während der Abschlussarbeiten ist die Zeitbank gesperrt: 06.01. bis 10.01.
     *
     * @return boolean
     */
    public static function isBuchungGesperrt()
    {
        $dateLower = new DateTime(date('Y') . '-01-06');
        $dateUpper = new DateTime(date('Y') . '-01-10');
        $today = new DateTime(date('Y-m-d'));

        return ($today >= $dateLower && $today <= $dateUpper);
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Berechnet die Anzahl der Monate, für die ein Mitglied Stunden zu leisten hat.
     * Gerundet wird auf einen halben Monat.
     */
    private static function computeMonate($einzug, $freistellung, $vorjahr = false)
    {
        $dateEinzug = new DateTime($einzug);
        $offset = $vorjahr ? 1 : 0;
        $dateYearStart = new DateTime(intval(date('Y')) - $offset . '-01-01');
        $dateYearEnd = new DateTime(intval(date('Y')) - $offset . '-12-31');

        $monate = 12.0;

        // Einzug erst im nächsten Jahr -> keine Leistungspflicht
        if ($dateEinzug > $dateYearEnd) {
            $monate = 0.0;
        } else if ($dateEinzug > $dateYearStart) {
            $diff = $dateEinzug->diff($dateYearStart, true);
            $monate -= $diff->m;

            $day = date('d', $dateEinzug->getTimestamp());

            // Einzug zwischen dem 02. und dem 15. -> 0.5 Monate Abzug
            if ($diff->d > 0 && $day <= 15) {
                $monate -= 0.5;
            } // Einzug zwischen dem 16. und dem 30./31. -> ganzen Monat Abzug
            else if ($diff->d > 0 && $day > 15) {
                $monate -= 1;
            }
        }

        // Freistellung abziehen
        $monate -= $freistellung;

        // keine negativen Werte zulässig
        if ($monate < 0) {
            $monate = 0;
        }

        return $monate;
    }

    /**
     * Berechnet die Anzahl der Monate, für die ein Mitglied nur die reduzierten Stunden wegen
     * Erstausbildung zu leisten hat.
     */
    private static function computeMonateAusbildung($monate, $ausbildungBis, $vorjahr = false)
    {
        $dateAusbildungBis = new DateTime($ausbildungBis);
        $offset = $vorjahr ? 1 : 0;
        $dateYearStart = new DateTime(intval(date('Y')) - $offset . '-01-01');
        $dateYearEnd = new DateTime(intval(date('Y')) - $offset . '-12-31');

        $monateAusbildung = 0.0;

        // Ausbildungsende ist später als das aktuelle Jahr
        if ($dateAusbildungBis > $dateYearStart && $dateAusbildungBis > $dateYearEnd) {
            $monateAusbildung = $monate;
        // Ausbildungsende liegt im aktuellen Jahr
        } else if ($dateAusbildungBis > $dateYearStart && $dateAusbildungBis <= $dateYearEnd) {
            $diff = $dateYearEnd->diff($dateAusbildungBis, true);
            $monateAusbildung = $monate - $diff->m;

//            $day = date('d', $dateAusbildungBis->getTimestamp());

            // Einen Monat hinzuzählen, da z.B. 31.01. - 01.01. = 0 Monate
//            if ($diff->d > 0 && $day > 2) {
//                $monateAusbildung += 1;
//            }
        }

        // keine negativen Werte zulässig
        if ($monateAusbildung < 0) {
            $monateAusbildung = 0;
        }

        return $monateAusbildung;
    }

    private static function getRules()
    {
        if (is_null(self::$rules)) {
            self::$rules = new Rules2018();
        }
        return self::$rules;
    }

}
