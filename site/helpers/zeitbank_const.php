<?php
defined('_JEXEC') or die;

/**
 * Klasse für verwendete Konstanten.
 */
class ZeitbankConst
{

    /**
     * Unter diesem Key werden die Formulardaten des Marktplatzes gespeichert werden, wenn eine Validierung fehlschlägt.
     */
    const SESSION_KEY_MARKET_PLACE_DATA = 'com_zeitbank.market.place.data';

    /**
     * Unter diesem Key wird der Typ abgelegt, welcher neuangelegt werden soll.
     */
    const SESSION_KEY_MARKET_PLACE_ENTRY_ART = 'com_zeitbank.market.place.entry.art';

    /**
     * Unter diesem Key wird die Menü-Id der Zeitbank gespeichert.
     */
    const SESSION_KEY_ZEITBANK_MENU_ID = 'com_zeitbank.market.place.menuid';

    /**
     * Unter diesem Key werden die Formulardaten der Zeitbank gespeichert werden, wenn eine Validierung fehlschlägt.
     */
    const SESSION_KEY_ZEITBANK_DATA = 'com_zeitbank.data';

    /**
     * ID der Arbeit "Stundengeschenk".
     */
    const ARBEIT_ID_STUNDENTAUSCH = 1;

    /**
     * ID der Arbeit "Stundengeschenk".
     */
    const ARBEIT_ID_STUNDENGESCHENK = 3;

    /**
     * ID der Kategorie Freiwilligenarbeit.
     */
    const KATEGORIE_ID_FREIWILLIG = -1;

    /**
     * ID der Kategorie Privater Stundentausch.
     */
    const KATEGORIE_ID_STUNDENTAUSCH = 1;

    /**
     * ID der Kategorie Stundengeschenk.
     */
    const KATEGORIE_ID_STUNDENGESCHENK = 10;

}
