<?php
defined('_JEXEC') or die();

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('BuchungHelper', JPATH_COMPONENT . '/helpers/buchung.php');

jimport('joomla.application.component.controller');

/**
 * Controller für die Autocomplete-Funktion. 
 *
 * @author Steffen Förster
*/
class ZeitbankControllerBuchung extends JController {
  
  /**
   * Liefert die Mitglieder als JSON-Struktur.
   */
  public function users() {
    if (!ZeitbankAuth::checkAuthZeitbank()) {
      return false;
    }
    
    $doc =& JFactory::getDocument();
    $doc->setMimeEncoding('text/plain');
    
    $query = $this->getQuery();
    $empfaenger = BuchungHelper::getEmpfaengerLike($this->getQuery());
    
    $json = '{"query":"'.$query.'","suggestions":[';
    foreach($empfaenger as $e) {
      $json = $json . '{"value":"'.$e->vorname.' '.$e->nachname.'","data":"'.$e->userid.'"},';
    }
    if (!empty($empfaenger)) {
      $json = substr($json, 0, -1);
    }
    $json = $json . ']}';
    
    echo $json;
  }
  
  /**
   * Liefert die Zeichenkette, die der Benutzer für die Suche nach dem Empfänger eingegeben hat.
   */
  private function getQuery() {
    $app = JFactory::getApplication();
    $input = $app->input;
    return $input->get("query", "", "STRING");
  }
  
}