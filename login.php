<?php
session_start();
require('./db.php');
require_once('./functions.php');

if (isset($_SESSION['login'])) {
   unset($_SESSION['login']);
   $message = melding($_SESSION['username'] . ": u bent nu uitgelogd.",2);
   unset($_SESSION['username']);
}
else {

   if (isset($_POST['frm_login_submit'])) {

      if (!empty($_POST["frm_login_username"]) && !empty($_POST["frm_login_password"])) {

         $username = $_POST['frm_login_username'];
         $password = $_POST['frm_login_password'];

         $select = "SELECT * FROM gebruikers WHERE gebruikersnaam='" . $username . "'";

         if ($result=mysqli_query($link,$select)) {

            if (!mysqli_num_rows($result)) {
               $message = melding("'".$username."' niet gevonden.",1);
            }
            else {
               $row = mysqli_fetch_row($result);
               if (md5($password) == $row[2]) {

                  $_SESSION['login']=TRUE;
                  $_SESSION['username']=$username;
                  $_SESSION['name']=$row[1];
                  $message = melding($row[1] . " (" . $username . "): u bent nu ingelogd.",2);
               }
               else {
                  $message = melding("ongeldig wachtwoord.",1);
               }
            }
         }
      }
      else {
         $message = melding("er zijn lege velden.",1);
      }
   }
}
require('./header.php');
?>
<?php echo $message; ?>

<?php if (!isset($_SESSION['login'])) { ?>

   <form id='login-form' action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
      <div class="display-board">
         <h2> Inloggen</h2>

      <div class="sub-board">
         <div class="line-wrapper">
            <div class="username">gebruikersnaam:</div>

            <div>
               <input type='text' name='frm_login_username' value="<?php if (isset($_POST['frm_login_username'])) { echo $_POST['frm_login_username']; } ?>">
            </div>
         </div>

         <div class="line-wrapper">
            <div class="password">wachtwoord:</div>
            <div>
               <input type='password' name='frm_login_password' value="<?php if (isset($_POST['frm_login_password'])){echo $_POST['frm_login_password'];} ?>">
            </div>
         </div>

         <div class="line-wrapper">
            <div class="submit">
               <input type='submit' name='frm_login_submit' value='login'>
            </div>
         </div>

      </div>
      </div>
   </form>
<?php } ?>
<?php require('./footer.php');
