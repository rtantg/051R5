<?php
require_once("./db.php");

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
$last = count($clubs);
$i = 0;
$insert  = "INSERT INTO voetbalteams (naam,locatie,stadion,opgericht,website,logo)\nVALUES\n"; 

foreach ($clubs as $club => $info) {
   $insert .= "('$club','$info[locatie]','$info[stadion]','$info[opgericht]','$info[website]','$info[logo]')";
   $i++;
   $insert .= ($last==$i)? ";":",";
   $insert .= "\n";
}

?>
<!DOCTYE HTML>
<HEAD><TITLE>Vul de teams tabel</TITLE>
    <meta charset="utf-8">
</HEAD>
<BODY>

<?php 
  $select ="SELECT * FROM voetbalteams";
  if ($result = mysqli_query($link,$select)) {
    if (mysqli_num_rows($result) > 0) {
        echo "Tabel 'teams' reeds gevuld.";
    }
    elseif (mysqli_query($link,$insert) == FALSE) {
        echo mysqli_error($link);
    }
    else {
        echo "Tabel 'teams' gevuld':<br><br>";
        echo nl2br($insert);
    }
  }
  else {
    echo mysqli_error($link);
  }
  
?>

</BODY>
</html>

