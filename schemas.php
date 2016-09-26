<?php session_start(); ?>
<?php require_once("./db.php"); ?>
<?php require_once("./functions.php"); ?>
<?php

$message = "";
$maanden  = array(
   "- maand -",
   "januari",
   "februari",
   "maart",
   "april",
   "mei",
   "juni",
   "juli",
   "augustus",
   "september",
   "oktober",
   "november",
   "december"
  );

// selecteer de reeds gespeelde wedstrijden
$select = "SELECT a.*,b.logo,c.logo FROM competitieschema AS a
           JOIN voetbalteams AS b ON (a.thuis_club = b.naam)
           JOIN voetbalteams AS C ON (a.uit_club = c.naam)
           WHERE datum <= CURDATE() ORDER BY datum ASC";

if ($result = mysqli_query($link,$select)) {

   $html_competities = "<div id=\"passed-events\" class=\"display-board\">";
   $html_competities .= "<h2>gespeelde wedstrijden</h2>";

   $html_competities .= "<div class=\"header\">";
   $html_competities .= "<div class=\"club-info\">stand</div>";
   $html_competities .= "<div class=\"update-stand\">update stand</div>";
   $html_competities .= "</div>";

   if (mysqli_num_rows($result)) {
      $html_competities .= "<div class=\"sub-board\">";
      while ($row = mysqli_fetch_row($result)) {
         $html_competities .= "<div class=\"line-wrapper\">";
         $html_competities .= "<div class=\"club-thuis\">";
         $html_competities .= $row[1];
         $html_competities .= "<img style=\"vertical-align:middle;\" width=\"24px\" src=\"" . $row[6] . "\">";
         $html_competities .= "</div>";
         $html_competities .= "<div class=\"uitslag\">";
         $html_competities .= (is_null($row[4])?" - ":$row[4]) . " - " . (is_null($row[5])?" - ":$row[5]);
         $html_competities .= "</div>";
         $html_competities .= "<div class=\"club-uit\">";
         $html_competities .= "<img style=\"vertical-align:middle;\" width=\"24px\" src=\"" . $row[7] . "\">";
         $html_competities .= $row[2];
         $html_competities .= "</div>";
         $html_competities .= "<div class=\"update-date-link\"><a href=\"./update_score.php?id=" . $row[0] . "\">" . $row[3] . "</a>";
         $html_competities .= "</div>";
         $html_competities .= "</div>";
      }
      $html_competities .= "</div>";
   }
   else {
      $html_competities .= melding("er zijn nog geen wedstrijden gespeeld.",2);
   }
   $html_competities .= "</div>";
}
else {
   $message = melding(mysqli_error($link),0);
}
// verzamel clubs die bestaan in de db om schemas in te voeren
$select = "SELECT * FROM voetbalteams ORDER BY naam ASC";

if ($result = mysqli_query($link,$select)) {
   // De te selecteren clubs
   $options_select = array();

   while ($row = mysqli_fetch_row($result)) {
      $options_select[] = $row[0];
   }
}
else {
   $message = melding("er ging iets fout met de verwerking van de gegevens.");
}
// verwerk formulier
if ($_POST["frm_schemas_submit"] && sizeof($options_select)>1) {

   if (empty($_POST["frm_thuis_club"]) ||
       empty($_POST["frm_uit_club"]) ||
       empty($_POST["frm_jaar"]) ||
       empty($_POST["frm_maand"]) ||
       empty($_POST["frm_dag"])) {

      $message = melding("niet alle velden zijn ingevuld!",1);
   }
   else {
      $thuis_club  = $_POST["frm_thuis_club"];
      $uit_club = $_POST["frm_uit_club"];
      $jaar = $_POST["frm_jaar"];
      $maand = $_POST["frm_maand"];
      $dag = $_POST["frm_dag"];
      $date = $jaar . "-" . $maand . "-" . $dag;

      if ($uit_club == $thuis_club) {
         $message = melding("een club kan niet tegen zichzelf spelen, wedstrijd niet ingepland!",1);
      }
      elseif (checkdate($maand,$dag,$jaar)==FALSE) {
         $message = melding("deze datum bestaat niet, wedstrijd niet ingepland",0);
      }
      else {
         // selecteer de wedstrijden op betreffende datum
         $select = "SELECT * FROM competitieschema
                    WHERE
                    (
                       thuis_club IN ('$thuis_club','$uit_club')
                       OR uit_club IN ('$thuis_club','$uit_club')
                    )
                    AND
                       datum = '$date'";

         if ($result=mysqli_query($link,$select)) {

            if (!mysqli_num_rows($result)) {

               // Geen match gevonden van gekozen clubs op betreffende datum.
               // ASSERT: vervolgens inplannen

               $insert = "INSERT INTO competitieschema (cid,thuis_club,uit_club,datum,thuis_score,uit_score)
                         VALUES(DEFAULT,'$thuis_club','$uit_club','$date',NULL,NULL)";

               if ($result = mysqli_query($link,$insert)) {
                  header("Location: ./voetbalcompetities.php");
               }
               else {
                  $message = melding(mysqli_error($link),1);
               }
            }
            else {
               $message = melding("&eacute;&eacute;n of beide clubs hebben die dag reeds een wedstrijd. Uw competitie is niet ingediend",1);
            }
         }
         else {
            $message = melding(mysqli_error($link),1);
         }
      }
   }
}
?>
<?php require("./header.php"); ?>

<?php if (sizeof($options_select)<2) {
   $message .=melding("er dienen minstens 2 clubs geregistreerd te zijn.",1);
}
?>
<article id="content">
<?php echo $message; ?>

<form id="select-event-form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">

   <div class="display-board">
     <h2>Plan uw wedstrijd</h2>
     <div class="header">
        <div class="thuis">thuis</div><div class="uit">uit</div><div class="datum">datum</div>
     </div>

     <div class="sub-board">

        <div class="thuis">
           <select name="frm_thuis_club">
              <option value=""> - thuis spelende club -</option>
              <?php
                 $team_in = (isset($_POST["frm_thuis_club"]))?$_POST["frm_thuis_club"]:"";

                 foreach ($options_select as $team) {
                    if ($team_in == $team) {
                       echo "<option selected=\"selected\" value=\"" . $team . "\">" . $team . "</option>";
                    }
                    else {
                       echo "<option value=\"" . $team . "\">" . $team . "</option>";
                    }
                 }
               ?>
          </select>
        </div>

        <div class="uit">
         <select name="frm_uit_club">
            <option value=""> - uit spelende club -</option>
            <?php
               $uit_club = (isset($_POST["frm_uit_club"]))?$_POST["frm_uit_club"]:"";
               foreach ($options_select as $club) {
                  if ($club == $uit_club) {
                     echo "<option selected=\"selected\" value=\"" . $club . "\">" . $club . "</option>";
                  }
                  else {
                     echo "<option value=\"" . $club . "\">" . $club . "</option>";
                  }
               }
            ?>
         </select>
      </div>

      <div class="date">
         <select name="frm_dag">
            <option value="">- dag -</option>
            <?php
               for($i=1;$i<32;$i++){
                  if ($i==$dag) {
                     echo "<option selected=\"selected\" value=\"" . $i ."\">" . $i . "</option>";
                  }
                  else {
                     echo "<option value=\"" . $i ."\">" . $i . "</option>";
                  }
               }
             ?>
         </select>

       <select name="frm_maand">
       <?php
          echo "<option value=\"\">" . $maanden[0] . "</option>";

          for($i=1; $i<13; $i++){

             if ($maanden[$i]==$maanden[$maand]) {
                echo "<option selected=\"selected\" value=\"" . ($i) ."\">" . $maanden[$i] . "</option>";
             }
             else {
                echo "<option value=\"" . ($i) ."\">" . $maanden[$i] . "</option>";
             }
          }
       ?>
       </select>

       <select name="frm_jaar">
          <option value="">- jaar -</option>
          <?php
             for($i = 2016; $i<2018; $i++)
             {
                if ($i==$jaar) {
                   echo "<option selected=\"selected\" value=\"" . $i ."\">" . $i . "</option>";
                }
                else {
                   echo "<option value=\"" . $i ."\">" . $i . "</option>";
                }
             }
          ?>
       </select>
    </div>
    <div class="submit">
       <input type="submit" name="frm_schemas_submit" value="Verstuur" />
    </div>
</div>
</div>
</form>
<?php echo  $html_competities; ?>
</article>
<?php require("./footer.php"); ?>
