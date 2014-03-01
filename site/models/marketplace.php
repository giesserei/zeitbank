<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('MarketPlaceOverview', JPATH_COMPONENT . '/models/data/market_place_overview.php');
JLoader::register('MarketPlaceDetails', JPATH_COMPONENT . '/models/data/market_place_details.php');

jimport('joomla.application.component.model');

/**
 * Modellklasse für Darstellung des Marktplatzes.
 *
 * @author Steffen Förster
 */
class ZeitbankModelMarketPlace extends JModel {
  
  private $db;
  
  private $user;
  
  public function __construct() {
    parent::__construct();
    $this->db = JFactory::getDBO();
    $this->user = JFactory::getUser();
  }
  
  /**
   * Liefert die Daten für die Darstellung der Übersicht.
   *  
   * @return MarketPlaceOverview 
   */
  public function getOverview($limit = 5) {
    $overview = new MarketPlaceOverview();
    
    $this->addMeineAngebote($overview, $limit);
    $this->addAngeboteArbeiten($overview, $limit);
    $this->addAngeboteTauschen($overview, $limit);
    
    return $overview;
  }
  
  /**
   * Liefert die komplette Liste der Angebote des Mitglieds.
   *  
   * @return MarketPlaceOverview
   */
  public function getMeineAngebote() {
    $overview = new MarketPlaceOverview();
    $this->addMeineAngebote($overview, 0);
    return $overview;
  }
  
  /**
   * Liefert die komplette Liste der aktiven Arbeitsangebote.
   *
   * @return MarketPlaceOverview
   */
  public function getAngeboteArbeiten() {
    $overview = new MarketPlaceOverview();
    $this->addAngeboteArbeiten($overview, 0);
    return $overview;
  }
  
  /**
   * Liefert die komplette Liste der aktiven Tauschangebote.
   *
   * @return MarketPlaceOverview
   */
  public function getAngeboteTauschen() {
    $overview = new MarketPlaceOverview();
    $this->addAngeboteTauschen($overview, 0);
    return $overview;
  }
  
  /**
   * Liefert die Details zu einem Angebot einschliesslich der Kontaktdaten des Ansprechpartners.
   * 
   * @return MarketPlaceDetails
   */
  public function getDetails($id) {
    $details = new MarketPlaceDetails();
    
    $query = sprintf(
        "SELECT m.*, 
           (SELECT CONCAT(k.bezeichnung, ' / ', a.kurztext) 
            FROM #__mgh_zb_kategorie k 
              JOIN #__mgh_zb_arbeit a ON k.id = a.kategorie_id
            WHERE a.id = m.arbeit_id) AS konto
         FROM #__mgh_zb_market_place as m
		     WHERE m.id = %s", mysql_real_escape_string($id));
    $this->db->setQuery($query);
    $details->item = $this->db->loadObject();
    
    $query = "SELECT m.*, u.email 
              FROM #__mgh_mitglied m JOIN #__users u ON m.userid = u.id
              WHERE m.userid = " . $details->item->userid;
    $this->db->setQuery($query);
    $details->ansprechpartner = $this->db->loadObject();
    
    return $details;
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  private function addMeineAngebote(&$overview, $limit) {
    $query = "SELECT m.*, 
                CASE WHEN m.art = 1 THEN
                  (SELECT CONCAT(k.bezeichnung, ' / ', a.kurztext) 
                   FROM #__mgh_zb_kategorie k 
                   JOIN #__mgh_zb_arbeit a ON k.id = a.kategorie_id
                   WHERE a.id = m.arbeit_id) 
                ELSE '' END AS konto
              FROM #__mgh_zb_market_place m
		    	    WHERE m.userid=" . $this->user->id . "
		    	    ORDER BY m.erstellt DESC" .
		    	    ($limit > 0 ? " LIMIT " . $limit : "");
    $this->db->setQuery($query);
    $overview->meineAngebote = $this->db->loadObjectList();
    
    $query = 'SELECT count(*)
              FROM #__mgh_zb_market_place m
		    	    WHERE m.userid=' . $this->user->id;
    $this->db->setQuery($query);
    $overview->meineAngeboteTotal = $this->db->loadResult();
  }
  
  private function addAngeboteArbeiten(&$overview, $limit) {
    $query = "SELECT m.*, mgl.vorname, mgl.nachname, u.email,
                (SELECT CONCAT(k.bezeichnung, ' / ', a.kurztext) 
                   FROM #__mgh_zb_kategorie k 
                     JOIN #__mgh_zb_arbeit a ON k.id = a.kategorie_id
                   WHERE a.id = m.arbeit_id) AS konto
              FROM #__mgh_zb_market_place m
                JOIN #__mgh_mitglied mgl ON m.userid = mgl.userid
                JOIN #__users u ON m.userid = u.id
		    	    WHERE m.art = 1 
                AND m.ablauf > NOW() 
                AND m.status = 1 
              ORDER BY m.erstellt DESC" .
              ($limit > 0 ? ' LIMIT ' . $limit : '');
    $this->db->setQuery($query);
    $overview->angeboteArbeiten = $this->db->loadObjectList();
    
    $query = "SELECT count(*)
              FROM #__mgh_zb_market_place as m
                JOIN #__mgh_mitglied mgl ON m.userid = mgl.userid
                JOIN #__users u ON m.userid = u.id
		    	    WHERE m.art = 1
                AND m.ablauf > NOW()
                AND m.status = 1";
    $this->db->setQuery($query);
    $overview->angeboteArbeitenTotal = $this->db->loadResult();
  }
  
  private function addAngeboteTauschen(&$overview, $limit) {
    $query = "SELECT m.*, mgl.vorname, mgl.nachname, u.email
              FROM #__mgh_zb_market_place as m
                JOIN #__mgh_mitglied mgl ON m.userid = mgl.userid
                JOIN #__users u ON m.userid = u.id
		    	    WHERE m.art = 2
                AND m.ablauf > NOW()
                AND m.status = 1 
              ORDER BY m.erstellt DESC" . 
              ($limit > 0 ? ' LIMIT ' . $limit : '');
    $this->db->setQuery($query);
    $overview->angeboteTauschen = $this->db->loadObjectList();
    
    $query = "SELECT count(*)
              FROM #__mgh_zb_market_place as m
                JOIN #__mgh_mitglied mgl ON m.userid = mgl.userid
                JOIN #__users u ON m.userid = u.id
		    	    WHERE m.art = 2
                AND m.ablauf > NOW()
                AND m.status = 1";
    $this->db->setQuery($query);
    $overview->angeboteTauschenTotal = $this->db->loadResult();
  }
  
}
?>