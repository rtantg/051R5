<?php 
session_start();
require_once('./db.php');
require_once('./functions.php');

if (!$link) {
    $message = melding(mysqli_connect_errno() . ': ' . mysqli_connect_error(),1);
}
else {
    $maanden  = array(
       '- maand -',
       'januari',
       'februari',
       'maart',
       'april',
       'mei',
       'juni',
       'juli',
       'augustus',
       'september',
       'oktober',
       'november',
       'december'
      );

    // Deze pagina vereist een login voor toegang
    // Check if die er is, anders doorverwijzen naar login page
    // en een redirect terug.
    if (!isset($_SESSION['login'])) {
        $_SESSION['last_page'] = $_SERVER['PHP_SELF'];
        header('Location: ./login.php');
        exit;
    }
    $message = '';
    $html_competities = '';
    $options_select = array();

    // selecteer de reeds gespeelde wedstrijden
    $select1 = 'SELECT a.*,b.logo,c.logo FROM competitieschema AS a
                JOIN voetbalteams AS b 
                    ON (a.thuis_club = b.naam)
                JOIN voetbalteams AS c
                    ON (a.uit_club = c.naam)
                WHERE datum <= CURDATE() ORDER BY datum ASC';

    $result1 = mysqli_query($link,$select1);

    if ($result1 == FALSE) {
        $message = melding(mysqli_errno($link).': '.mysqli_error($link),0);
    }
    elseif (!mysqli_num_rows($result1)) {
        $message = melding('er zijn nog geen wedstrijden gespeeld.',2);
    }
    else {
        $html_competities = "<div id='passed-events' class='display-board'>";
        $html_competities .= '<h2>gespeelde wedstrijden</h2>';

        $html_competities .= "<div class='header'>";
        $html_competities .= "<div class='club-info'>uitslag</div>";
        $html_competities .= "<div class='update-stand'>update stand</div>";
        $html_competities .= '</div>';

        $html_competities .= "<div class='sub-board'>";

        while ($row = mysqli_fetch_row($result1)) {
            $html_competities .= "<div class='line-wrapper'>";
            $html_competities .= "<div class='club-thuis'>";
            $html_competities .= $row[1];
            $html_competities .= "<img style='vertical-align:middle;' width='24px' src='$row[6]'>";
            $html_competities .= '</div>';
            $html_competities .= "<div class='uitslag'>";
            $html_competities .= (is_null($row[4]) ? ' - ' : $row[4]) . ' - ' . (is_null($row[5]) ? ' - ' : $row[5]);
            $html_competities .= '</div>';
            $html_competities .= "<div class='club-uit'>";
            $html_competities .= "<img style='vertical-align:middle;' width='24px' src='$row[7]'>";
            $html_competities .= $row[2];
            $html_competities .= '</div>';
            $html_competities .= "<div class='update-date-link'><a href='./update_score.php?cid=$row[0]'>$row[3]</a>";
            $html_competities .= '</div>';
            $html_competities .= '</div>';
        }
        $html_competities .= '</div>';
        $html_competities .= '</div>';
    }

    // verzamel clubs die bestaan in de db voor de select controls op userform
    // om wedstrijden te plannen.
    $select2 = 'SELECT * FROM voetbalteams ORDER BY naam ASC';

    $result2 = mysqli_query($link,$select2);

    if ($result2 == FALSE) {
        $message = melding(mysqli_errno($link). ': ' . mysqli_error($link),0);
    }
    elseif(mysqli_num_rows($result2)<6){
        $message = melding('u kunt nog geen wedstrijden in plannen daar er geen voetbalteams zijn geregisteerd.',1);
    }
    else {
        while ($row = mysqli_fetch_row($result2)) {
            $options_select[] = $row[0];
        }
    }
    $thuis_club = isset($_POST['frm_thuis_club']) ? $_POST['frm_thuis_club'] : '';
    $uit_club   = isset($_POST['frm_uit_club']) ? $_POST['frm_uit_club'] : '';
    $jaar       = isset($_POST['frm_jaar']) ? $_POST['frm_jaar'] : 0;
    $maand      = isset($_POST['frm_maand']) ? $_POST['frm_maand'] : 0;
    $dag        = isset($_POST['frm_dag']) ? $_POST['frm_dag'] : 0;

    // verwerk formulier
    if (isset($_POST['frm_schemas_submit']) && sizeof($options_select)>1) {

        if (empty($thuis_club) || 
            empty($uit_club) ||
            empty($jaar) || 
            empty($maand) ||
            empty($dag)) {
            $message = melding('niet alle velden zijn ingevuld!',1);
        }
        elseif ($uit_club === $thuis_club) {
            $message = melding('een club kan niet tegen zichzelf spelen, wedstrijd niet ingepland!',1);
        }
        elseif (checkdate($maand,$dag,$jaar)==FALSE) {
            $message = melding('deze datum bestaat niet, wedstrijd niet ingepland',0);
        }
        else {
            // selecteer de wedstrijden op betreffende datum
            $date = "$jaar-$maand-$dag";
            $select3 = "SELECT * FROM competitieschema
                        WHERE (
                            thuis_club IN ('$thuis_club','$uit_club')
                        OR 
                            uit_club IN ('$thuis_club','$uit_club'))
                        AND
                        datum = '$date'";

            $result3=mysqli_query($link,$select3);

            if ($result3 == FALSE) {
                $message .= melding(mysqli_errno($link) . ': ' . mysqli_error($link),1);
            }
            elseif (mysqli_num_rows($result3)>0) {
                $message .= melding('&eacute;&eacute;n of beide clubs hebben die dag reeds een wedstrijd. Uw competitie is niet ingediend',1);
            }
            else {
                // Geen match gevonden van gekozen clubs op betreffende datum.
                // ASSERT: vervolgens inplannen

                $insert1 = "INSERT INTO competitieschema 
                            (cid,thuis_club,uit_club,datum,thuis_score,uit_score)
                            VALUES(DEFAULT,'$thuis_club','$uit_club','$date',NULL,NULL)";

                $result4 = mysqli_query($link,$insert1);

                if ($result4 == FALSE) {
                    $message .= melding(mysqli_errno($link) . ': ' .mysqli_error($link),0);
                }
                else {
                    header('Location: ./index.php');
                    exit;
                }
            }
        }
    }
} // end no database connection
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>Inzendopdracht 051R5</title>
<link rel='stylesheet' type='text/css' href='./css/style.css' />
</head>
<body>
<?php require('./header.php'); ?>

<article id='content'>
    <?php echo $message; ?>

    <?php if (count($options_select)>0) { ?>

    <form id='select-event-form' action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>

        <div class='display-board'>
            <h2>Plan uw wedstrijd</h2>
            <div class='header'>
            <div class='thuis'>thuis</div><div class='uit'>uit</div><div class='datum'>datum</div>
            </div>

            <div class='sub-board'>

                <div class='thuis'>
                    <select name='frm_thuis_club'>
                        <option value=''> - thuis spelende club -</option>
                        <?php

                        foreach ($options_select as $club) {
                            if ($club == $thuis_club ) {
                                echo "<option selected='selected' value='$club'>$club</option>";
                            }
                            else {
                                echo "<option value='$club'>$club</option>";
                            }
                        }
                        ?>
                    </select>
                    </div>

                <div class='uit'>
                    <select name='frm_uit_club'>
                        <option value=''> - uit spelende club -</option>
                        <?php

                        foreach ($options_select as $club) {
                            if ($club == $uit_club) {
                                echo "<option selected='selected' value='$club'>$club</option>";
                            }
                            else {
                                echo "<option value='$club'>$club</option>";
                            }
                        }
                        ?>
                    </select>
                    </div>

                <div class='date'>
                    <select name='frm_dag'>
                        <option value=''>- dag -</option>
                        <?php
                        for($i=1;$i<32;$i++){
                            if ($i==$dag) {
                                echo "<option selected='selected' value='$i'>$i</option>";
                            }
                            else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                        ?>
                        </select>

                    <select name='frm_maand'>
                        <?php
                        echo "<option value=''>$maanden[0]</option>";

                        for($i=1; $i<13; $i++){

                            if ($maanden[$i]==$maanden[$maand]) {
                                echo "<option selected='selected' value='$i'>$maanden[$i]</option>";
                            }
                            else {
                                echo "<option value='$i'>$maanden[$i]</option>";
                            }
                        }
                        ?>
                    </select>

                    <select name='frm_jaar'>
                        <option value=''>- jaar -</option>
                        <?php
                        for($i = 2016; $i<2018; $i++)
                        {
                            if ($i==$jaar) {
                                echo "<option selected='selected' value='$i'>$i</option>";
                            }
                            else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                        ?>
                        </select>
                    </div>
                    <div class='submit'>
                        <input type='submit' name='frm_schemas_submit' value='Verstuur' />
                    </div>
                </div>
            </div>
    </form>
    <?php } ?>
    <?php echo  $html_competities; ?>
</article>
</body>
</html>
<?php require('./footer.php'); ?>