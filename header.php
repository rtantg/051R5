<?php
if (isset($_GET["select"]) ){

   switch($_GET["select"]) {

      case "users":
         header("Location: ./users.php");
         break;

      case "teams":
         header("Location: ./teams.php");
         break;

      case "schemas":
         header("Location: ./schemas.php");
         break;

      case "competities":
      default:
         header("Location: ./voetbalcompetities.php");
         break;
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
<header>
   <ul id="menu">
      <li>
         <a href="./voetbalcompetities.php?select=competities">schema</a>
      </li>

      <?php if ($_SESSION['login']) { ?>
      <li>
         <a href="./voetbalcompetities.php?select=schemas">schema opvoeren</a>
      </li>
      <?php } ?>

      <li><a href="./voetbalcompetities.php?select=users">gebruikers</a></li>
      <li><a href="./voetbalcompetities.php?select=teams">clubs</a></li>
      <li style="float:right;">
         <a href="./login.php"><?php echo $_SESSION['login']?'uitloggen':'inloggen'; ?></a></li>
   </ul>
</header>
