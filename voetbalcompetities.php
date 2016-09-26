<?php session_start(); ?>
<?php require_once("./db.php"); ?>
<?php require_once("./functions.php"); ?>
<?php
// maak query voor reeds gespeelde wedstrijden *EN* die een uitslag hebben.
//   verkrijg ook logo's 
$select = "SELECT a.*, b.logo, c.logo
           FROM competitieschema AS a
           JOIN 
              voetbalteams AS b ON (a.thuis_club=b.naam)
           JOIN 
              voetbalteams AS c ON (a.uit_club=c.naam)
           WHERE 
              a.datum <= CURDATE()
           AND 
              (a.thuis_score IS NOT NULL AND uit_score IS NOT NULL) 
           ORDER BY 
              a.datum";

if ($result = mysqli_query($link,$select)) {
   // collect actuele stand gespeelde wedstrijden
   $stand = array();
   $uitslagen = array();

   while($row=mysqli_fetch_row($result)){
      $thuis = $row[1];
      $uit = $row[2];

      $uitslagen[$row[3]][] = array( 
         "thuis" => array ("naam" => $row[1], "score" => $row[4], "logo" => $row[7]),
         "uit"   => array ("naam" => $row[2], "score" => $row[5], "logo" => $row[6])
         );

      $stand[$thuis]["gespeeld"] += 1;
      $stand[$uit]["gespeeld"]   += 1;

      $stand[$thuis]["doelpunten"] += $row[4];
      $stand[$thuis]["tegen"]      += $row[5];

      $stand[$uit]["doelpunten"] += $row[5];
      $stand[$uit]["tegen"]      += $row[4];

      $stand[$thuis]["gewonnen"] += ($row[4]>$row[5])?1:0;
      $stand[$thuis]["gelijk"]   += ($row[4]==$row[5])?1:0;
      $stand[$thuis]["verloren"] += ($row[4]<$row[5])?1:0;

      $stand[$uit]["gewonnen"] += ($row[5]>$row[4])?1:0;
      $stand[$uit]["gelijk"]   += ($row[5]==$row[4])?1:0;
      $stand[$uit]["verloren"] += ($row[5]<$row[4])?1:0;

      if ($row[4]>$row[5]) {
         $stand[$thuis]["punten"] += 3;
         $stand[$uit]["punten"] += 0; // Als dit element nog leeg is dat willen we een 0 en geen null
      }
      else if ($row[4]<$row[5]) {
         $stand[$thuis]["punten"] += 0; 
         $stand[$uit]["punten"] += 3;
      }
      else if ($row[4]==$row[5]) {
         $stand[$thuis]["punten"] += 1;
         $stand[$uit]["punten"] += 1;
      }
      $stand[$thuis]["logo"] = $row[6];
      $stand[$uit]["logo"] = $row[7];
   }
   $html_uitslagen .= "<div id=\"uitslagen\" class=\"display-board\">";
   $html_uitslagen .= "<h2>uitslagen</h2>";

   $html_uitslagen .= "<div class=\"header\">";
   $html_uitslagen .= "<div class=\"thuis\">thuis</div>";
   $html_uitslagen .= "<div class=\"uit\">uit</div>";
   $html_uitslagen .= "</div>";

   if (!mysqli_num_rows($result)) {
      $html_uitslagen .= melding("er zijn nog geen uitslagen bekend.",2);
   }
   foreach ($uitslagen as $date => $wedstrijden) {

      $html_uitslagen .= "<div class=\"sub-board\">";
      $html_uitslagen .= "<h3>" . $date . "</h3>";

      foreach($wedstrijden as $wedstrijd) {

         $html_uitslagen .= "<div class=\"line-wrapper\">";

         $html_uitslagen .= "<div class=\"thuis\">"; 
         $html_uitslagen .= "<span>" . $wedstrijd["thuis"]["naam"] . "</span>";
         $html_uitslagen .= "&nbsp;&nbsp"; 
         $html_uitslagen .= "<img width=\"30px\" src=\"" . $wedstrijd["thuis"]["logo"] . "\">";
         $html_uitslagen .= "</div>"; 

         $html_uitslagen .= "<div class=\"uitslag\">";
         $html_uitslagen .= $wedstrijd["thuis"]["score"];
         $html_uitslagen .= " - ";
         $html_uitslagen .= $wedstrijd["uit"]["score"];
         $html_uitslagen .= "</div>";

         $html_uitslagen .= "<div class=\"uit\">"; 
         $html_uitslagen .= "<img width=\"30px\" src=\"" . $wedstrijd["uit"]["logo"] . "\">";
         $html_uitslagen .= "&nbsp;&nbsp"; 
         $html_uitslagen .= "<span>" . $wedstrijd["uit"]["naam"] . "</span>";
         $html_uitslagen .= "</div>"; 

         $html_uitslagen .= "</div>";
      }
      $html_uitslagen .= "</div>";
   }
   $html_uitslagen .= "</div>";
   // haal alle nog te spelen wedstijden uit de db
   $select = "SELECT  a.*, b.logo,c.logo
           FROM competitieschema AS a
           JOIN voetbalteams AS b
           ON
              (a.thuis_club=b.naam)
           JOIN voetbalteams AS c
           ON
              (a.uit_club=c.naam)
           WHERE
              a.datum > CURDATE()
           ORDER BY a.datum";

   if ($result = mysqli_query($link,$select)) {

      $nog_te_spelen = array();

      while($row=mysqli_fetch_row($result)){

         $nog_te_spelen[$row[3]][] = array(
            "club_thuis" => $row[1],
            "club_uit" => $row[2],
            "logo_thuis" => $row[6],
            "logo_uit" => $row[7]
            );
      }
      $html_nog_te_spelen .= "<div id=\"programma\" class=\"display-board\">";
      $html_nog_te_spelen .= "<h2>programma</h2>";
      $html_nog_te_spelen .= "<div class=\"header\">";
      $html_nog_te_spelen .= "<div class=\"thuis\">thuis</div>";
      $html_nog_te_spelen .= "<div class=\"uit\">uit</div>";
      $html_nog_te_spelen .= "</div>";

      if (!mysqli_num_rows($result)) {
         $html_nog_te_spelen .= melding("er is nog geen programma bekend.",2);
      }
      foreach($nog_te_spelen as $date => $wedstrijden ) {

         $html_nog_te_spelen .= "<div class=\"sub-board\">";
         $html_nog_te_spelen .= "<h3>" .$date ."</h3>";

         foreach($wedstrijden as $wedstrijd) {

            $html_nog_te_spelen .= "<div class=\"line-wrapper\">";

            $html_nog_te_spelen .= "<div class=\"thuis\">";
            $html_nog_te_spelen .= $wedstrijd["club_thuis"];
            $html_nog_te_spelen .= "&nbsp;&nbsp;";
            $html_nog_te_spelen .= "<img width=\"30px\"; src=\"" . $wedstrijd["logo_thuis"] . "\">";
            $html_nog_te_spelen .= "</div>";

            $html_nog_te_spelen .= "<div class=\"uitslag\">&nbsp;</div>";

            $html_nog_te_spelen .= "<div class=\"uit\">";
            $html_nog_te_spelen .= "<img width=\"30px\"; src=\"" . $wedstrijd["logo_uit"] . "\">";
            $html_nog_te_spelen .= "&nbsp;&nbsp;";
            $html_nog_te_spelen .= $wedstrijd["club_uit"];
            $html_nog_te_spelen .= "</div>";

            $html_nog_te_spelen .= "</div>";
         }
         $html_nog_te_spelen .= "</div>";
      }
      $html_nog_te_spelen .= "</div>";
   }
   else {
      $message = melding("er ging iets fout bij het verwerken van de gegevens.",0);
   }
   $html_stand .= "<div id=\"stand\" class=\"display-board\">";
   $html_stand .= "<h2>stand</h2>";

   $html_stand .= "<div class=\"header\">";
   $html_stand .= "<div class=\"club\">club</div>";
   $html_stand .= "<div class=\"gespeeld\">gespeeld</div>";
   $html_stand .= "<div class=\"doelpunten\">doelpunten</div>";
   $html_stand .= "<div class=\"gewonnen\">w</div>";
   $html_stand .= "<div class=\"gelijk\">g</div>";
   $html_stand .= "<div class=\"verloren\">v</div>";
   $html_stand .= "<div class=\"punten\">punten</div>";
   $html_stand .= "</div>";

   uasort($stand,"vergelijk_score");

   if (empty($stand)) {
      $html_stand .= melding("er is nog geen stand bekend.",2);
   }
   $html_stand .= "<div class=\"sub-board\">";
   foreach($stand as $club => $info) {

      $html_stand .= "<div class=\"line-wrapper\">";

      $html_stand .= "<div class=\"club\">";
      $html_stand .= $club;
      $html_stand .= "&nbsp;&nbsp;";
      $html_stand .= "<img width=\"30px\" src=\"" . $stand[$club]["logo"] . "\">";
      $html_stand .= "</div>";

      $html_stand .= "<div class=\"gespeeld\">" . $stand[$club]["gespeeld"] . "</div>";

      $html_stand .= "<div class=\"doelpunten\">";
      $html_stand .= ($stand[$club]["doelpunten"]>0)? "+":"";
      $html_stand .= $stand[$club]["doelpunten"];
      $html_stand .= "</div>";

      $html_stand .= "<div class=\"tegen\">";
      $html_stand .= ($stand[$club]["tegen"]>0)? "-":"";
      $html_stand .= $stand[$club]["tegen"];
      $html_stand .= "</div>";

      $html_stand .= "<div class=\"saldo\">";
      $html_stand .= "(" . ($stand[$club]["doelpunten"] - $stand[$club]["tegen"]) . ")";
      $html_stand .= "</div>";

      $html_stand .= "<div class=\"gewonnen\">" .$stand[$club]["gewonnen"] . "</div>";
      $html_stand .= "<div class=\"gelijk\">" . $stand[$club]["gelijk"] . "</div>";
      $html_stand .= "<div class=\"verloren\">" . $stand[$club]["verloren"] . "</div>";

      $html_stand .= "<div class=\"punten\">" . $stand[$club]["punten"] . "</div>";

      $html_stand .= "</div>";
   }
   $html_stand .= "</div>";
   $html_stand .= "</div>";
}
else {
   $message = melding("er ging iets fout bij het verwerken van de gegevens.",0);
}
?>
<?php require_once("./header.php"); ?>

<article id="content">
<?php
   echo $html_stand;
   echo $html_uitslagen;
   echo $html_nog_te_spelen;
?>
</article>

<?php require_once("./footer.php");
