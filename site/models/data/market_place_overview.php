<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Enthält die Angebote auf der Übersichtsseite.
 * 
 * @author Steffen Förster
 */
class MarketPlaceOverview extends JObject {
  
  /**
   * Array mit den eigenen Angeboten des Mitglieds.
   * 
   * @var array
   */
  public $meineAngebote = array();
  
  public $meineAngeboteTotal = 0;
  
  /**
   * Array mit den Angeboten zum Beziehen von Stunden.
   * 
   * @var array
   */
  public $angeboteBeziehen = array();
  
  public $angeboteBeziehenTotal = 0;
  
  /**
   * Array mit den Angeboten zum Ausgeben von Stunden.
   *
   * @var array
   */
  public $angeboteGeben = array();
  
  public $angeboteGebenTotal = 0;
  
  /**
   * Konstruktor
   */
  public function __construct() {
  }
  
}