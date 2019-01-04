<?php

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');
JLoader::register('ZeitbankCalc', JPATH_COMPONENT . '/helpers/zeitbank_calc.php');

$max_journal_buchungen = 10000;

$user = JFactory::getUser();
$model = $this->getModel();
$lastYear = intval(date('Y')) - 1;

$useLastYear = !ZeitbankCalc::isCurrentYearAllowed();
$year = $useCurrentYear ? intval(date('Y')) : intval(date('Y')) - 1;

echo '<div class="component">';

if (ZeitbankAuth::checkAuthZeitbank()):

    //echo '<div style="color:red;font-size:14pt;margin-bottom:20px;border-width:1px; border-color:red; border-style:solid;padding:5px">';
    //echo 'Aktuell funktionieren die Funktionen Stundengeschenk und Stundentausch nicht.';
    //echo '</div>';

    // Kategorien-Administrator?
    if ($kategorieId = ZeitbankAuth::getKategorieId()):
        $kategorie = $this->getKategorieItem($kategorieId);
        echo "<h1>Zeitbank: Du bist Administrator der Kategorie <strong>" . $kategorie->bezeichnung . "</strong></h1>";
        echo "Du kannst:";
        echo "<ul>";
        echo "<li><a href=\"" . JRoute::_("index.php?option=com_zeitbank&view=arbeiten&Itemid=" . $this->menuId) . "\">Deine Ämtli verwalten</a></li>";
        echo "<li><a href=\"" . JRoute::_("index.php?option=com_zeitbank&task=kategorie.edit&id=" . $kategorieId . "&Itemid=" . $this->menuId) . "\">Dein Kategorienbudget verwalten</a></li>";
        echo "<li><a href=\"" . JRoute::_("index.php?option=com_zeitbank&task=arbeitadmin.edit&id=0&Itemid=" . $this->menuId) . "\">Deine Ämtli-Administratoren verwalten</a></li>";
        echo "</ul><br />";
    endif;

    // Ämtli-Administrator?
    if (ZeitbankAuth::isAemtliAdmin()):
        echo "<h1>Zeitbank: Du bist Ämtli-Administrator</h1>";
        echo "Du kannst:";
        echo "<ul>";
        echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittung_amt&Itemid=" . $this->menuId . "\">Anträge quittieren</a> (offene Anträge: " . $this->getAnzahlOffeneQuittierungen() . ")</li>";
        echo "<li><a href=\"/index.php?option=com_zeitbank&view=quittungsliste_amt&Itemid=" . $this->menuId . "\">Quittierte Buchungen anzeigen</a></li>";
        echo "<li><a href=\"/index.php?option=com_zeitbank&task=report.aemtliBuchungen&format=raw\">Download: Quittierte Buchungen mit Kommentaren</a>&nbsp;(Format CSV, Encoding UTF-8)</li>";
        echo "</ul><br />";
    endif;

    // Reports
    if (ZeitbankAuth::hasAccess(ZeitbankAuth::ACTION_REPORT_DOWNLOAD_SALDO)) {
        echo "<h1>Zeitbank: Du hast Zugriff auf die Zeitbank-Reports</h1>";
        echo "Du kannst:";
        echo '<ul><li><a href="index.php?option=com_zeitbank&view=report&Itemid=' . $this->menuId . '">Kennzahlen ansehen und Reports erstellen</a></li></ul><br />';
    }

    // Allgemeine Funktionen
    echo "<h1>Zeitbank: Allgemeine Funktionen</h1>";
    echo '<ul>
		      <li><a href="/index.php?option=com_zeitbank&Itemid=' . $this->menuId . '&view=marketplace">Arbeitsangebote und Angebote zum Stundentausch</a></li>
		      <li><a href="/index.php?option=com_zeitbank&Itemid=' . $this->menuId . '&view=arbeitsliste">Liste mit allen Ämtli und Zuständigkeiten</a></li>
		      <li><a href="/index.php?option=com_zeitbank&Itemid=' . $this->menuId . '&view=report">Kennzahlen ansehen</a></li>
		  </ul><br />';

    // Offene Quittierungen und Anträge
    echo "<h1>Zeitbank: Quittierungen und Anträge</h1>";

    /* Liste der persönlichen Zeitbankauszüge ausgeben */
    /*

    echo "<h1><a href=\"index2.php?option=com_zeitbank&Itemid=".MENUITEM."&view=zeitbank\"	target=\"_blank\">
            <img src=\"/images/M_images/printButton.png\" style=\"float: right;\"></a>Zeitbank: Dein Konto</h1>";
    */


    // Offene Quittungen ausgeben (Bestätigung dass Stunden beim aktuellen User ab, beim Antragsteller eingebucht werden)
    echo "<h4>Offene Quittierungen (inkl. privater Stundentausch)</h4>";

    if (count($this->quittierungen) > 0) {
        echo '<table class="zeitbank" >';
        echo '<tr class="head">
				    <th>Datum</th>
			        <th>Antrag von</th>
			        <th>Arbeitsgattung</th>
			        <th>Zeit<br />[min]</th>
			        <th>Kommentar</th>
			        <th style="text-align:right">B-Nr.</th>
			        <th>&nbsp;</th>
			      </tr>';

        $k = 0;
        foreach ($this->quittierungen as $qt) {
            $style = $k ? "zb_even" : "zb_odd";
            $ktext = ZeitbankFrontendHelper::cropText($qt->text, 35);
            echo '<tr class="' . $style . '">
					      <td>' . JHTML::date($qt->datum_antrag, 'd.m.Y') . '</td>
					      <td>' . ZeitbankFrontendHelper::getEmailLink($qt->vorname, $qt->nachname, $qt->email, "Antrag privater Stundentausch") . '</td>
					      <td>' . $qt->kurztext . '</td>
					      <td style="text-align:right;">' . $qt->minuten . '</td>
					      <td>' . $ktext . '</td>
					      <td style="text-align:right">' . $qt->id . '</td>
					      <td>
					        <input type="button" value="bestätigen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=quittung.edit&id=' . $qt->id . '&Itemid=' . $this->menuId . '\'">
					      </td>
					    </tr>';
            $k = 1 - $k;
        }
        echo '</table>';
    } else {
        echo "Keine offenen Quittierungen";
    }

    // Offene Anfragen => Einträge auflisten, für welche der aktuelle User Stunden geleistet hat, aber noch nicht bestätigt wurden
    echo "<br /><br /><h4>Offene Anträge (von dir geleistete, unquittierte Stunden)</h4>";

    if (count($this->antraege) > 0) {
        echo "<table class=\"zeitbank\" >";
        echo "<tr class=\"head\">
				      <th>Datum</th>
			        <th>Antrag an</th>
			        <th>Arbeitsgattung<br/>Ämtli-VerantwortlicheR</th>
			        <th>Zeit<br />[min]</th>
			        <th>Kommentar</th>
			        <th>B-Nr.</th>
			        <th>&nbsp;</th>
			      </tr>";

        $k = 0;
        $countAbgelehnt = 0;
        foreach ($this->antraege as $at) {
            $style = $k ? "zb_even" : "zb_odd";
            $ktext = ZeitbankFrontendHelper::cropText($at->text, 35);
            echo '<tr class="' . $style . '">
				        <td>' . JHTML::date($at->datum_antrag, 'd.m.Y') . '</td>
				        <td>' . $at->name . '</td>
				        <td>' . $at->kurztext;
            // Ämtli-Verantwortlichen anzeigen, wenn kein privater Stundentausch
            if ($at->arbeit_id != ZeitbankConst::ARBEIT_ID_STUNDENTAUSCH) {
                echo '<br/>' . ZeitbankFrontendHelper::getEmailLink($at->vorname, $at->nachname, $at->email,
                        'Zeitbank / Mein Antrag vom ' . JHTML::date($at->datum_antrag, 'd.m.Y'));
            }

            echo '</td>
				        <td style="text-align:right;">' . $at->minuten . '</td>
				        <td>' . $ktext . '</td>
				        <td style="text-align:right">' . $at->id . '</td>
					      <td>';

            if (!ZeitbankCalc::isBuchungGesperrt()) {
                echo '<input type="button" value="ändern" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=' . $at->task . '&id=' . $at->id . '&Itemid=' . $this->menuId . '\'"/>';
            }

            echo '<input type="button" value="löschen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=antragloeschen.confirmDelete&id=' . $at->id . '&Itemid=' . $this->menuId . '\'"/>
				        </td>
					    </tr>';
            if ($at->abgelehnt == 1) {
                echo '<tr class="' . $style . '">
				          <td colspan="7" style="color:red">Antrag abgelehnt: ' . $at->kommentar_ablehnung . '</td>
				        </tr>';
                $countAbgelehnt++;
            }
            $k = 1 - $k;
        }
        echo "</table>";
        if ($countAbgelehnt > 0) {
            echo '<br/><strong>Achtung:</strong> Es ' . ($countAbgelehnt == 1 ? 'wurde ein Antrag' : 'wurden ' . $countAbgelehnt . ' Anträge') . ' abgelehnt.
			        Bitte ändere ' . ($countAbgelehnt == 1 ? 'den betroffenen Antrag' : 'die betroffenen Anträge') . '.';
        }
    } else {
        echo "Keine offenen Anträge";
        echo "<br />";
    };

    if (ZeitbankCalc::isBuchungGesperrt()) {
        echo '<br /><h1 style="color:red">Wegen Wartungsarbeiten können keine Buchungen erfasst werden.</h1>';
    } else {
        echo '<br />
            <fieldset>
              <input type="button" value="Antrag Eigenleistungen" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=eigenleistungen.edit&Itemid=' . $this->menuId . '\'" />&nbsp;&nbsp;
              <input type="button" value="Antrag privater Stundentausch" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=stundentausch.edit&Itemid=' . $this->menuId . '\'" />&nbsp;&nbsp;
              <input type="button" value="Antrag Freiwilligenarbeit" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=freiwilligenarbeit.edit&Itemid=' . $this->menuId . '\'" />&nbsp;&nbsp;
  		        <input type="button" value="Stunden verschenken" onclick="window.location.href=\'/index.php?option=com_zeitbank&task=stundengeschenk.edit&Itemid=' . $this->menuId . '\'" />
  		      </fieldset>';
    }

    // Journal
    echo "<br />";
    echo "<h1>Zeitbank: Dein Journal</h1>";


    $saldo = $this->getSaldo($useLastYear);
    $saldoVorjahr = $this->getSaldoVorjahr();
    $saldoFreiwilligenarbeit = $this->getSaldoFreiwilligenarbeit($useLastYear);
    $soll = $this->getSoll();

    echo '<div style="margin-bottom:10px">
	        <table class="stunden">
		        <tr>
		          <td class="description">Dein Jahressaldo der Giessereistunden für ' . $year . '</td>
		          <td class="time"><strong>' . ZeitbankFrontendHelper::formatTime($saldo) . ' h</strong></td>
		          <td></td>
		        </tr>
		        <tr>
		          <td class="description">Dein Jahressaldo der Freiwilligenstunden für ' . $year . '</td>
		          <td class="time"><strong>' . ZeitbankFrontendHelper::formatTime($saldoFreiwilligenarbeit) . ' h</strong></td>
		          <td></td> 
		        </tr>';
    if (!$this->isGewerbe()) {
        echo '<tr>
		          <td class="description">Dein Stundensoll für ' . $year . '</td>
		          <td class="time">
		            <strong>' . ZeitbankFrontendHelper::formatTime($soll) . ' h</strong>
		          </td>
		          <td class="description">
		            <a class="modal"
                  href="index.php?option=com_zeitbank&tmpl=component&view=zeitbank&layout=hinweise_soll"
                  rel="{handler: \'iframe\', size: {x: 700, y: 480}}"><strong>Hinweise zur Berechnung</strong></a>
		          </td>
		        </tr>';
    }
    if (!$useLastYear) {
        echo '<tr>
		          <td class="description">Dein Jahressaldo des Vorjahres ' . $lastYear . '</td>
		          <td class="time">' . ZeitbankFrontendHelper::formatTime($saldoVorjahr) . ' h</td>
		          <td></td>
		       </tr>';
    }
    echo '</table></div>';

    // Alle verbuchten Posten aus dem Journal
    // TODO (SF) Code benötigt dringend Refactoring -> Für jede Buchung gibt es eine DB-Anfrage, um den Namen des Benutzers zu holen
    echo "<h4>Bestätigte Buchungen des Jahres <span style=\"color:#9C2215\">" . $year . "</span></h4><br />";

    if (count($this->journal) > 0) {
        echo "<table class=\"zeitbank\" >";
        echo '<tr style="background-color: #7BB72B; color:white;">
				      <th>Datum</th>
		          <th>bekommen von</th>
		          <th>übergeben an</th>
		          <th>Arbeitsgattung</th>
		          <th style="text-align:right">Zeit<br />[min]</th>
		          <th style="text-align:right">Saldo<br />[h:m]</th>
		          <th style="text-align:right">B-Nr.</th>
		        </tr>';

        $k = 0;    // Zebra start
        $zaehler = 0;

        foreach ($this->journal as $jn) {
            if ($zaehler < $max_journal_buchungen):
                $zaehler++;
                // Der Schenker bleibt anonym & die Buchungsdetails können nicht betrachtet werden
                $isGeschenk = $jn->arbeit_id == ZeitbankConst::ARBEIT_ID_STUNDENGESCHENK;
                $isFreiwillig = $jn->art === 'freiwillig';

                $op_sign = ($jn->belastung_userid == $user->id ? "-" : "+");
                $op_sign = ($isFreiwillig ? "" : $op_sign); // kein Vorzeichen bei Freiwilligenarbeit
                $geber_name = "";
                $empf_name = "";

                if ($isGeschenk) {
                    if ($jn->belastung_userid == $user->id) {
                        $empf_name = $model->getUserName($jn->gutschrift_userid);
                    }
                } else {
                    if ($jn->belastung_userid != $user->id) {
                        $geber_name = $model->getUserName($jn->belastung_userid);
                    } else {
                        $empf_name = $model->getUserName($jn->gutschrift_userid);
                    }
                }

                $style = $k ? "e9e2c8" : "EEE"; // Zebramuster
                $styleMinuten = $isFreiwillig ? "color:#888888" : "";
                $styleSaldo = $saldo < 0 ? 'color:red;' : '';
                echo '<tr style="vertical-align:top; background-color: #' . $style . '">
						      <td>' . ZeitbankFrontendHelper::getLinkBuchung($jn->id, JHTML::date($jn->datum_antrag, 'd.m.Y')) . '</td>
		              <td>' . $geber_name . '</td>
		              <td>' . $empf_name . '</td>
		              <td>' . $jn->kurztext . '</td>
			            <td style="text-align:right;' . $styleMinuten . '">' . ($isFreiwillig ? "(" : "") . $op_sign . $jn->minuten . ($isFreiwillig ? ")" : "") . '</td>
						      <td style="text-align:right;' . $styleSaldo . '">' . ZeitbankFrontendHelper::formatTime($saldo) . '</td>
			            <td style="text-align:right;">' . $jn->id . '</td>
				        </tr>';
                $k = 1 - $k;

                // Saldo ändert sich nicht bei Freiwilligenarbeit
                if (!$isFreiwillig) {
                    if ($jn->belastung_userid != $user->id) {
                        $saldo -= $jn->minuten;
                    } else {
                        $saldo += $jn->minuten;
                    }
                }
            endif;
        }
        echo "</table>";
    } else {
        // Noch keine Buchungen
        echo "<p>Noch keine Buchungen für " . $year . " vorhanden.</p>";
    }

    //echo "<br /><br /><input type=\"button\" value=\"Alle Buchungen anzeigen\" onclick=\"window.location.href='index.php?option=com_zeitbank&view=userJournal&Itemid=".MENUITEM."'\"/><br/><br/>";
    echo '<br /><br /><ul>
		        <li><a href="/index.php?option=com_zeitbank&view=userJournal&Itemid=' . $this->menuId . '">Alle Buchungen anzeigen</a></li>
		        <li><a href="/index.php?option=com_zeitbank&task=report.kontoauszug&format=raw">Download: Kontoauszug</a>&nbsp;(Format CSV, Encoding UTF-8)</li>
		      </ul>';

    // Giessereifonds
    echo "<h1>Zeitbank: Giessereistundenfonds</h1>";
    $saldoStundenfonds = $this->getSaldoStundenfonds($useLastYear);
    echo "Stunden im Giessereistundenfonds für " . $year . ":&nbsp;&nbsp;&nbsp;<strong>" . ZeitbankFrontendHelper::formatTime($saldoStundenfonds) . " h</strong><br />";
    if (!$useLastYear) {
        $saldoStundenfondsVorjahr = $this->getSaldoStundenfondsVorjahr();
        echo "Stunden im Giessereistundenfonds für " . $lastYear . ":&nbsp;&nbsp;&nbsp;<strong>" . ZeitbankFrontendHelper::formatTime($saldoStundenfondsVorjahr) . " h</strong><br /><br />";
    }

endif;    // Userprüfung

echo '<div style="color:#888888;margin-top:10px;text-align:right">Release: ' . ZeitbankConst::RELEASE . '</div>';

echo "</div>";
