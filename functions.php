<?php
function melding($str,$i) {

   switch ($i) {
      case 0:
         return "<div class=\"error_melding\">Error: " . nl2br($str) . "</div>";
      case 1:
         return "<div class=\"waarschuwing_melding\">Waarschuwing: " . nl2br($str) . "</div>";
      case 2:
         return "<div class=\"melding_melding\">Melding: " . nl2br($str) . "</div>";
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
function check_name($str) {
   return preg_match('/[[:alpha:]]+/',$str);
}
function check_username($str) {
   return preg_match('/[[:alpha:]]{3,20}/', $str);
}
function check_password($str) {
   return preg_match('/[!-~]{8,32}/', $str);
}
function check_email($str) {
   return preg_match('/[[:alpha:]]{2,}@[[:alpha:]]{2,}\.nl/',$str);
}
?>
