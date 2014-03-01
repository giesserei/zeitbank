<?php
defined('_JEXEC') or die;

/**
 * Tabelle zum Speichern der Marktplatz-Einträge.
 * 
 * @author Steffen Förster
 */
class ZeitbankTableMarketplace extends JTable {
  
  public $id = null;
  public $userid = null;
  public $erstellt = null;
  public $ablauf = null;
  public $termin = null;
  public $status = null;
  public $art = null;
  public $richtung = null;
  public $arbeit_id = null;
  public $unterkategorie = null;
  public $beschreibung = null;
  public $titel = null;
  public $tags = null;
  public $update_timestamp = null;
  public $anforderung = null;
  public $zeit = null;
  public $aufwand = null;
  
	function ZeitbankTableMarketplace($db) {
		parent::__construct('#__mgh_zb_market_place', 'id', $db);
	}
	
	/**
	 * Vor dem Speichern eines Datensatzes werden zusätzliche Tasks durchgeführt.
	 */
	public function store($updateNulls = false) {
	  $this->update_timestamp = date('Y-m-d H:i:s');
	
	  if (empty($this->tags)) {
	    $this->tags = "";
	  }
	  if (empty($this->unterkategorie)) {
	    $this->unterkategorie = -1;
	  }
	  if ($this->art == 1) {
	    $this->richtung = -1;
	  }
	  if ($this->art == 2) {
	    $this->arbeit_id = -1;
	  }
	  if (empty($this->erstellt)) {
	    $this->erstellt = $this->update_timestamp;
	  }
	  if (empty($this->userid)) {
	    $this->userid = JFactory::getUser()->id;
	  }
	  
	  return parent::store($updateNulls);
	}
}
?>
