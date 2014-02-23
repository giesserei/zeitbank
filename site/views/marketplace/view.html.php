<?php 
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');

jimport('joomla.application.component.view');

/**
 * View-Klasse für die Ansicht des Marktplatzes.
 *
 * @author Steffen Förster
 */
class ZeitbankViewMarketPlace extends JView {
  
  protected $overview;
  
  protected $details;
  
  protected $menuId;
  
  function display($tpl = null) {
    if (!ZeitbankFrontendHelper::checkAuthMarket()) {
      return false;
    }
    
    if ($this->isLayout("details")) {
      $this->prepareDetails();
    }
    else if ($this->isLayout("meine")) {
      $this->prepareTableMeine();
    }
    else if ($this->isLayout("beziehen")) {
      $this->prepareTableBeziehen();
    }
    else if ($this->isLayout("geben")) {
      $this->prepareTableGeben();
    }
    else {
      $this->prepareDefault();
    }
    
    parent::display($tpl);
  }
  
  /**
   * Liefert einen Link, um zur Übersicht zu springen.
   */
  protected function getLinkOverview() {
    return '<a href="index.php?option=com_zeitbank&view=marketplace&Itemid='.$this->menuId.'">Zurück zur Übersicht</a>';
  }
  
  /**
   * Liefert einen Link zur Anzeige der Details in einem JS-Window.
   * Der Parameter tmpl=component sorgt dafür, dass nur die View ohne Template angezeigt wird.
   */
  protected function getLink($id, $text) {
    return '<a class="modal"
             href="index.php?option=com_zeitbank&tmpl=component&view=marketplace&layout=details&id='.$id.'"
             rel="{handler: \'iframe\', size: {x: 640, y: 480}}"><strong>'.$text.'</strong></a>';
  }
  
  /**
   * Erstellt die Tabelle mit den eigenen Angeboten.
   */
  protected function renderTableMeineAngebote() {
    if (!empty($this->overview->meineAngebote)) {
      echo "<h1>Marktplatz: Deine Einträge
        (".count($this->overview->meineAngebote)."/".$this->overview->meineAngeboteTotal.")</h1>";
    
      echo '<table class="zeitbank" >';
      echo '<tr class="head">
				  <th>Titel</th>
          <th>Giessereistunden</th>
          <th>Anbieter</th>
          <th>Erstellt</th>
          <th>Aktion</th>
        </tr>';
    
      $i = 0;
    
      foreach($this->overview->meineAngebote as $angebot) {
        $style = $i % 2 == 0 ? "even" : "odd";
        echo '<tr class="'.$style.'">
            <td>'.$this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)).'</td>
            <td>'.($angebot->art == 1 ? "beziehen" : "eintauschen").'</td>
            <td>'.($angebot->anbieter == 2 ? "Privat" : "Bereich " . $angebot->anbieter_name).'</td>
				    <td>'.JHTML::date($angebot->erstellt,"d.m.Y").'</td>
				    <td></td>
				  </tr>';
        $i ++;
      }
      echo "</table>";
      
      if (count($this->overview->meineAngebote) < $this->overview->meineAngeboteTotal) {
        echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid='.$this->menuId.'">Alle Einträge</a>
              </div>';
      }
    }
    else {
      echo "<h1>Marktplatz: Deine Einträge</h1>";
      echo "Du hast keine Einträge erfasst.";
    }
  }
  
  /**
   * Erstellt die Tabelle mit den eigenen Arbeitsangeboten.
   */
  protected function renderTableBeziehen() {
    if (!empty($this->overview->angeboteBeziehen)) {
      echo "<h1 style='margin-top:25px'>Marktplatz: Giessereistunden beziehen
         (".count($this->overview->angeboteBeziehen)."/".$this->overview->angeboteBeziehenTotal.")</h1>";
      echo "<div style='width:700px;margin-bottom:10px'>Hier findest du Angebote, wie du Giessereistunden durch das Leisten von Gemeinschaftsarbeit
      oder durch das Leisten einer Arbeit für einen Bewohner beziehen kannst.</div>";
    
      echo '<table class="zeitbank" >';
      echo '<tr class="head">
				  <th>Titel</th>
          <th>Anbieter der Giessereistunden</th>
          <th>Ansprechpartner</th>
          <th>Erstellt</th>
        </tr>';
    
      $i = 0;
    
      foreach($this->overview->angeboteBeziehen as $angebot) {
        $style = $i % 2 == 0 ? "even" : "odd";
        echo '<tr class="'.$style.'">
            <td>'.$this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)).'</td>
            <td>'.($angebot->anbieter == 2 ? "Privat" : "Bereich " . $angebot->anbieter_name).'</td>
            <td>'.$angebot->ansprechpartner.'</td>
				    <td>'.JHTML::date($angebot->erstellt,"d.m.Y").'</td>
				  </tr>';
        $i ++;
      }
      echo "</table>";
      
      if (count($this->overview->angeboteBeziehen) < $this->overview->angeboteBeziehenTotal) {
        echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=beziehen&Itemid='.$this->menuId.'">Alle Angebote</a>
              </div>';
      }
    }
    else {
      echo "<h1 style='margin-top:25px'>Marktplatz: Giessereistunden beziehen</h1>";
      echo "Es gibt aktuell keine Angebote zum Bezug von Giessereistunden";
    }
  }
  
  /**
   * Erstellt die Tabelle mit den eigenen Tauschangeboten.
   */
  protected function renderTableTauschen() {
    if (!empty($this->overview->angeboteGeben)) {
      echo "<h1 style='margin-top:25px'>Marktplatz: Giessereistunden eintauschen
        (".count($this->overview->angeboteGeben)."/".$this->overview->angeboteGebenTotal.")</h1>";
      echo "<div style='width:700px;margin-bottom:10px'>Hier findest du Angebote von Bewohnern oder des Gewerbes,
      die du mit deinen Giessereistunden begleichen kannst.</div>";
    
      echo '<table class="zeitbank" >';
      echo '<tr class="head">
				  <th>Titel</th>
          <th>Anbieter der Leistung</th>
          <th>Ansprechpartner</th>
          <th>Erstellt</th>
        </tr>';
    
      $i = 0;
    
      foreach($this->overview->angeboteGeben as $angebot) {
        $style = $i % 2 == 0 ? "even" : "odd";
        echo '<tr class="'.$style.'">
            <td>'.$this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)).'</td>
            <td>Privat</td>
            <td>'.$angebot->ansprechpartner.'</td>
				    <td>'.JHTML::date($angebot->erstellt,"d.m.Y").'</td>
				  </tr>';
        $i ++;
      }
      echo "</table>";
      
      if (count($this->overview->angeboteGeben) < $this->overview->angeboteGebenTotal) {
        echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=geben&Itemid='.$this->menuId.'">Alle Angebote</a>
              </div>';
      }
    }
    else {
      echo "<h1 style='margin-top:25px'>Marktplatz: Giessereistunden eintauschen</h1>";
      echo "Es gibt aktuell keine Angebote, wie du deine Giessereistunden eintauschen kannst.";
    }
  }
  
  // -------------------------------------------------------------------------
  // private section
  // -------------------------------------------------------------------------
  
  private function isLayout($layoutToCheck) {
    $jinput = JFactory::getApplication()->input;
    $layout = $jinput->get("layout");
    if (empty($layout)) {
      return false;
    }
    return $layout == $layoutToCheck;
  }
  
  private function prepareDefault() {
    $app = JFactory::getApplication();
    
    // Form-Daten aus Session löschen -> User hat die letzte Eingabe vielleicht nicht abgeschlossen
    $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, null);
    
    // Marktplatz aus Model laden
    $model = $this->getModel();
    $this->overview = $model->getOverview(1);
    
    ZeitbankFrontendHelper::addComponentStylesheet();
    
    // Menü-Id in der User-Session speichern
    $jinput = $app->input;
    $this->menuId = $jinput->get("Itemid", "0", "INT");
    $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);
  }
  
  private function prepareDetails() {
    $app = JFactory::getApplication();
    $id = $jinput = $app->input->get("id");
    $model = $this->getModel();
    $this->details = $model->getDetails($id);
    
    ZeitbankFrontendHelper::addComponentStylesheet();
  }
  
  private function prepareTableMeine() {
    $app = JFactory::getApplication();
    
    $model = $this->getModel();
    $this->overview = $model->getMeineAngebote();
    
    ZeitbankFrontendHelper::addComponentStylesheet();
    
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
  }
  
  private function prepareTableBeziehen() {
    $app = JFactory::getApplication();
  
    $model = $this->getModel();
    $this->overview = $model->getAngeboteBeziehen();
  
    ZeitbankFrontendHelper::addComponentStylesheet();
  
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
  }
  
  private function prepareTableGeben() {
    $app = JFactory::getApplication();
  
    $model = $this->getModel();
    $this->overview = $model->getAngeboteGeben();
  
    ZeitbankFrontendHelper::addComponentStylesheet();
  
    $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
  }
} 