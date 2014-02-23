<?php

echo "<div style='margin-bottom:20px'><strong>"
      .$this->details->item->titel."</strong></div>";

echo "<div style='margin-bottom:20px'>"
      .$this->details->item->beschreibung."</div>";

echo "<table class='market_details'>";

echo "<tr>
        <td class='lb'>Ansprechpartner:</td>
        <td class='value'>".getAnsprechpartner($this->details->ansprechpartner)."</td>
      </tr>";

echo "<tr>
        <td class='lb'>Anbieter:</td>
        <td class='value'>".($this->details->item->anbieter == 2 ? "Privat" : "Bereich " . $this->details->item->anbieter_name)."</td>
      </tr>";

echo "<tr>
        <td class='lb'>Erstellt:</td>
        <td class='value'>".JHTML::date($this->details->item->erstellt,"d.m.Y")."</td>
      </tr>";

echo "</table>";


function getAnsprechpartner($person) {
  $name = $person->vorname . " " . $person->nachname;
  $linkName = $name;

  if (substr($person->email, 0, 11) != "kein.email.") {
    $linkName = '<a href="mailto:'.$person->email.'?subject=Marktplatz&body=Liebe/Lieber '.$person->vorname.'">'.$name.'</a>'; 
  }
  
  $telefon = "";
  if (!empty($person->telefon) && $person->telefon_frei) {
    $telefon = " (Telefon: ".$person->telefon.")";
  }
  else if (!empty($person->handy) && $person->handy_frei) {
    $telefon = " (Telefon: ".$person->telefon.")";
  }
  
  return $linkName . $telefon;
}