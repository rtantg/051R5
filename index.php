<?php 
session_start();
require_once('./db.php');
require_once('./functions.php');
require_once('./globals.php');

$message = '';
$html_stand = '';
$html_uitslagen = '';
$html_programma = '';

// maak query voor reeds gespeelde wedstrijden *en* die een uitslag hebben.
// verkrijg ook logo's
$select1 = 'SELECT a.*, b.logo, c.logo
           FROM competitieschema AS a
           JOIN
              voetbalteams AS b 
              ON (a.thuis_club=b.naam)
           JOIN
              voetbalteams AS c 
              ON (a.uit_club=c.naam)
           WHERE
              a.datum <= CURDATE()
           AND 
                a.thuis_score IS NOT NULL 
           AND 
                a.uit_score IS NOT NULL
           ORDER BY
              a.datum';

$select4 = 'SELECT * FROM competitieschema
            WHERE datum <= CURDATE() AND thuis_score IS NULL AND uit_score IS NULL';
$result4 = mysqli_query($link,$select4);

$no_score=0;
if ($result4==FALSE) {
   $message = melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
}
elseif (mysqli_num_rows($result4)>0){
    while($row=mysqli_fetch_row($result4)){
        $no_score += 1;
        $str = ($no_score == 1) ? "er is &eacute;&eacute;n gespeelde wedstrijd " : "er zijn $no_score gespeelde wedstrijden ";
        $message = melding ($str . "waar de uitslag nog niet is doorgevoerd.\nU kunt <a href='./schemas.php'>hier</a> uitslagen doorgeven.",2);
    }
}

// Ga door met 
$result1 = mysqli_query($link,$select1);

if ($result1==FALSE) {
   $message = melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
}
elseif(!mysqli_num_rows($result1) ) {
    $message .= melding('er zijn nog geen uitslagen bekend en daardoor ook geen stand.',2);
}
else {
    // collect actuele stand en uitslagen gespeelde wedstrijden
    $stand = array();
    $uitslagen = array();

    // Initialiseer array to 0 or ''
    foreach ($clubs as $club => $info) {
       $stand[$club]['gespeeld'] = 0;
       $stand[$club]['doelpunten'] = 0;
       $stand[$club]['tegen'] = 0;
       $stand[$club]['gewonnen'] = 0;
       $stand[$club]['verloren'] = 0;
       $stand[$club]['gelijk'] = 0;
       $stand[$club]['punten'] = 0;
       $stand[$club]['logo'] = '';
    }
    // Verzamel de wedstrijden statistieken
    while($row=mysqli_fetch_row($result1)){

        $datum       = $row[3];
        $thuis       = $row[1];
        $score_thuis = $row[4];
        $logo_thuis  = $row[6];
        $uit         = $row[2];
        $score_uit   = $row[5];
        $logo_uit    = $row[7];
       
        // groupeer gespeelde wedstrijden op datum
        $uitslagen[$datum][] = array(
            'thuis' => array ('naam' => $thuis, 'score' => $score_thuis, 'logo' => $logo_thuis),
            'uit'   => array ('naam' => $uit, 'score' => $score_uit, 'logo' => $logo_uit)
        );
        $stand[$thuis]['gespeeld'] += 1;
        $stand[$uit]['gespeeld']   += 1;

        $stand[$thuis]['doelpunten'] += $score_thuis;
        $stand[$thuis]['tegen']      += $score_uit;

        $stand[$uit]['doelpunten'] += $score_uit;
        $stand[$uit]['tegen']      += $score_thuis;

        $stand[$thuis]['gewonnen'] += ($score_thuis>$score_uit)?1:0;
        $stand[$thuis]['gelijk']   += ($score_thuis==$score_uit)?1:0;
        $stand[$thuis]['verloren'] += ($score_thuis<$score_uit)?1:0;

        $stand[$uit]['gewonnen'] += ($score_uit>$score_thuis)?1:0;
        $stand[$uit]['gelijk']   += ($score_uit==$score_thuis)?1:0;
        $stand[$uit]['verloren'] += ($score_uit<$score_thuis)?1:0;

        if ($score_thuis>$score_uit) {
            $stand[$thuis]['punten'] += 3;
        }
        else if ($score_thuis<$score_uit) {
            $stand[$uit]['punten'] += 3;
        }
        else if ($score_thuis==$score_uit) {
            $stand[$thuis]['punten'] += 1;
            $stand[$uit]['punten'] += 1;
        }
        $stand[$thuis]['logo'] = $logo_thuis;
        $stand[$uit]['logo'] = $logo_uit;
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
   uasort($stand,'vergelijk_score');
   $html_stand .= "<div class='sub-board'>";

   foreach($stand as $club => $info) {

        if ($info['gespeeld']>0) {
            
            $html_stand .= "<div class='line-wrapper'>";

            $html_stand .= "<div class='club'>";
            $html_stand .= $club;
            $html_stand .= '&nbsp;&nbsp;';
            $html_stand .= "<img width='30px' src='$info[logo]'>";
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
    }
    $html_stand .= '</div>';
    $html_stand .= "<div style='width:100%;border-top:1px solid #ccc;padding:3px;'><b>w</b> = winnen, <b>g</b> = gelijk, <b>v</b> = verloren</div>";
    $html_stand .= '</div>';

    // Genereer html voor uitslagen
    $html_uitslagen = "<div id='uitslagen' class='display-board'>";
    $html_uitslagen .= '<h2>uitslagen</h2>';

    $html_uitslagen .= "<div class='header'>";
    $html_uitslagen .= "<div class='thuis'>thuis</div>";
    $html_uitslagen .= "<div class='uit'>uit</div>";
    $html_uitslagen .= '</div>';

    $html_uitslagen .= "<div class='sub-board'>";

    foreach ($uitslagen as $date => $wedstrijden) {

        $html_uitslagen .= "<h3>$date</h3>";

        foreach($wedstrijden as $wedstrijd) {

            $html_uitslagen .= "<div class='line-wrapper'>";

            $html_uitslagen .= "<div class='thuis'>";
            $html_uitslagen .= "<span>" . $wedstrijd['thuis']['naam'] . "</span>";
            $html_uitslagen .= '&nbsp;&nbsp';
            $html_uitslagen .= "<img width='30px' src='" . $wedstrijd['thuis']['logo'] . "'>";
            $html_uitslagen .= '</div>';

            $html_uitslagen .= "<div class='uitslag'>";
            $html_uitslagen .= $wedstrijd['thuis']['score'];
            $html_uitslagen .= ' - ';
            $html_uitslagen .= $wedstrijd['uit']['score'];
            $html_uitslagen .= '</div>';

            $html_uitslagen .= "<div class='uit'>";
            $html_uitslagen .= "<img width='30px' src='" . $wedstrijd['uit']['logo']     . "'>";
            $html_uitslagen .= '&nbsp;&nbsp';
            $html_uitslagen .= '<span>' . $wedstrijd['uit']['naam'] . '</span>';
            $html_uitslagen .= '</div>';

            $html_uitslagen .= '</div>';
        }
    }
    $html_uitslagen .= '</div>';
    $html_uitslagen .= '</div>';
}

// Genereer programma: haal alle nog te spelen wedstijden(programma) uit de db
$select2 = 'SELECT  a.*, b.logo,c.logo
       FROM competitieschema AS a
       JOIN voetbalteams AS b
       ON
          (a.thuis_club=b.naam)
       JOIN voetbalteams AS c
       ON
          (a.uit_club=c.naam)
       WHERE
          a.datum > CURDATE()
       ORDER BY a.datum';

$result2 = mysqli_query($link,$select2);

if ($result2 == FALSE) {
  $message = melding(mysqli_errno($link) . ': ' . mysqli_errno($link),0);
}
elseif(!mysqli_num_rows($result2)) {
    $message .= melding('er is nog geen programma bekend.',2);
}
else {
    
  $programma = array();

  while($row=mysqli_fetch_row($result2)){

     $programma[$row[3]][] = array(
        'club_thuis' => $row[1],
        'club_uit' => $row[2],
        'logo_thuis' => $row[6],
        'logo_uit' => $row[7]
        );
  }
  $html_programma = "<div id='programma' class='display-board'>";
  $html_programma .= '<h2>programma</h2>';
  $html_programma .= "<div class='header'>";
  $html_programma .= "<div class='thuis'>thuis</div>";
  $html_programma .= "<div class='uit'>uit</div>";
  $html_programma .= '</div>';

  $html_programma .= "<div class='sub-board'>";
  
  foreach($programma as $date => $wedstrijden ) {

     $html_programma .= "<h3>$date</h3>";

     foreach($wedstrijden as $wedstrijd) {

        $html_programma .= "<div class='line-wrapper'>";

        $html_programma .= "<div class='thuis'>";
        $html_programma .= $wedstrijd['club_thuis'];
        $html_programma .= '&nbsp;&nbsp;';
        $html_programma .= "<img width='30px'; src='$wedstrijd[logo_thuis]'>";
        $html_programma .= '</div>';

        $html_programma .= "<div class='uitslag'>&nbsp;</div>";

        $html_programma .= "<div class='uit'>";
        $html_programma .= "<img width='30px'; src='$wedstrijd[logo_uit]'>";
        $html_programma .= '&nbsp;&nbsp;';
        $html_programma .= $wedstrijd['club_uit'];
        $html_programma .= '</div>';

        $html_programma .= '</div>';
     }
  }
  $html_programma .= '</div>';
  $html_programma .= '</div>';
}
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
    echo $html_stand;
    echo $html_uitslagen;
    echo $html_programma;
 ?>
</article>
<?php require_once('./footer.php'); ?>
</body>
</html>