<header>
   <ul id="menu">
      <li>
         <a href="./index.php">schema</a>
      </li>

      <?php if ($_SESSION['login']) { ?>
      <li>
         <a href="./schemas.php">schema opvoeren</a>
      </li>
      <?php } ?>

      <li><a href="./users.php">gebruikers</a></li>
      <li><a href="./teams.php">clubs</a></li>
      <li style="float:right;">
         <a href="./login.php"><?php echo $_SESSION['login']?'uitloggen':'inloggen'; ?></a></li>
   </ul>
</header>
