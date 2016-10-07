<?php
session_start();
require_once("./functions.php");
require_once("./db.php");
require_once("./globals.php");

$message = "";
$clubs_in_db = array();
$html_clubs_in_db = "";

// See which clubs are in the database. they don"t need to be entered again 
// (not a select option).

$select = "SELECT * FROM voetbalteams ORDER BY naam ASC";

$result = mysqli_query($link,$select);

if ($result == FALSE ) {
    $message = melding(mysqli_errno($link) .": " . mysqli_error($link),0);
}
else {
    // vull array met clubs die reeds in db zijn
    // later nodig om verschil array te creeren
    while ($row=mysqli_fetch_row($result)) {
        $clubs_in_db[$row[0]] = array (
            'locatie' => $row[1],
            'stadion' => $row[2],
            'opgericht' => $row[3],
            'website' => $row[4],
            'logo' => $row[5]
            );
    }
}
// Maak een lijst van clubs die nog niet zijn ingevoerd (in de db).
// voor select options vanuit form
$clubs_niet_in_db = !empty($clubs_in_db) ? array_diff_key($clubs,$clubs_in_db) : $clubs;

if (isset($_GET['i']) && $_GET['i'] == 'all') {
    $insert  = "INSERT INTO voetbalteams (naam,locatie,stadion,opgericht,website,logo)\nVALUES\n"; 
    $i=0;
    foreach ($clubs_niet_in_db as $club => $info) {
       $insert .= "('$club','$info[locatie]','$info[stadion]','$info[opgericht]','$info[website]','$info[logo]')";
       $i++;
       if ($i < sizeof($clubs_niet_in_db)){
           $insert .= ',';
       }
    }
    $result2 = mysqli_query($link, $insert);
    if ($result2==FALSE) {
        $message = melding(mysqli_errno($link) .  ': ' . mysqli_error($link),0);
    }
    else {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
else{
    if (sizeof($clubs_in_db) > 0) {
        // Genereer html voor lijst met clubs in db ter info
        $html_clubs_in_db = "<div id=\"registered-teams\" class=\"display-board\">";
        $html_clubs_in_db .= "<h2>reeds toegevoegde clubs</h2>";

        $html_clubs_in_db .= "<div class=\"header\">";
        $html_clubs_in_db .= "<div class=\"club\">naam</div>";
        $html_clubs_in_db .= "<div class=\"founded\">opgericht</div>";
        $html_clubs_in_db .= "<div class=\"stadion\">stadion</div>";
        $html_clubs_in_db .= "<div class=\"location\">plaats</div>";
        $html_clubs_in_db .= "<div class=\"website\">website</div>";
        $html_clubs_in_db .= "</div>";

        $html_clubs_in_db .= "<div class=\"sub-board\">";

        foreach ($clubs_in_db as $club => $info) {

            $html_clubs_in_db .= "<div class=\"line-wrapper\">";
            $html_clubs_in_db .= "<div class=\"club\"><img width=\"18px\" src=\"" . $info['logo'] . "\">";
            $html_clubs_in_db .= "&nbsp;&nbsp;";
            $html_clubs_in_db .= $club;
            $html_clubs_in_db .= "</div>";
            $html_clubs_in_db .= "<div class=\"founded\">" . $info['opgericht'] . "</div>";;
            $html_clubs_in_db .= "<div class=\"stadion\">" . $info['stadion'] . "</div>";;
            $html_clubs_in_db .= "<div class=\"location\">" . $info['locatie'] . "</div>";;
            $html_clubs_in_db .= "<div class=\"website\"><a href=\"http://" . $info['website'] . "\">". $info['website'] . "</a></div>";;
            $html_clubs_in_db .= "</div>";
        }
        $html_clubs_in_db .= "</div>";
        $html_clubs_in_db .= "</div>";
    }
    else {
        $message = melding('nog geen clubs geregistreerd.',2);
    }
    if (isset($_POST["frm_club_submit"])) {

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
            $message = melding(mysqli_errno($link) . ": " . mysqli_error($link),0);
         }
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
    
    <?php if (count($clubs_niet_in_db) == 0) { 
        $message .= melding('alle clubs zijn geregistreerd.',2);
    }
    
    echo $message;

    if (count($clubs_niet_in_db) > 0) { ?>

        <form id="select-club-form" action=<?php echo $_SERVER["PHP_SELF"] ?> method="post">

            <div class="display-board">

                <h2>voeg club toe</h2>
                <div class="sub-board">
                    <div class="select-club">
                        <select style="width:200px;" id="frm_club_name" name="frm_club_name">
                            <option value=""> - Selecteer club - </option>
                            <?php
                            foreach ($clubs_niet_in_db as $club => $info) {
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
            <div id='upload_all'><a href='./teams.php?i=all'>Upload alle clubs</a></div>
        </form>
    <?php }?>

    <?php echo $html_clubs_in_db; ?>

</article>

    <?php require_once("./footer.php"); ?>
</body>
</html>
