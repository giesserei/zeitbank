<?php

/**
 * Liefert Regeln des geltenden Eigenleistungsreglements.
 */
interface Rules
{

    /**
     * Liefert das Stundensoll für einen Bewohner.
     */
    public function getStundenSollBewohner();

    /**
     * Liefert das Stundensoll für das Gewerbe (pro qm).
     */
    public function getStundenSollGewerbe();

    /**
     * Liefert die Stunden, die ein Bewohner trotz Dispension mind. im Jahr leisten muss.
     */
    public function getStundenSollMinBewohner();

    /**
     * Liefert die Stunden, die ein Bewohner in Ausbildung im Jahr leisten muss.
     */
    public function getStundenSollAusbildung();

    /**
     * Liefert die Höhe der Ersatzabgabe.
     */
    public function getErsatzabgabe();

}