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
  
    $query = "
      SELECT nachname, vorname, einzug, dispension_grad, haben-soll saldo, userid
      FROM (
        SELECT ROUND(COALESCE((
          SELECT SUM(j1.minuten) / 60 FROM joomghjos_mgh_zb_journal j1
          WHERE j1.belastung_userid = h.gutschrift_userid
            -- nur quittierte
            AND j1.datum_quittung != '0000-00-00'
            -- ohne die gelöschten
            AND j1.admin_del = 0
            -- Zeitraum
            AND datum_antrag between '2014-01-01' AND '2014-12-31'
        ),0), 2) soll, h.haben, h.gutschrift_userid AS userid, h.nachname, h.vorname, h.einzug, h.dispension_grad 
        FROM (
          SELECT ROUND((sum(j2.minuten) / 60), 2) haben, j2.gutschrift_userid, m.nachname, m.vorname, m.einzug, m.dispension_grad
          FROM joomghjos_mgh_zb_journal j2 
            JOIN joomghjos_mgh_mitglied m ON j2.gutschrift_userid = m.userid
          WHERE
          -- nur quittierte
          j2.datum_quittung != '0000-00-00'
          -- ohne die gelöschten
          AND j2.admin_del = 0
          -- Zeitraum
          AND datum_antrag between '2014-01-01' AND '2014-12-31'
          -- Nur aktive Bewohner und Gewerbe
          AND m.typ IN (1,2)
          AND (m.austritt = '0000-00-00' OR m.austritt > NOW())
          GROUP BY j2.gutschrift_userid, m.nachname, m.vorname, m.einzug, m.dispension_grad
        ) h
      ) s
      ORDER BY NACHNAME
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
