<?php 
session_start();
require_once('./db.php');
require_once('./functions.php');

$message = '';
$html_stand = '';
$html_uitslagen = '';
$html_programma = '';

// Check database connectie
if (!$link) {
    $message = melding(mysqli_connect_errno() . ': ' . mysqli_connect_error(),1);
}
else {
    // alle teams
    $query0 = 'SELECT * FROM voetbalteams ORDER BY naam ASC';
    // alle competities met logos
    $query1 = 'SELECT a.*, b.logo, c.logo
               FROM competitieschema AS a
               JOIN
                  voetbalteams AS b 
                  ON (a.thuis_club=b.naam)
               JOIN
                  voetbalteams AS c 
                  ON (a.uit_club=c.naam)
               ORDER BY a.datum';
    
    $result0 = mysqli_query($link,$query0);  // voetbalteams
    $result1 = mysqli_query($link,$query1);  // voetbalcompetities met logos
    
    if ($result0 == false || $result1 == false){
        $message .= melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
    }
    else {
        // genereer stand, uitslagen en programma
        // Het programma: alle gespeelde wedstrijden zonder score
        // stand: alle wedstrijden met score
        // uitslagen: alle wedstrijden met score
        // Initialiseer array to 0 or '' for all teams.

        $wedstrijd_info_per_team = array();
        $wedstrijd_info_op_datum = array();

        while($row=mysqli_fetch_row($result0)){
            $wedstrijd_info_per_team[$row[0]] = array (
                'gespeeld' => 0,
                'doelpunten' => 0,
                'tegen' => 0,
                'gewonnen' => 0,
                'verloren' => 0,
                'gelijk' => 0,
                'punten' => 0,
                'logo' => $row[5]
            );
        }
        // Verzamel de wedstrijden statistieken
        while($row=mysqli_fetch_row($result1)){
            $cid         = $row[0];
            $thuis       = $row[1];
            $uit         = $row[2];
            $datum       = $row[3];
            $score_thuis = $row[4];
            $score_uit   = $row[5];
            $logo_thuis  = $row[6];
            $logo_uit    = $row[7];
            
            $wedstrijd_info_op_datum[$datum][] = array(
                'cid'    => $cid,
                'thuis' => array ('naam' => $thuis, 'score' => $score_thuis, 'logo' => $logo_thuis),
                'uit'   => array ('naam' => $uit, 'score' => $score_uit, 'logo' => $logo_uit)
            );
            if(!is_null($score_thuis) && !is_null($score_uit)) {
                $wedstrijd_info_per_team[$thuis]['gespeeld'] += 1;
                $wedstrijd_info_per_team[$uit]['gespeeld']   += 1;

                $wedstrijd_info_per_team[$thuis]['doelpunten'] += $score_thuis;
                $wedstrijd_info_per_team[$thuis]['tegen']      += $score_uit;

                $wedstrijd_info_per_team[$uit]['doelpunten'] += $score_uit;
                $wedstrijd_info_per_team[$uit]['tegen']      += $score_thuis;

                $wedstrijd_info_per_team[$thuis]['gewonnen'] += ($score_thuis>$score_uit)?1:0;
                $wedstrijd_info_per_team[$thuis]['gelijk']   += ($score_thuis==$score_uit)?1:0;
                $wedstrijd_info_per_team[$thuis]['verloren'] += ($score_thuis<$score_uit)?1:0;

                $wedstrijd_info_per_team[$uit]['gewonnen'] += ($score_uit>$score_thuis)?1:0;
                $wedstrijd_info_per_team[$uit]['gelijk']   += ($score_uit==$score_thuis)?1:0;
                $wedstrijd_info_per_team[$uit]['verloren'] += ($score_uit<$score_thuis)?1:0;

                if ($score_thuis>$score_uit) {
                    $wedstrijd_info_per_team[$thuis]['punten'] += 3;
                }
                else if ($score_thuis<$score_uit) {
                    $wedstrijd_info_per_team[$uit]['punten'] += 3;
                }
                else if ($score_thuis==$score_uit) {
                    $wedstrijd_info_per_team[$thuis]['punten'] += 1;
                    $wedstrijd_info_per_team[$uit]['punten'] += 1;
                }
                $wedstrijd_info_per_team[$thuis]['logo'] = $logo_thuis;
                $wedstrijd_info_per_team[$uit]['logo'] = $logo_uit;
            }
       }
       // Genereer html voor de stand van de clubs
       $html_stand = "<div id='stand' class='display-board'>";
       $html_stand .= "<h2>stand</h2>";

       $html_stand .= "<div class='header'>";
       $html_stand .= "<div class='club'>club</div>";
       $html_stand .= "<div class='gespeeld'>gespeeld</div>";
       $html_stand .= "<div class='doelpunten'>doelpunten</div>";
       $html_stand .= "<div class='gewonnen'>w</div>";
       $html_stand .= "<div class='gelijk'>g</div>";
       $html_stand .= "<div class='verloren'>v</div>";
       $html_stand .= "<div class='punten'>punten</div>";
       $html_stand .= '</div>';

       // Sorteer club lijst op aantal punten en op doelsaldo indien 
       // gelijk aantal punten (zie vergelijk_score)
       uasort($wedstrijd_info_per_team,'vergelijk_score');
       $html_stand .= "<div class='sub-board'>";
       $even = 1;
       
       foreach($wedstrijd_info_per_team as $club => $info) {
           
            $html_stand .= "<div class='line-wrapper ";
            $html_stand .= ($even%2) ? "even'>" : "oneven'>";
            $even ++; 

            $html_stand .= "<div class='club'>";
            $html_stand .= $club;
            $html_stand .= '&nbsp;&nbsp;';
            $html_stand .= "<img class='team-logo' src='$info[logo]'>";
            $html_stand .= '</div>';

            $html_stand .= "<div class='gespeeld'>$info[gespeeld]</div>";

            $html_stand .= "<div class='doelpunten'>";
            $html_stand .= ($info['doelpunten']>0)? '+':'';
            $html_stand .= $info['doelpunten'];
            $html_stand .= '</div>';

            $html_stand .= "<div class='tegen'>";
            $html_stand .= ($info['tegen']>0)? '-':'';
            $html_stand .= $info['tegen'];
            $html_stand .= '</div>';

            $html_stand .= "<div class='saldo'>";
            $html_stand .= '(' . ($info['doelpunten'] - $info['tegen']) . ')';
            $html_stand .= '</div>';

            $html_stand .= "<div class='gewonnen'>$info[gewonnen]</div>";
            $html_stand .= "<div class='gelijk'>$info[gelijk]</div>";
            $html_stand .= "<div class='verloren'>$info[verloren]</div>";

            $html_stand .= "<div class='punten'>$info[punten]</div>";

            $html_stand .= '</div>';
        }
        $html_stand .= '</div>';
        $html_stand .= "<div style='width:100%;border-top:1px solid #ccc;padding:3px;'><b>w</b> = winnen, <b>g</b> = gelijk, <b>v</b> = verloren</div>";
        $html_stand .= '</div>';
        
        if (!count($wedstrijd_info_per_team)) {
            $html_stand = '';
        }

        // Genereer html voor uitslagen
        $html_uitslagen = "<div id='uitslagen' class='display-board'>";
        $html_uitslagen .= '<h2>uitslagen</h2>';

        $html_uitslagen .= "<div class='header'>";
        $html_uitslagen .= "<div class='thuis'>thuis</div>";
        $html_uitslagen .= "<div class='uit'>uit</div>";
        $html_uitslagen .= '</div>';

        $geen_uitslagen = true;
        foreach ($wedstrijd_info_op_datum as $datum => $wedstrijden) {

            $html_header = '';
            $html_lijst = '';
            $html_header .= "<div class='sub-board'>";

            $html_header .= "<h3>$datum</h3>";
            
            foreach($wedstrijden as $wedstrijd) {
                if (!is_null($wedstrijd['thuis']['score']) && !is_null($wedstrijd['uit']['score'])) {
                    $geen_uitslagen = false;
                    $html_lijst .= "<div class='line-wrapper'>";

                    $html_lijst .= "<div class='thuis'>";
                    $html_lijst .= "<span>" . $wedstrijd['thuis']['naam'] . "</span>";
                    $html_lijst .= '&nbsp;&nbsp';
                    $html_lijst .= "<img class='team-logo' src='" . $wedstrijd['thuis']['logo'] . "'>";
                    $html_lijst .= '</div>';

                    $html_lijst .= "<div class='uitslag'>";
                    $html_lijst .= $wedstrijd['thuis']['score'];
                    $html_lijst .= ' - ';
                    $html_lijst .= $wedstrijd['uit']['score'];
                    $html_lijst .= '</div>';

                    $html_lijst .= "<div class='uit'>";
                    $html_lijst .= "<img class='team-logo' src='" . $wedstrijd['uit']['logo']     . "'>";
                    $html_lijst .= '&nbsp;&nbsp';
                    $html_lijst .= '<span>' . $wedstrijd['uit']['naam'] . '</span>';
                    $html_lijst .= '</div>';

                    $html_lijst .= '</div>';
                }
            }
            if ($html_lijst != '') {
                $html_uitslagen .= $html_header . $html_lijst;
                $html_uitslagen .= '</div>';
            }
        }
        $html_uitslagen .= '</div>';
        $geen_programma = true;
        
        // Genereer HTML voor het programma de wedstrijden zonder score.
        $html_programma = "<div id='programma' class='display-board'>";
        $html_programma .= '<h2>programma</h2>';
        $html_programma .= "<div class='header'>";
        $html_programma .= "<div class='thuis'>thuis</div>";
        $html_programma .= "<div class='uit'>uit</div>";
        $html_programma .= '</div>';

        foreach ($wedstrijd_info_op_datum as $datum => $wedstrijden) {
           
            $html_header = '';
            $html_lijst = '';
            $html_get_list = '';
            $html_header = "<div class='sub-board'>";
            if (isset($_SESSION['username'])) {
                $html_header .= "<h3><a href='./update_score.php?d=$datum'>$datum</a></h3>";
            }
            else {
                $html_header .= "<h3>$datum</h3>";
            }
            foreach($wedstrijden as $wedstrijd) {

                if (is_null($wedstrijd['thuis']['score']) && is_null($wedstrijd['uit']['score'])) {
                    $geen_programma = false;
                    $html_lijst .= "<div class='line-wrapper'>";

                    $html_lijst .= "<div class='thuis'>";
                    $html_lijst .= $wedstrijd['thuis']['naam'];
                    $html_lijst .= '&nbsp;&nbsp;';
                    $html_lijst .= "<img class='team-logo' src='" . $wedstrijd['thuis']['logo'] . "'>";
                    $html_lijst .= '</div>';

                    $html_lijst .= "<div class='uitslag'>&nbsp;</div>";

                    $html_lijst .= "<div class='uit'>";
                    $html_lijst .= "<img class='team-logo' src='" . $wedstrijd['uit']['logo'] . "'>";
                    $html_lijst .= '&nbsp;&nbsp;';
                    $html_lijst .= $wedstrijd['uit']['naam'];
                    $html_lijst .= '</div>';

                    $html_lijst .= '</div>';
                }
            }
            if ($html_lijst!= '') {
                $html_programma .= $html_header . $html_lijst;
                $html_programma .= '</div>';
            }
        }
        $html_programma .= '</div>';
        if ($geen_uitslagen) {
            $html_uitslagen = '';
            }
        if ( $geen_programma ) {
            $html_programma = '';
        }
    }
}
// end no database connection
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>Inzendopdracht 051R5</title>
<link rel='stylesheet' type='text/css' href='./css/style.css' />
</head>
<body>

<?php require_once('./header.php'); ?>

<article id='content'>
<?php
    echo $message;
    if (!empty($html_stand)) {
        echo $html_stand;
    }
    else {
        echo melding('Op dit moment zijn er nog geen teams geregistreerd.',2);
    }

    if (!empty($html_uitslagen)) {
        echo $html_uitslagen;
    }
    else {
        echo melding('Op dit moment zijn er nog geen wedstrijden gespeeld.',2);
    }
    if (!empty($html_programma)) {
        echo $html_programma;
    }
    else {
        echo melding('Op dit moment is er nog geen schema bekend.',2);
    }
 ?>
</article>
<?php require_once('./footer.php'); ?>
</body>
</html>