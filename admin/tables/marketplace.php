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
  public $anbieter = null;
  public $kategorie_id = null;
  public $unterkategorie = null;
  public $beschreibung = null;
  public $titel = null;
  public $tags = null;
  public $update_timestamp = null;
  
	function ZeitbankTableMarketplace($db) {
		parent::__construct('#__mgh_zb_market_place', 'id', $db);
	}
	
	/**
	 * Vor dem Speichern eines Datensatzes werden zusätzliche Tasks durchgeführt.
	 */
	public function store($updateNulls = false) {
	  $this->update_timestamp = date('Y-m-d H:i:s');
	
	  return parent::store($updateNulls);
	}
}
?>
