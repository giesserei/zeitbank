<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.archive' );
jimport( 'joomla.environment.response' );
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
   * und schreibt diese in den Response. Die Bewohner und das Gewerbe müssen noch aktives 
   * Mitglied sein (nicht ausgezogen).
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
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Erstellt die CSV-Datei mit den aktuellen Kontosaldo.
   */
  private function createKontosaldoCSVFile($filepath) {
    $db = $this->getDBO();
    $csv_output = 'Nachname;Vorname;Einzug;Dispensionsgrad;Saldo;User-ID';
    $csv_output .= "\n";
  
    $laufendesJahr = date('Y');
    $query = "
      SELECT m.vorname, m.nachname, m.einzug, m.dispension_grad, COALESCE(r.saldo, 0) saldo, m.userid  
      FROM (
        SELECT haben-soll saldo, userid
        FROM (
          SELECT ROUND(COALESCE((
            SELECT SUM(j1.minuten) / 60 FROM #__mgh_zb_journal j1
            WHERE j1.belastung_userid = h.gutschrift_userid
              AND j1.datum_quittung != '0000-00-00'
              AND j1.admin_del = 0
              AND datum_antrag BETWEEN '".$laufendesJahr."-01-01' AND '".$laufendesJahr."-12-31'
          ),0), 2) soll, h.haben, h.gutschrift_userid AS userid
          FROM (
            SELECT ROUND((sum(j2.minuten) / 60), 2) haben, j2.gutschrift_userid
            FROM #__mgh_zb_journal j2   
            WHERE
            j2.datum_quittung != '0000-00-00'
            AND j2.admin_del = 0
            AND datum_antrag BETWEEN '".$laufendesJahr."-01-01' AND '".$laufendesJahr."-12-31'
            GROUP BY j2.gutschrift_userid
          ) h
        ) s
      ) r RIGHT OUTER JOIN #__mgh_mitglied m ON r.userid = m.userid
      WHERE 
        m.typ IN (1,2)
        AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
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
