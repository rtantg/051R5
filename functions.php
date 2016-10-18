<?php
function melding($str,$i) {
  switch ($i) {
      case 0:
         return "<div class='error_melding'>Error: " . nl2br($str) . '</div>';
      case 1:
         return "<div class='waarschuwing_melding'>Waarschuwing: " . nl2br($str) . '</div>';
      case 2:
         return "<div class='melding_melding'>" . nl2br($str) . "</div>";
   }
}
// callback functie om huidige stand te sorteren
function vergelijk_score($x, $y) {
   if ($x['punten']==$y['punten']) {
      // bij gelijke score sorteer op doelpunten
      return ($y['doelpunten']-$x['doelpunten']);
   }
   return ($y['punten']-$x['punten']);
}