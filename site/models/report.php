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
class ZeitbankModelReport extends JModelLegacy {
  
  /**
   * Erstellt eine CSV-Datei mit den aktuellen Kontosaldo der Bewohner und des Gewerbes
   * und schreibt diese in den Response. 
   */
  public function exportKontosaldoToCSV() {
    $filename = 'kontosaldo.csv';
    $random = rand(1, 99999);
    $filepath = JPATH_SITE.'/tmp/'.date('Y-m-d').'_'.strval($random).'_'.$filename;
    
    if ($this->createKontosaldoCSVFile($filepath, false)) {
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
   * Erstellt eine CSV-Datei mit den Kontosaldo der Bewohner und des Gewerbes für das Vorjahr
   * und schreibt diese in den Response.
   */
  public function exportKontosaldoVorjahrToCSV() {
    $filename = 'kontosaldo.csv';
    $random = rand(1, 99999);
    $filepath = JPATH_SITE.'/tmp/'.date('Y-m-d').'_'.strval($random).'_'.$filename;
  
    if ($this->createKontosaldoCSVFile($filepath, true)) {
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
   * Liefert eine CSV-Datei mit quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
   */
  public function exportAemtliBuchungenToCSV() {
    $filename = 'buchungen.csv';
    $random = rand(1, 99999);
    $filepath = JPATH_SITE.'/tmp/'.date('Y-m-d').'_'.strval($random).'_'.$filename;
  
    if ($this->createAemtliBuchungenCSVFile($filepath)) {
      // deliver file
      $this->deliverFile($filepath, 'buchungen');
  
      // clean up
      JFile::delete($filepath);
    }
    else {
      return false;
    }
  }
  
  /**
   * Liefert eine CSV-Datei mit quittierten Buchungen für das Konto des angemeldeten Benutzers - ohne Zeiteinschränkung. 
   */
  public function exportKontoauszugToCSV() {
    $filename = 'kontoauszug.csv';
    $random = rand(1, 99999);
    $filepath = JPATH_SITE.'/tmp/'.date('Y-m-d').'_'.strval($random).'_'.$filename;
  
    if ($this->createKontoauszugCSVFile($filepath)) {
      // deliver file
      $this->deliverFile($filepath, 'kontoauszug');
  
      // clean up
      JFile::delete($filepath);
    }
    else {
      return false;
    }
  }
  
  /**
   * Liefert die Summe der verbuchten Arbeitstunden (ohne freiwilligenarbeit) ohne den Stundentausch und die Geschenke. 
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
   * Liefert die Summe der nicht quittierten Arbeitstunden (ohne Freiwilligenarbeit) ohne den Stundentausch und die Geschenke.
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
   * Liefert die Summen der verbuchten und quittierten Stunden je Arbeitskategorie 
   * (ohne Geschenke, Stundentausch und Freiwilligenarbeit).
   */
  public function getSummeGiessereiStundenNachKategorie() {
    $db = $this->getDBO();
  
    $query = "
      SELECT ROUND((sum(j.minuten) / 60), 0) saldo, k.id, k.bezeichnung, k.gesamtbudget, 
        ROUND(((k.gesamtbudget / 365) * (DATEDIFF(NOW(), CONCAT(YEAR(NOW()), '-01-01'))))) budget_pro_rata
      FROM #__mgh_zb_journal_quittiert_laufend j 
        JOIN #__mgh_zb_arbeit a ON a.id = j.arbeit_id
        JOIN #__mgh_zb_kategorie k ON k.id = a.kategorie_id
      WHERE k.id NOT IN (".ZeitbankConst::KATEGORIE_ID_STUNDENGESCHENK.",".ZeitbankConst::KATEGORIE_ID_STUNDENTAUSCH.")
      GROUP BY k.bezeichnung
      ORDER BY k.bezeichnung";
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  /**
   * Liefert die Summen der verbuchten und quittierten Stunden je Arbeitskategorie
   * Geschenke, Stundentausch und Freiwilligenarbeit.
   */
  public function getSummeSonstigeStundenNachKategorie() {
    $db = $this->getDBO();
  
    $query = "
      SELECT ROUND((sum(j.minuten) / 60), 0) saldo, k.id, k.bezeichnung, k.gesamtbudget,
        ROUND(((k.gesamtbudget / 365) * (DATEDIFF(NOW(), CONCAT(YEAR(NOW()), '-01-01'))))) budget_pro_rata
      FROM #__mgh_zb_journal_quittiert_laufend_inkl_freiw j
        JOIN #__mgh_zb_arbeit a ON a.id = j.arbeit_id
        JOIN #__mgh_zb_kategorie k ON k.id = a.kategorie_id
      WHERE k.id IN (".ZeitbankConst::KATEGORIE_ID_STUNDENGESCHENK.",".ZeitbankConst::KATEGORIE_ID_STUNDENTAUSCH.",".ZeitbankConst::KATEGORIE_ID_FREIWILLIG.")
      GROUP BY k.bezeichnung
      ORDER BY k.bezeichnung";
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  /**
   * Liefert die maximale und die durchschnittliche Dauer zwischen einer Buchung und der Quittierung
   * (ohne Freiwilligenarbeit).
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
   * Liefert die durchschnittliche Wartezeit der noch unquittierten Buchungen des laufenden Jahres (ohne Freiwilligenarbeit).
   */
  public function getWartezeitUnquittierteBuchungen() {
    $db = $this->getDBO();
  
    $query = sprintf("
      SELECT ROUND(AVG(DATEDIFF(NOW(), datum_antrag)), 0) 
      FROM joomghjos_mgh_zb_journal j
      WHERE arbeit_id NOT IN (%s, %s)
        AND datum_quittung = '0000-00-00'
        AND admin_del = 0
        AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')
        AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = %s)",
      ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK, 
      ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH,
      ZeitbankConst::KATEGORIE_ID_FREIWILLIG);
    $db->setQuery($query);
    return $db->loadResult();
  }
    
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  /**
   * Erstellt die CSV-Datei mit den Kontosaldo.
   */
  private function createKontosaldoCSVFile($filepath, $vorjahr = false) {
    $db = $this->getDBO();
    $csv_output = 'Nachname;Vorname;Einzug;Austritt;Dispensionsgrad;Saldo;User-ID;E-Mail;Telefon1;Telefon2;WOHNUNG';
    $csv_output .= "\n";
  
    $view = $vorjahr ? "#__mgh_zb_journal_quittiert_vorjahr" : "#__mgh_zb_journal_quittiert_laufend";
    
    $query = "
      SELECT m.vorname, m.nachname, m.einzug, m.austritt, m.dispension_grad, COALESCE(r.saldo, 0) saldo, m.userid, 
             m.email, m.telefon, m.handy,
         (SELECT GROUP_CONCAT(DISTINCT objektid ORDER BY objektid DESC SEPARATOR ',')
          FROM #__mgh_x_mitglied_mietobjekt o
          WHERE o.userid = m.userid) wohnung 
      FROM (
        SELECT haben-soll saldo, userid
        FROM (
          SELECT ROUND(COALESCE((
            SELECT SUM(j1.minuten) / 60 FROM ".$view." j1
            WHERE j1.belastung_userid = h.gutschrift_userid
          ),0), 2) soll, h.haben, h.gutschrift_userid AS userid
          FROM (
            SELECT ROUND((sum(j2.minuten) / 60), 2) haben, j2.gutschrift_userid
            FROM ".$view." j2   
            GROUP BY j2.gutschrift_userid
          ) h
        ) s
      ) r RIGHT OUTER JOIN #__mgh_aktiv_mitglied m ON r.userid = m.userid
      WHERE m.typ IN (1,2)
      ORDER BY m.nachname     
    ";
    
    $db->setQuery($query);
    $rows = $db->loadObjectList();

    foreach($rows as $row) {
      foreach($row as $col_name => $value) {
        $csv_output .= $value.';';
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
   * Erstellt eine CSV-Datei mit quittierten Buchungen für alle Ämtli, die vom angemeldeten Benutzer verwaltet werden.
   * 
   * 2015-01-07 SF: keine Einschränkung hinsichtlich Zeitraum vornehmen
   */
  private function createAemtliBuchungenCSVFile($filepath) {
    $db = $this->getDBO();
    $user = JFactory::getUser();
    $csv_output = 'Buchung-Nr;Minuten;Datum;Arbeitsgattung;Empfänger;Kommentar Antrag;Kommentar Quittierung';
    $csv_output .= "\n";
  
    $query = "
       SELECT j.id, j.minuten, j.datum_antrag, a.kurztext,
         (SELECT u.name FROM #__users u WHERE u.id = j.gutschrift_userid) konto_gutschrift, 
         CONCAT('\"', j.kommentar_antrag, '\"') kommentar_antrag,
         CONCAT('\"', j.kommentar_quittung, '\"') kommentar_quittung
    	 FROM #__mgh_zb_journal j JOIN #__mgh_zb_arbeit a ON j.arbeit_id = a.id
       WHERE j.datum_quittung != '0000-00-00'
         AND j.admin_del = 0
         AND a.admin_id = ".$user->id."
       ORDER BY a.kurztext, j.datum_antrag DESC, j.id DESC
    ";
  
    $db->setQuery($query);
    $rows = $db->loadObjectList();
  
    foreach($rows as $row) {
      foreach($row as $col_name => $value) {
        $value = str_replace(array("\n", "\r"), '', $value);
        $csv_output .= $value.';';
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
   * Erstellt eine CSV-Datei mit allen quittierten Buchungen für das Konto des angemeldeten Benutzers.
   */
  private function createKontoauszugCSVFile($filepath) {
    $db = $this->getDBO();
    $user = JFactory::getUser();
    $csv_output = 'Buchung-Nr;Minuten;Minuten für Saldoberechnung;Datum Antrag;Datum Quittierung;Arbeitsgattung;bekommen von;übergeben an;Kommentar Antrag;Kommentar Quittierung';
    $csv_output .= "\n";
  
    $query = "
       SELECT j.id buchnungs_nr, j.minuten, 
         CASE 
           WHEN a.kategorie_id = -1 THEN 0
           WHEN j.belastung_userid = ".$user->id." THEN (-1 * j.minuten)
	         ELSE j.minuten
         END AS fuer_saldo_berechnung, 
         j.datum_antrag, j.datum_quittung, a.kurztext arbeitsgattung,
         CASE 
           WHEN j.belastung_userid = ".$user->id." THEN ''
		       WHEN j.belastung_userid != ".$user->id." AND a.id = 3 THEN 'Anonymous'
           ELSE (SELECT u.name FROM joomghjos_users u WHERE u.id = j.belastung_userid)
         END bekommen_von,
		     CASE 
           WHEN j.gutschrift_userid = ".$user->id." THEN ''
		       ELSE (SELECT u.name FROM joomghjos_users u WHERE u.id = j.gutschrift_userid)
		     END uebergeben_an,
         CONCAT('\"', j.kommentar_antrag, '\"') kommentar_antrag,
         CONCAT('\"', j.kommentar_quittung, '\"') kommentar_quittung
    	 FROM joomghjos_mgh_zb_journal j JOIN joomghjos_mgh_zb_arbeit a ON j.arbeit_id = a.id
       WHERE j.datum_quittung != '0000-00-00'
         AND j.admin_del = 0
         AND (j.gutschrift_userid = ".$user->id." OR j.belastung_userid = ".$user->id.")
       ORDER BY j.datum_antrag DESC, j.id DESC
    ";
  
    $db->setQuery($query);
    $rows = $db->loadObjectList();
  
    foreach($rows as $row) {
      foreach($row as $col_name => $value) {
        $value = str_replace(array("\n", "\r"), '', $value);
        $csv_output .= $value.';';
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
    $appWeb = JApplicationWeb::getInstance();
    $appWeb->setHeader('Content-Type', 'application/octet-stream', true);
    $appWeb->setHeader('Content-Transfer-Encoding', 'Binary', true);
    $appWeb->setHeader('Content-Disposition', 'attachment; filename='.$filename.'_'.date('Y-m-d').'.csv', true);
    $appWeb->setHeader('Content-Length', $filesize, true);
    $appWeb->sendHeaders();
    echo file_get_contents($filepath);
  }
  
}
