<?php

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');

echo "<div style='margin-bottom:20px;color:#7BB72B;font-size:14pt'><strong>" . $this->details->item->titel . "</strong></div>";

echo "<div style='margin-bottom:5px'><strong>Beschreibung:</strong></div>";
echo "<div style='margin-bottom:10px'>" . $this->details->item->beschreibung . "&nbsp;</div>";

if ($this->details->item->art == 1) {
    echo "<div style='margin-bottom:5px'><strong>Anforderung:</strong></div>";
    echo "<div style='margin-bottom:10px'>" . $this->details->item->anforderung . "&nbsp;</div>";

    echo "<div style='margin-bottom:5px'><strong>Zeit:</strong></div>";
    echo "<div style='margin-bottom:10px'>" . $this->details->item->zeit . "&nbsp;</div>";

    echo "<div style='margin-bottom:5px'><strong>Buchbarer Aufwand:</strong></div>";
    echo "<div style='margin-bottom:20px'>" . $this->details->item->aufwand . "&nbsp;</div>";
}

echo "<table class='market_details'>";

echo "<tr>
        <td class='lb'>Ansprechpartner:</td>
        <td class='value'>" . getAnsprechpartner($this->details->ansprechpartner, $this->details->item->titel) . "</td>
      </tr>";

if ($this->details->item->art == 1) {
    echo "<tr>
          <td class='lb'>Arbeitsgattung:</td>
          <td class='value'>" . $this->details->item->konto . "</td>
        </tr>";
} else {
    echo "<tr>
          <td class='lb'>Suche / Biete:</td>
          <td class='value'>" . ($this->details->item->richtung == 1 ? "Suche Stunden" : "Biete Stunden") . "</td>
        </tr>";
}

echo "<tr>
        <td class='lb'>Erstellt:</td>
        <td class='value'>" . JHTML::date($this->details->item->erstellt, "d.m.Y") . "</td>
      </tr>";

echo "</table>";


function getAnsprechpartner($person, $titel)
{
    $html = ZeitbankFrontendHelper::getEmailLink($person->vorname, $person->nachname, $person->email,
        'Marktplatz / ' . ZeitbankFrontendHelper::cropText($titel, 75));

    $telNos = "";
    if ($person->telefon && $person->telefon_frei) {
       $telNos = $person->telefon;
    }
    if ($person->handy && $person->handy_frei) {
       if ($telNos) {
          $telNos .= ", ";
       }
       $telNos .= $person->handy;
    }
    if ($telNos) {
       $html .= " (Telefon: " . $telNos . ")";
    }

    return $html;
}
