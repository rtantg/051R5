<?php
session_start();
require_once('./db.php');
require_once('./functions.php');

$datum = '';
$result = '';
$query = '';
$update_form = '';
$message  = '';
$_post_keys = array();

// login check
if (!isset($_SESSION['username'])) {
    header('Location: ./index.php');
    exit;
}
if (isset($_GET['d']) && !empty($_GET['d'])) {
    $datum = $_GET['d'];
}
elseif (isset($_POST['frm_update_score_datum']) && !empty($_POST['frm_update_score_datum'])) {
    $datum = $_POST['frm_update_score_datum'];
}
else {
    header('Location: ./index.php');
    exit;
 }
// initialiseer variables

$select = "SELECT a.*, b.logo, c.logo
            FROM competitieschema AS a
            JOIN voetbalteams AS b 
                ON (a.thuis_club=b.naam)
            JOIN voetbalteams AS c 
                ON (a.uit_club=c.naam)
            WHERE 
                a.thuis_score IS NULL 
            AND 
                a.uit_score IS NULL
            AND
                a.datum='$datum'";

$result = mysqli_query($link,$select);

if ($result == FALSE) {
    $message .= melding(mysqli_errno($link) . ": " . mysqli_error($link), 0);
}
if (mysqli_num_rows($result)==0) {
    header('Location: ./index.php');
    exit;
}
while($row = mysqli_fetch_row($result)) {
    // to check $_POST later
    $_post_keys[] = array(
        'cid' => $row[0],
        'thuis' => "frm_thuis_score_$row[0]",
        'uit'   => "frm_uit_score_$row[0]"
    );
}
// verwerk formulier on submit
if (isset($_POST['frm_score_update_submit']) ) {
    $message = '';
    $valid_input = false;
    // check if all is filled in
    foreach ($_post_keys as $key) {
        
        if (isset($_POST[$key['thuis']]) && isset($_POST[$key['uit']])) {
            
            if (is_numeric($_POST[$key['thuis']]) && is_numeric($_POST[$key['uit']])) {
            
                $query  = "UPDATE competitieschema ";
                $query .= "SET thuis_score='" . $_POST[$key['thuis']] . "', ";
                $query .= "uit_score='" . $_POST[$key['uit']] . "' ";
                $query .= "WHERE cid='$key[cid]'";
 
                if (mysqli_query($link,$query)==false) { 
                    $message .= melding(mysqli_errno($link) . ": " . mysqli_error($link),0);
                }
                $valid_input = true;
            }
            else {
                $message = melding('geen juiste invoer van score.', 2);
            }
        }
    }
    if ($valid_input) {
        $message = '';
    }
}// if form submit
$result = mysqli_query($link,$select);

if ($result == FALSE) {
    $message .= melding(mysqli_errno($link) . ": " . mysqli_error($link), 0);
}
if (mysqli_num_rows($result)==0) {
    header('Location: ./index.php');
}
$update_form .= "<div class='sub-board'>";
$update_form .= "<h3>$datum</h2>";

while($row = mysqli_fetch_row($result)) {
    $cid  = $row[0];
    $thuis = array('naam' => $row[1],'logo' => $row[6]);
    $uit   = array('naam' => $row[2],'logo' => $row[7]);

    $update_form .= "<div class='line-wrapper'>";

    $update_form .= "<div class='thuis'>";
    $update_form .= $thuis['naam'];
    $update_form .= "&nbsp;&nbsp;<img class='team-logo' src='";
    $update_form .= $thuis['logo'];
    $update_form .= "'>";
    $update_form .= '</div>';

    $update_form .= "<div class='uitslag'>";
    $update_form .= "<input size='3' name='frm_thuis_score_$cid' ";
    $update_form .= "value='";
    $key1 = "frm_thuis_score_$cid";
    $update_form .= isset($_POST[$key1]) ? $_POST[$key1] : '';
    $update_form .= "' type='text'>";
    $update_form .= '&nbsp;&nbsp';
    $update_form .= "<input size='3' name='frm_uit_score_$cid' value='";
    $key2 = "frm_uit_score_$cid";
    $update_form .= isset($_POST[$key2]) ? $_POST[$key2] : '';
    $update_form .= "' type='text'>";
    $update_form .= '</div>';

    $update_form .= "<div class='uit'>";
    $update_form .= "&nbsp;&nbsp;<img class='team-logo' src='";
    $update_form .= $uit['logo'];
    $update_form .= "'>";
    $update_form .= '&nbsp;&nbsp';
    $update_form .= $uit['naam'];
    $update_form .= '</div>';

    $update_form .= '</div>';
}
$update_form .= '</div>';
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

    <?php echo $message; ?>

    <form id='update-score-form' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
        <input name='frm_update_score_datum' type='hidden' value='<?php echo $datum; ?>' />
    
    <div class='display-board'>
        <h2>update uw score</h2>

        <div class='header'>
            <div class='thuis'>thuis</div>
            <div class='uitslag'>&nbsp;</div>
            <div class='uit'>uit</div>
        </div>

        <?php echo $update_form; ?>

        <div class='submit'>
            <input name='frm_score_update_submit' type='submit' value='update uitslag'>
        </div>
    </div>
    
</form>
</article>
<?php require('./footer.php'); ?>
</body>
</html>