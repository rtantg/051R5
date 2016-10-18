<header>
   <ul id="menu">
      <li>
          <a id='home-button' href="./index.php">home</a>
      </li>

      <?php if (isset($_SESSION['username'])) { ?>
      <li>
         <a href="./schemas.php">schema opvoeren</a>
      </li>
      <?php } ?>

      <li><a href="./gebruikers.php">registreren</a></li>
      <li style="float:right;">
         <a href="./login.php"><?php echo isset($_SESSION['username'])?'uitloggen':'inloggen'; ?></a></li>
   </ul>
</header>
