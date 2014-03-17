<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.archive' );
jimport('joomla.environment.response' );
jimport('joomla.application.component.model');

/**
 * Model zum Erstellen von Reports für die Zeitbank.
 * 
 * http://192.168.154.129/index.php?option=com_zeitbank&task=report.kontosaldo&format=raw
 * http://www.giesserei-gesewo.ch/index.php?option=com_zeitbank&task=report.kontosaldo&format=raw
 * 
 * @author Steffen Förster
 */
class ZeitbankModelReport extends JModel {
  
  /**
   * Erstellt eine CSV-Datei mit den aktuellen Kontosaldo der Bewohner und des Gewerbes
   * und schreibt diese in den Response. 
   */
  public function exportKontosaldoToCSV() {
    $filename = 'kontosaldo.csv';
    $random = rand(1, 99999);
    $filepath = JPATH_SITE.'/tmp/'.date('Y-m-d').'_'.strval($random).'_'.$filename;
    
    if ($this->createKontosaldoCSVFile($filepath)) {
      // deliver file
      $this->deliverFile($filepath, 'kontosaldo');
    
      // clean up
      JFile::delete($filepath);
    }
    else {
      return false;
    }
  }
  
  /**
   * Liefert die Summe der verbuchten Arbeitstunden ohne den Stundentausch. 
   */
  public function getSummeArbeitStunden() {
    $db = $this->getDBO();
    
    $query = sprintf("
        SELECT ROUND((sum(j.minuten) / 60), 0) stunden_verbucht
        FROM #__mgh_zb_journal_quittiert_laufend j
        WHERE arbeit_id NOT IN (%s, %s)", 
        ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK, 
        ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $db->setQuery($query);
    return $db->loadResult();
  }
  
  /**
   * Liefert die Summe der nicht quittierten Arbeitstunden ohne den Stundentausch.
   */
  public function getSummeNichtQuittierteStunden() {
    $db = $this->getDBO();
  
    $query = sprintf("
      SELECT ROUND((sum(j.minuten) / 60), 0) stunden_unquittiert
      FROM joomghjos_mgh_zb_journal j
      WHERE arbeit_id NOT IN (%s, %s)
        AND datum_quittung = '0000-00-00'
        AND admin_del = 0
        AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')", 
      ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK, 
      ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $db->setQuery($query);
    return $db->loadResult();
  }
  
  /**
   * Liefert die Summen der verbuchten Stunden je Arbeitskategorie.
   */
  public function getSummeStundenNachKategorie() {
    $db = $this->getDBO();
  
    $query = "
      SELECT ROUND((sum(j.minuten) / 60), 0) saldo, k.bezeichnung
      FROM #__mgh_zb_journal_quittiert_laufend j 
        JOIN #__mgh_zb_arbeit a ON a.id = j.arbeit_id
        JOIN #__mgh_zb_kategorie k ON k.id = a.kategorie_id
      GROUP BY k.bezeichnung
      ORDER BY k.bezeichnung";
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  /**
   * Liefert die maximale und die durchschnittliche Dauer zwischen einer Buchung und der Quittierung.
   */
  public function getQuittungDauer() {
    $db = $this->getDBO();
    
    $query = sprintf("
      SELECT MAX(DATEDIFF(datum_quittung, datum_antrag)) max_dauer, ROUND(AVG(DATEDIFF(datum_quittung, datum_antrag)), 0) avg_dauer
      FROM #__mgh_zb_journal_quittiert_laufend j
      WHERE arbeit_id NOT IN (%s, %s)", 
      ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK, 
      ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $db->setQuery($query);
    return $db->loadObject();
  }
  
  /**
   * Liefert die durchschnittliche Wartezeit der noch unquittierten Buchungen.
   */
  public function getWartezeitUnquittierteBuchungen() {
    $db = $this->getDBO();
  
    $query = sprintf("
      SELECT ROUND(AVG(DATEDIFF(NOW(), datum_antrag)), 0) 
      FROM joomghjos_mgh_zb_journal j
      WHERE arbeit_id NOT IN (%s, %s)
        AND datum_quittung = '0000-00-00'
        AND admin_del = 0
        AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')",
      ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK, 
      ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH);
    $db->setQuery($query);
    return $db->loadResult();
  }
    
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Erstellt die CSV-Datei mit den aktuellen Kontosaldo.
   */
  private function createKontosaldoCSVFile($filepath) {
    $db = $this->getDBO();
    $csv_output = 'Nachname;Vorname;Einzug;Austritt;Dispensionsgrad;Saldo;User-ID';
    $csv_output .= "\n";
  
    $query = "
      SELECT m.vorname, m.nachname, m.einzug, m.austritt, m.dispension_grad, COALESCE(r.saldo, 0) saldo, m.userid  
      FROM (
        SELECT haben-soll saldo, userid
        FROM (
          SELECT ROUND(COALESCE((
            SELECT SUM(j1.minuten) / 60 FROM #__mgh_zb_journal_quittiert_laufend j1
            WHERE j1.belastung_userid = h.gutschrift_userid
          ),0), 2) soll, h.haben, h.gutschrift_userid AS userid
          FROM (
            SELECT ROUND((sum(j2.minuten) / 60), 2) haben, j2.gutschrift_userid
            FROM #__mgh_zb_journal_quittiert_laufend j2   
            GROUP BY j2.gutschrift_userid
          ) h
        ) s
      ) r RIGHT OUTER JOIN #__mgh_mitglied m ON r.userid = m.userid
      WHERE m.typ IN (1,2)
      ORDER BY m.nachname     
    ";
    
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    foreach($rows as $row) {
      foreach($row as $col_name => $value) {
        $csv_output .= $value.'; ';
      }
      $csv_output .= "\n";
    }

    if (!JFile::write($filepath, $csv_output)) {
      JFactory::getApplication()->enqueueMessage('Datei konnte nicht erstellt werden.');
      return false;
    }
    return true;
  }
  
  /**
   * Schreibt das ZIP-File in den Response-Output-Stream.
   */
  private function deliverFile($filepath, $filename) {
    $filesize = filesize($filepath);
    JResponse::setHeader('Content-Type', 'application/octet-stream');
    JResponse::setHeader('Content-Transfer-Encoding', 'Binary');
    JResponse::setHeader('Content-Disposition', 'attachment; filename='.$filename.'_'.date('Y-m-d').'.csv');
    JResponse::setHeader('Content-Length', $filesize);
    echo JFile::read($filepath);
  }
  
}
