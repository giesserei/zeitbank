<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Enthält die Details zu einem Angebot.
 * 
 * @author Steffen Förster
 */
class MarketPlaceDetails extends JObject {
  
  public $item = null;
  
  public $ansprechpartner = null;
  
  /**
   * Konstruktor
   */
  public function __construct() {
  }
  
}