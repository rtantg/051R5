<?php
require_once('./db.php');
require_once('./globals.php');
require_once 'functions.php';
?>
<!DOCTYE HTML>
<HEAD><TITLE>Vul de clubs tabel</TITLE>
    <meta charset='utf-8'>
    <link rel='stylesheet' type='text/css' href='./css/style.css' />
</HEAD>
<BODY style="padding:0px;">

<?php 
$select ='SELECT * FROM voetbalteams';

$result = mysqli_query($link,$select);

if ($result == FALSE) {
    $message=  melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
}
elseif(mysqli_num_rows($result) > 0) {
    $message=  melding ("Tabel 'clubs' reeds gevuld.",2);
}
else {
    $insert  = "INSERT INTO voetbalteams (naam,locatie,stadion,opgericht,website,logo)\nVALUES\n"; 
    $i = 0;
    foreach ($clubs as $club => $info) {
       $insert .= "('$club','$info[locatie]','$info[stadion]','$info[opgericht]','$info[website]','$info[logo]')";
       $i++;
       if ($i < count($clubs)) { $insert .= ',';  }
    }
    $result2 = mysqli_query($link, $insert);
    if ($result2 == FALSE) {
        $message=  melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
    }
    else {
        $message = melding( "Tabel 'voetbalteams' gevuld':<br><br>" . nl2br($insert) . "<br><br>Klik <a href='./'>hier</a>",2);
    }
}
echo $message;
?>
</BODY>
</html>