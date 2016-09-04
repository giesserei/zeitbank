<?php
defined('_JEXEC') or die;

JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

/**
 * Helperklasse.
 */
class ZeitbankFrontendHelper
{

    /**
     * Fügt das Stylesheet dieser Komponente zum Dokument hinzu.
     */
    public static function addComponentStylesheet()
    {
        self::addStylesheet('giesserei_default.css');
    }

    /**
     * Kürzt den übergebenen Text, wenn erforderlich.
     */
    public static function cropText($text, $maxLength, $addDots = true)
    {
        $result = $text;
        if (!empty($text) && strlen($text) > $maxLength) {
            $result = substr($text, 0, $maxLength) . ($addDots ? "..." : "");
        }
        return $result;
    }

    /**
     * Liefert den Javascript-Code, welcher das Header-Image ausblendet.
     */
    public static function getScriptToHideHeaderImage()
    {
        return '<!-- Header-Images ausblenden -->'
        . '<script type="text/javascript">'
        . 'document.getElementById("header-image").style.display = "none";'
        . '</script>';
    }

    /**
     * Liefert ein Datum, welches $days Tage in der Zukunft liegt.
     */
    public static function getMySqlDateInFuture($days)
    {
        $date = new DateTime();
        $date->add(new DateInterval('P' . $days . 'D'));
        return $date->format('Y-m-d');
    }

    /**
     * Liefert ein Datum, welches $days Tage in der Zukunft liegt.
     */
    public static function getViewDateInFuture($days)
    {
        $date = new DateTime();
        $date->add(new DateInterval('P' . $days . 'D'));
        return $date->format('d.m.Y');
    }

    /**
     * Erstellt mit den übergebenen Parametern einen Mailto-Link. Wenn die Mail-Adresse mit "kein.mail" beginnt,
     * wird einfach nur der Name (ohne Link) zurückgeliefert,
     */
    public static function getEmailLink($vorname, $nachname, $email, $subject)
    {
        $name = $vorname . " " . $nachname;
        $link = $name;

        if (substr($email, 0, 11) != "kein.email.") {
            $link = '<a href="mailto:' . $email . '?subject=' . $subject . '&body=Liebe/Lieber ' . $vorname . '">' . $name . '</a>';
        }

        return $link;
    }

    /**
     * Liefert true, wenn der übergebene String-Wert NULL ist oder eine leere Zeichenkette ist.
     * Alle Whitespaces werden für den Test entfernt.
     *
     * @param $value string
     * @return boolean
     */
    public static function isBlank($value)
    {
        if (empty($value)) {
            return true;
        }
        $trimedValue = trim($value);
        return empty($trimedValue);
    }

    /**
     * Liefert eine Zeitangabe in der Form Stunden:Minuten.
     *
     * @param $time_in_minutes
     * @return string
     *
     */
    public static function formatTime($time_in_minutes)
    {
        $time_in_minutes = round($time_in_minutes);

        // Negative Werte gesondert behandeln
        if ($time_in_minutes >= 0) {
            $hours = floor($time_in_minutes / 60);
            $minutes = $time_in_minutes - $hours * 60;
        } else {
            $hours = ceil($time_in_minutes / 60);
            $minutes = $time_in_minutes - $hours * 60;
        }

        // Minuszeichen bei den Minuten wegschneiden
        $minutes = ltrim($minutes, '-');
        if (strlen($minutes) <= 1) {
            $minutes = "0" . $minutes;
        }
        return ($hours . ":" . $minutes);
    }

    /**
     * Liefert einen Link zur Anzeige der Details einer Buchung in einem JS-Window.
     * Der Parameter tmpl=component sorgt dafür, dass nur die View ohne Template angezeigt wird.
     */
    public static function getLinkBuchung($id, $text)
    {
        return '<a class="modal"
             href="index.php?option=com_zeitbank&tmpl=component&view=buchung&id=' . $id . '"
             rel="{handler: \'iframe\', size: {x: 550, y: 450}}"><strong>' . $text . '</strong></a>';
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private static function addStylesheet($stylesheetName)
    {
        $doc = JFactory::getDocument();
        $base = JURI::base(true);
        $doc->addStyleSheet($base . '/components/com_zeitbank/template/' . $stylesheetName);
    }

}