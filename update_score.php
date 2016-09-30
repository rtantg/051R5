<?php
session_start();
require_once("./db.php");
require_once("./functions.php");

$select = (isset($_GET['id'])) ?
   "SELECT a.thuis_club,a.uit_club,b.logo,c.logo
    FROM competitieschema AS a
    JOIN voetbalteams AS b ON (a.thuis_club=b.naam)
    JOIN voetbalteams AS c ON (a.uit_club=c.naam)
    WHERE a.cid='" . $_GET['id'] . "'"
    : "";

$result = mysqli_query($link,$select);

if ($result==FALSE) {
   die(mysqli_error($link));
}
else {
   $row = mysqli_fetch_row($result);
   $thuis_club = $row[0];
   $thuis_logo = $row[2];
   $uit_club = $row[1];
   $uit_logo = $row[3];
}
if ($_POST['frm_score_update_submit'] ) {
   if(empty($_POST["frm_score_thuis"])) {
      $message .= melding("thuis score is leeg.",2);
   }
   if(empty($_POST["frm_score_uit"])) {
      $message .= melding("uit score is leeg.",2);
   }
   $thuis_score = $_POST["frm_score_thuis"];
   $uit_score = $_POST["frm_score_uit"];

   $update = "UPDATE competitieschema SET thuis_score=" . $thuis_score . ", " . "uit_score=" . $uit_score . " WHERE cid=" . $_GET['id'];

   if (mysqli_query($link,$update)) {
      header("Location: ./voetbalcompetities.php");
   }
   else {
      $message = mysqli_error($link);
      $message .= "<br>" . $update;
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
<?php require("./header.php"); ?>

<article id="content">

<div class="message"><?php echo $message; ?></div>

<form id="update-score-form" action="<?php echo $_server['PHP_SELF']; ?>" method="post">
   <div class="display-board">
      <h2>update uw score</h2>

      <div class="header">
         <div class="thuis">thuis</div>
         <div class="uitslag">&nbsp;</div>
         <div class="uit">uit</div>
      </div>

      <div class="sub-board">

      <div class="thuis">
         <?php echo $thuis_club; ?>
         <?php echo "<img src=\"" . $thuis_logo . "\">";  ?>
      </div>

      <div class="uitslag">
         <input size="3" name="frm_score_thuis" type="text">
         &nbsp;&nbsp;
         <input size="3" name="frm_score_uit" type="text">
      </div>

      <div class="uit">
         <?php echo "<img src=\"" . $uit_logo . "\">";  ?>
         <?php echo $uit_club; ?>
      </div>

      <div class="submit">
         <input name="frm_score_update_submit" type="submit" value="update uitslag">
      </div>
      </div>
   </div>
</form>

</article>

<?php require("./footer.php"); ?>
</body>
</html>
