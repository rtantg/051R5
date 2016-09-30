<?php
session_start();
require_once("./functions.php");
require_once("./db.php");

$message = "";

$clubs = array(

   "Ado Den Haag" => array(
      "locatie" => "Den Haag",
      "opgericht" => "1905-01-01",
      "stadion" => "Kyocera stadion",
      "website" => "www.adodenhaag.nl",
      "logo" => "./images/ado-den-haag.png"
   ),

   "Ajax" => array(
      "locatie" => "Amstedam",
      "opgericht" => "1900-03-18",
      "stadion" => "Amsterdam Arena",
      "website" => "www.ajax.nl",
      "logo" => "./images/ajax.png"
   ),

   "AZ" => array(
       "locatie" => "Alkmaar",
      "opgericht" => "1967-05-10",
      "stadion" => "AFAS stadion",
      "website" => "www.az.nl",
      "logo" => "./images/az.png"
   ),

   "Excelsior" => array(
      "locatie" => "Rotterdam",
      "opgericht" => "1902-07-23",
      "stadion" => "Woudestein",
      "website" => "www.sbvexcelsior.nl",
      "logo" => "./images/excelsior.png"
   ),

   "FC Groningen" => array(
      "locatie" => "Groningen",
      "opgericht" => "1971-07-16",
      "stadion" => "Euroborg",
      "website" => "www.fcgroningen.nl",
      "logo" => "./images/fc-groningen.png"
   ),

   "FC Twente" => array(
      "locatie" => "Enschede",
      "opgericht" => "1965-07-01",
      "stadion" => "Grolsch Veste",
      "website" => "www.fctwente.nl",
      "logo" => "./images/fc-twente.png"
   ),

   "FC Utrecht" => array(
      "locatie" => "Utrecht",
      "opgericht" => "1970-07-01",
      "stadion" => "stadion Galgenwaard",
      "website" => "www.fcutrecht.nl",
      "logo" => "./images/fc-utrecht.png"
   ),

   "Feyenoord" => array(
      "locatie" => "Rotterdam",
      "opgericht" => "1908-07-19",
      "stadion" => "De Kuip",
      "website" => "www.feyenoord.nl",
      "logo" => "./images/feyenoord.png"
   ),

   "Go Ahead Eagles" => array(
      "locatie" => "Deventer",
      "opgericht" => "1902-12-02",
      "stadion" => "De Adelaarshorst",
      "website" => "www.ga-eagles.nl",
      "logo" => "./images/go-ahead-eagles.png"
   ),

   "Heracles Almelo" => array(
      "locatie" => "Almelo",
      "opgericht" => "1903-05-03",
      "stadion" => "Polman stadion",
      "website" => "www.heracles.nl",
      "logo" => "./images/heracles-almelo.png"
   ),

   "NEC" => array(
      "locatie" => "Nijmegen",
      "opgericht" => "1900-11-15",
      "stadion" => "De Goffert",
      "website" => "www.nec-nijmegen.nl",
      "logo" => "./images/nec.png"
   ),

   "PEC Zwolle" => array(
      "locatie" => "Zwolle",
      "opgericht" => "1910-06-12",
      "stadion" => "IJseldelta stadion",
      "website" => "www.peczwolle.nl",
      "logo" => "./images/pec-zwolle.png"
   ),

   "PSV" => array(
      "locatie" => "Eindhoven",
      "opgericht" => "1913-08-31",
      "stadion" => "Philips stadion",
      "website" => "www.psv.nl",
      "logo" => "./images/psv.png"
   ),

   "Roda JC" => array(
      "locatie" => "Kerkrade",
      "opgericht" => "1962-06-27",
      "stadion" => "Parkstad Limburg stadion",
      "website" => "www.rodajc.nl",
      "logo" => "./images/roda-jc.png"
   ),

   "SC Heerenveen" => array(
      "locatie" => "Heerenveen",
      "opgericht" => "1920-07-20",
      "stadion" => "Abe Lenstra stadion",
      "website" => "www.sc-heerenveen.nl",
      "logo" => "./images/sc-heerenveen.png"
   ),

   "Sparta" => array(
      "locatie" => "Rotterdam",
      "opgericht" => "1888-04-01",
      "stadion" => "Het Kasteel",
      "website" => "www.sparta-rotterdam.nl",
      "logo" => "./images/sparta.png"
   ),

   "Vitesse" => array(
      "locatie" => "Arnhem",
      "opgericht" => "1892-05-14",
      "stadion" => "Gelredome",
      "website" => "www.vitesse.nl",
      "logo" => "./images/vitesse.png"
   ),

   "Willem II" => array(
      "locatie" => "Tilburg",
      "opgericht" => "1896-08-12",
      "stadion" => "Willem II stadion",
      "website" => "www.willem-ii.nl",
      "logo" => "./images/willemII.png"
   )
);
// See which clubs are in the database. they don"t need to be entered again 
// (not a select option.

$clubs_ind_db = array();

$select = "SELECT * FROM voetbalteams ORDER BY naam ASC";

if ($result = mysqli_query($link,$select) ) {

   $html_clubs_in_db = "<div id=\"registered-teams\" class=\"display-board\">";
   $html_clubs_in_db .= "<h2>reeds toegevoegde clubs</h2>";

   $html_clubs_in_db .= "<div class=\"header\">";
   $html_clubs_in_db .= "<div class=\"club\">naam</div>";
   $html_clubs_in_db .= "<div class=\"founded\">opgericht</div>";
   $html_clubs_in_db .= "<div class=\"stadion\">stadion</div>";
   $html_clubs_in_db .= "<div class=\"location\">plaats</div>";
   $html_clubs_in_db .= "<div class=\"website\">website</div>";
   $html_clubs_in_db .= "</div>";

   if(mysqli_num_rows($result)) {
      $html_clubs_in_db .= "<div class=\"sub-board\">";
      while ($row=mysqli_fetch_row($result)) {

         $club = $row[0];

      // Verzamel clubs die reeds in de database bestaan.
         $clubs_in_db[$club]["opgericht"] = $row[1];
         $clubs_in_db[$club]["locatie"] = $row[2];
         $clubs_in_db[$club]["stadion"] = $row[3];
         $clubs_in_db[$club]["website"] = $row[4];
         $clubs_in_db[$club]["logo"] = $row[5];

         $html_clubs_in_db .= "<div class=\"line-wrapper\">";
         $html_clubs_in_db .= "<div class=\"club\"><img width=\"18px\" src=\"" . $row[5] . "\">";
         $html_clubs_in_db .= "&nbsp;&nbsp;";
         $html_clubs_in_db .= $row[0];
         $html_clubs_in_db .= "</div>";
         $html_clubs_in_db .= "<div class=\"founded\">" . $row[3] . "</div>";;
         $html_clubs_in_db .= "<div class=\"stadion\">" . $row[2] . "</div>";;
         $html_clubs_in_db .= "<div class=\"location\">" . $row[1] . "</div>";;
         $html_clubs_in_db .= "<div class=\"website\"><a href=\"http://" . $row[4] . "\">". $row[4] . "</a></div>";;
         $html_clubs_in_db .= "</div>";
      }
      $html_clubs_in_db .= "</div>";
   }
   else {
      $html_clubs_in_db .= melding("er zijn nog geen clubs toegevoegd.",2);
   }
   $html_clubs_in_db .= "</div>";
   //  Maak een lijst van clubs die nog niet zijn ingevoerd (in de db).
   $club_select_list = (!empty($clubs_in_db))?array_diff_key($clubs,$clubs_in_db):$clubs;
}
else {
   $message = mysqli_error($link);
}

if ($_POST["frm_club_submit"]) {

   if (empty($_POST["frm_club_name"])) {
      $message = melding("selecteer een club.",1);
   }
   else {
     // process arguments
     $club = html_entity_decode($_POST["frm_club_name"]);

     $location    = $clubs[$club]["locatie"];
     $stadion     = $clubs[$club]["stadion"];
     $established = $clubs[$club]["opgericht"];
     $website     = $clubs[$club]["website"];
     $logo        = $clubs[$club]["logo"];

     $insert = "INSERT INTO voetbalteams (naam,locatie,stadion,opgericht,website,logo) 
                VALUES('$club','$location','$stadion','$established','$website','$logo')";

     if (mysqli_query($link,$insert)) {
        header("Location: ./teams.php");
     }
     else {
        $message = mysqli_errno($link) . ": " . mysqli_error($link);
     }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Inzendopdracht 051R5</title>
<link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>

<?php require_once("./header.php"); ?>

<article id="content">

<?php echo $message; ?>

<?php echo $html_clubs_in_db; ?>
<form id="select-club-form" action=<?php echo $_SERVER["PHP_SELF"] ?> method="post">

   <div class="display-board">
      <h2>voeg club toe</h2>

      <div class="sub-board">

         <div class="select-club">
            <select style="width:200px;" id="frm_club_name" name="frm_club_name">
               <option value=""> - Selecteer club - </option>
               <?php
                  foreach ($club_select_list as $club => $info) {
                     echo "<option value=\"" . htmlentities($club) ."\">" . htmlentities($club) . "</option>";
                   }
               ?>
            </select>
         </div>

         <div class="submit" >
             <input style="width:200px" type="submit" name="frm_club_submit" value="verstuur">
         </div>
      </div>
   </div>

</form>
</article>
<?php require_once("./footer.php"); ?>
</body>
</html>
