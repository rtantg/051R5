<?php
session_start();
require_once("./db.php");
require_once("./functions.php");

// Het is niet toegestaan om handmatig update script uit te voeren
// daarom doorverwijzen naar schemas.php
if (!isset($_SESSION['login'])) {
    header('Location: ./schemas.php');
    exit;
}
// initialiseer variables
$message    = "";
$cid_found  = isset($_SESSION['cid_found'])?$_SESSION['cid_found']:"";
$cid        = isset($_GET['cid'])?$_GET['cid']:"";

if (empty($cid_found)){
    // cid_found not yet set => first time page load
    if (!empty($cid) && is_numeric($cid)) {

        // 1ste time: selecteer betreffende wedstrijd en haal logo's op
        $select = "SELECT a.thuis_club,a.uit_club,b.logo,c.logo
                    FROM competitieschema AS a
                    JOIN voetbalteams AS b 
                        ON (a.thuis_club=b.naam)
                    JOIN voetbalteams AS c 
                        ON (a.uit_club=c.naam)
                    WHERE a.cid='" . $cid . "'";

        $result = mysqli_query($link,$select);

        if ($result == FALSE) {
            $message = melding(mysqli_errno($link) . ": " . mysqli_error($link), 0);
        }
        elseif (!mysqli_num_rows ($result)){
            $message = melding("wedstrijd niet gevonden om uitslag door te voeren.",0);
        }
        else {
            $row = mysqli_fetch_row($result);
            $_SESSION['thuis_club'] = $row[0];
            $_SESSION['thuis_logo'] = $row[2];
            $_SESSION['uit_club'] = $row[1];
            $_SESSION['uit_logo'] = $row[3];
            $_SESSION['cid_found'] = $cid;
        }
    }
    else {
        $message = melding("invalid or no cid on url.", 0);
    }
}// eerste verzoek
$thuis_club = isset($_SESSION['thuis_club']) ? $_SESSION['thuis_club']:"";
$thuis_logo = isset($_SESSION['thuis_logo']) ? $_SESSION['thuis_logo']:"";
$uit_club   = isset($_SESSION['uit_club'])   ? $_SESSION['uit_club']:"";
$uit_logo   = isset($_SESSION['uit_logo'])   ? $_SESSION['uit_logo']:"";
$thuis_score = isset($_POST['frm_thuis_score'])?$_POST['frm_thuis_score']:NULL;
$uit_score   = isset($_POST['frm_uit_score'])?$_POST['frm_uit_score']:NULL;

// verwerk formulier on submit
if (isset($_POST['frm_score_update_submit']) ) {

    if(is_null($thuis_score) || is_null($uit_score)) {
        $message = melding("uitslag niet volledig ingevuld.",2);
    }
    else {
        $update = "UPDATE competitieschema " . 
                  "SET thuis_score=" . $thuis_score . ", " . "uit_score=" . $uit_score . 
                  " WHERE cid=" . $cid_found;

        unset($_SESSION['cid_found']);
        unset($_SESSION['thuis_club']);
        unset($_SESSION['thuis_logo']);
        unset($_SESSION['uit_club']);
        unset($_SESSION['uit_logo']);

        if (mysqli_query($link,$update)) {
            header("Location: ./index.php");
            exit;
        }
        else {
            $message = melding(mysqli_errno($link) . ": " . mysqli_error($link),0);
        }     
    }
}// if form submit
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Inzendopdracht 051R5</title>
<link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>
<?php require("./header.php"); ?>

<article id="content">

<?php 
echo $message;
if (!empty($thuis_club) && !empty($uit_club)) {
?>
<form id="update-score-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
   <div class="display-board">
      <h2>update uw score</h2>

      <div class="header">
         <div class="thuis">thuis</div>
         <div class="uitslag">&nbsp;</div>
         <div class="uit">uit</div>
      </div>

      <div class="sub-board">

      <div class="thuis">
         <?php 
         echo $thuis_club; 
         echo "<img src=\"" . $thuis_logo . "\">";  
         ?>
      </div>

      <div class="uitslag">
         <input size="3" name="frm_thuis_score" type="text">
         &nbsp;&nbsp;
         <input size="3" name="frm_uit_score" type="text">
      </div>

      <div class="uit">
         <?php 
         echo "<img src=\"" . $uit_logo . "\">";
         echo $uit_club; 
         ?>
      </div>

      <div class="submit">
         <input name="frm_score_update_submit" type="submit" value="update uitslag">
      </div>
      </div>
   </div>
</form>
<?php } ?>
</article>
<?php require("./footer.php"); ?>
</body>
</html>