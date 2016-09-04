<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

/**
 * View-Klasse für die Ansicht des Marktplatzes.
 */
class ZeitbankViewMarketPlace extends JViewLegacy
{

    /**
     * @var MarketPlaceOverview
     */
    protected $overview;

    /**
     * @var MarketPlaceDetails
     */
    protected $details;

    /**
     * @var int
     */
    protected $menuId;

    function display($tpl = null)
    {
        if (!ZeitbankAuth::checkAuthMarket()) {
            return false;
        }

        if ($this->isLayout("details")) {
            $this->prepareDetails();
        } else if ($this->isLayout("meine")) {
            $this->prepareTableMeine();
        } else if ($this->isLayout("arbeiten")) {
            $this->prepareTableArbeiten();
        } else if ($this->isLayout("tauschen")) {
            $this->prepareTableTauschen();
        } else {
            $this->prepareDefault();
        }

        return parent::display($tpl);
    }

    /**
     * Liefert einen Link, um zur Übersicht zu springen.
     */
    protected function getLinkOverview()
    {
        return '<a href="index.php?option=com_zeitbank&view=marketplace&Itemid=' . $this->menuId . '">Zurück zur Übersicht</a>';
    }

    /**
     * Liefert einen Link, um zur Zeitbank zu springen.
     */
    protected function getLinkZeitbank()
    {
        return '<a href="index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $this->menuId . '">Zurück zur Übersicht</a>';
    }

    /**
     * Liefert einen Link zur Anzeige der Details in einem JS-Window.
     * Der Parameter tmpl=component sorgt dafür, dass nur die View ohne Template angezeigt wird.
     *
     * @param int $id ID des Objektes
     * @param string text Link-Text
     *
     * @return string
     */
    protected function getLink($id, $text)
    {
        return '<a class="modal"
             href="index.php?option=com_zeitbank&tmpl=component&view=marketplace&layout=details&id=' . $id . '"
             rel="{handler: \'iframe\', size: {x: 640, y: 480}}"><strong>' . $text . '</strong></a>';
    }

    /**
     * Liefert true, wenn der Benutzer bereits Einträge erstellt hat.
     */
    protected function hasEntries()
    {
        return !empty($this->overview->meineAngebote);
    }

    /**
     * Erstellt die Tabelle mit den eigenen Angeboten.
     */
    protected function renderTableMeineAngebote()
    {
        if (!empty($this->overview->meineAngebote)) {
            echo "<h1>Deine Einträge
        (" . count($this->overview->meineAngebote) . "/" . $this->overview->meineAngeboteTotal . ")</h1>";

            echo '<table class="market_overview" >';
            echo '<tr class="head">
				  <th>Titel</th>
          <th>Art</th>
          <th>Erstellt</th>
          <th>Ablauf</th>
          <th>Status</th>
          <th>Aktion</th>
        </tr>';

            $i = 0;

            foreach ($this->overview->meineAngebote as $angebot) {
                $style = $i % 2 == 0 ? "zb_even" : "zb_odd";
                $now = new DateTime('now');
                $ablauf = new DateTime($angebot->ablauf);
                $styleAblauf = ($now < $ablauf ? '' : 'style="color:red"');

                echo '<tr class="' . $style . '">
            <td>' . $this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)) . '</td>
            <td>' . ($angebot->art == 1 ? "Arbeitsangebot" : "Tauschen") . '</td>
				    <td>' . JHTML::date($angebot->erstellt, "d.m.Y") . '</td>
				    <td ' . $styleAblauf . '>' . JHTML::date($angebot->ablauf, "d.m.Y") . '</td>
				    <td>' . ($angebot->status == 1 ? "aktiv" : "inaktiv") . '</td>
				    <td><input type="button" value="Bearbeiten" 
				               onclick="window.location.href=\'index.php?option=com_zeitbank&task=updangebot.edit&id=' . $angebot->id . '&Itemid=' . $this->menuId . '\'" />
				        <input type="button" value="Löschen"
				               onclick="window.location.href=\'index.php?option=com_zeitbank&task=updangebot.delete&id=' . $angebot->id . '\'&Itemid=' . $this->menuId . '\'" />
				    </td>
				  </tr>';
                $i++;
            }
            echo "</table>";

            if (count($this->overview->meineAngebote) < $this->overview->meineAngeboteTotal) {
                echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=meine&Itemid=' . $this->menuId . '">Alle Einträge</a>
              </div>';
            }
        } else {
            echo "<h1>Deine Einträge</h1>";
            echo "Du hast keine Einträge erfasst.";
        }
    }

    /**
     * Erstellt die Tabelle mit den Arbeitsangeboten.
     */
    protected function renderTableArbeiten()
    {
        if (!empty($this->overview->angeboteArbeiten)) {
            echo "<h1 style='margin-top:25px'>Arbeitsangebote
         (" . count($this->overview->angeboteArbeiten) . "/" . $this->overview->angeboteArbeitenTotal . ")</h1>";
            echo "<div style='width:700px;margin-bottom:10px'>Hier findest du aktuelle Arbeitsangebote unserer Bereiche.<br/>
               Ein Klick auf den Titel öffnet die Beschreibung mit allen Details zu einem Angebot.</div>";

            echo '<table class="market_overview" >';
            echo '<tr class="head">
				  <th>Titel</th>
          <th>Arbeitsgattung</th>
          <th>Ansprechpartner</th>
          <th>Erstellt</th>
        </tr>';

            $i = 0;

            foreach ($this->overview->angeboteArbeiten as $angebot) {
                $style = $i % 2 == 0 ? "zb_even" : "zb_odd";
                echo '<tr class="' . $style . '">
            <td>' . $this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)) . '</td>
            <td>' . ZeitbankFrontendHelper::cropText($angebot->konto, 50) . '</td>
            <td>' . ZeitbankFrontendHelper::getEmailLink($angebot->vorname, $angebot->nachname, $angebot->email,
                        'Marktplatz / ' . ZeitbankFrontendHelper::cropText($angebot->titel, 75)) . '</td>
				    <td>' . JHTML::date($angebot->erstellt, "d.m.Y") . '</td>
				  </tr>';
                $i++;
            }
            echo "</table>";

            if (count($this->overview->angeboteArbeiten) < $this->overview->angeboteArbeitenTotal) {
                echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=arbeiten&Itemid=' . $this->menuId . '">Alle Angebote</a>
              </div>';
            }
        } else {
            echo "<h1 style='margin-top:25px'>Arbeitsangebote</h1>";
            echo "Es gibt aktuell keine Arbeitsangebote";
        }
    }

    /**
     * Erstellt die Tabelle mit den eigenen Tauschangeboten.
     */
    protected function renderTableTauschen()
    {
        if (!empty($this->overview->angeboteTauschen)) {
            echo "<h1 style='margin-top:25px'>Marktplatz zum Tauschen von Giessereistunden
        (" . count($this->overview->angeboteTauschen) . "/" . $this->overview->angeboteTauschenTotal . ")</h1>";
            echo "<div style='width:700px;margin-bottom:10px'>Hier findest du Angebote für einen privaten Stundentausch.<br/>
               Ein Klick auf den Titel öffnet die Beschreibung mit allen Details zu einem Angebot.</div>";

            echo '<table class="market_overview" >';
            echo '<tr class="head">
				  <th>Titel</th>
          <th>Suche / Angebot</th>
          <th>Ansprechpartner</th>
          <th>Erstellt</th>
        </tr>';

            $i = 0;

            foreach ($this->overview->angeboteTauschen as $angebot) {
                $style = $i % 2 == 0 ? "zb_even" : "zb_odd";
                echo '<tr class="' . $style . '">
            <td>' . $this->getLink($angebot->id, ZeitbankFrontendHelper::cropText($angebot->titel, 25)) . '</td>
            <td>' . ($angebot->richtung == 1 ? 'Suche Stunden' : 'Biete Stunden') . '</td>
            <td>' . ZeitbankFrontendHelper::getEmailLink($angebot->vorname, $angebot->nachname, $angebot->email,
                        'Marktplatz / ' . ZeitbankFrontendHelper::cropText($angebot->titel, 75)) . '</td>
				    <td>' . JHTML::date($angebot->erstellt, "d.m.Y") . '</td>
				  </tr>';
                $i++;
            }
            echo "</table>";

            if (count($this->overview->angeboteTauschen) < $this->overview->angeboteTauschenTotal) {
                echo '<div style="margin-top:10px">
                <a href="index.php?option=com_zeitbank&view=marketplace&layout=tauschen&Itemid=' . $this->menuId . '">Alle Tauschangebote</a>
              </div>';
            }
        } else {
            echo "<h1 style='margin-top:25px'>Marktplatz zum Tauschen von Giessereistunden</h1>";
            echo "Es gibt aktuell keine Tauschangebote.";
        }
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    private function isLayout($layoutToCheck)
    {
        $jinput = JFactory::getApplication()->input;
        $layout = $jinput->get("layout");
        if (empty($layout)) {
            return false;
        }
        return $layout == $layoutToCheck;
    }

    private function prepareDefault()
    {
        $app = JFactory::getApplication();

        // Form-Daten aus Session löschen -> User hat die letzte Eingabe vielleicht nicht abgeschlossen
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_DATA, null);
        $app->setUserState(ZeitbankConst::SESSION_KEY_MARKET_PLACE_ENTRY_ART, null);

        // Marktplatz aus Model laden
        $model = $this->getModel();
        $this->overview = $model->getOverview(5);

        ZeitbankFrontendHelper::addComponentStylesheet();

        // Menü-Id in der User-Session speichern
        $jinput = $app->input;
        $this->menuId = $jinput->get("Itemid", "0", "INT");
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $this->menuId);
    }

    private function prepareDetails()
    {
        $app = JFactory::getApplication();
        $id = $jinput = $app->input->get("id");
        $model = $this->getModel();
        $this->details = $model->getDetails($id);

        ZeitbankFrontendHelper::addComponentStylesheet();
    }

    private function prepareTableMeine()
    {
        $app = JFactory::getApplication();

        $model = $this->getModel();
        $this->overview = $model->getMeineAngebote();

        ZeitbankFrontendHelper::addComponentStylesheet();

        $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    }

    private function prepareTableArbeiten()
    {
        $app = JFactory::getApplication();

        $model = $this->getModel();
        $this->overview = $model->getAngeboteArbeiten();

        ZeitbankFrontendHelper::addComponentStylesheet();

        $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    }

    private function prepareTableTauschen()
    {
        $app = JFactory::getApplication();

        $model = $this->getModel();
        $this->overview = $model->getAngeboteTauschen();

        ZeitbankFrontendHelper::addComponentStylesheet();

        $this->menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
    }
} 