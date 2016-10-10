<header>
   <ul id="menu">
      <li>
          <a href="./index.php"><img width='45px' src='./images/home-goal.png'></a>
      </li>

      <?php if (isset($_SESSION['login'])) { ?>
      <li>
         <a href="./schemas.php">schema opvoeren</a>
      </li>
      <?php } ?>

      <li><a href="./gebruikers.php">registreren</a></li>
      <li style="float:right;">
         <a href="./login.php"><?php echo isset($_SESSION['login'])?'uitloggen':'inloggen'; ?></a></li>
   </ul>
</header>
