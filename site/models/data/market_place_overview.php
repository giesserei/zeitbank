<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Enthält die Angebote auf der Übersichtsseite.
 *
 * @author Steffen Förster
 */
class MarketPlaceOverview
{

    /**
     * Array mit den eigenen Angeboten des Mitglieds.
     *
     * @var array
     */
    public $meineAngebote = array();

    public $meineAngeboteTotal = 0;

    /**
     * Array mit den Arbeitsangeboten.
     *
     * @var array
     */
    public $angeboteArbeiten = array();

    public $angeboteArbeitenTotal = 0;

    /**
     * Array mit den Tauschangeboten.
     *
     * @var array
     */
    public $angeboteTauschen = array();

    public $angeboteTauschenTotal = 0;

    /**
     * Konstruktor
     */
    public function __construct()
    {
    }

}