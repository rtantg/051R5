<?php
session_start();
require_once('./db.php');
require_once('./functions.php');

$message = "";

if ($_POST['frm_user_submit']) {

   if (!empty($_POST["frm_user_name"]) &&
       !empty($_POST["frm_user_username"]) &&
       !empty($_POST["frm_user_password"]) &&
       !empty($_POST["frm_user_email"])) {
     // Form has been submitted

     $error_flag = FALSE;
     // process arguments
     if (!check_name($_POST['frm_user_name'])) {
        $message .= melding("uw naam moet uit alphanumerieke karakters bestaan.",1);
        $error_flag = TRUE;
     }
     if (!check_username($_POST['frm_user_username'])) {
        $message .= melding("gebruikersnaam: '" . $_POST['frm_user_username'] . "' moet uit minstens 3 alphanumerieke karakters bestaan.",1);
        $error_flag = TRUE;
     }
     if (!check_password($_POST['frm_user_password'])) {
        $message .= melding("het wachtwoord moet uit minstens 8 karakters bestaan (!-~)",1);
        $error_flag = TRUE;
     }
     if (!check_email($_POST['frm_user_email'])) {
        $message .= melding("emailadres: '" . $_POST['frm_user_email'] . "' moet volgens volgend formaat:\n\n&nbsp;&nbsp;&nbsp;&lt;naam&gt;@&lt;domein&gt;.nl\n\n waar 'naam' en 'domein' beide uit minstens 2 alphanumerieke karakters bestaan.",1);
        $error_flag = TRUE;
     }
     if (!$error_flag) {
     $name = $_POST['frm_user_name'];
     $username = $_POST['frm_user_username'];
     $password = md5($_POST['frm_user_password']);
     $email = $_POST['frm_user_email'];
     $message .= nl2br(htmlentities("\ninsert: " . $username . ", " . $password . ", " . $email . "\n"));

     $user_insert = "INSERT INTO gebruikers (gebruikersnaam,naam,wachtwoord,email) VALUES('$username','$name','$password','$email')";

     if (mysqli_query($link,$user_insert)==FALSE) {
        $message = melding(mysqli_errno($link) . ": " . mysqli_error($link),0);
     }
     else {
        header('Location: ./login.php');
        exit;
     }
     }
   }
   else {
      $message = melding("niet alle velden zijn ingevuld.",1);
   }
}
require('./header.php');
?>

<article id="content">

<?php echo $message; ?>
<form id='user-input-form' action=<?php echo $_SERVER['PHP_SELF'] ?> method='post'>
   <div class="display-board">

      <h2>voeg gebruiker toe</h2>
      <div class="sub-board">

         <div class="line-wrapper">
            <div class="name">naam:</div>
            <input type="text" size="25" name='frm_user_name' value="<?php if(isset($_POST["frm_user_name"])){echo $_POST['frm_user_name'];} ?>">
         </div>

         <div class="line-wrapper">
            <div class="username">gebruikersnaam:</div>
            <input type="text" size="25" name='frm_user_username' value="<?php if(isset($_POST['frm_user_username'])){echo $_POST['frm_user_username'];} ?>">
         </div>

         <div class="line-wrapper">
            <div class="password">wachtwoord:</div>
            <input type="password" size="25" name='frm_user_password' value="<?php if(isset($_POST['frm_user_password'])){echo $_POST['frm_user_password'];} ?>">
         </div>

         <div class="line-wrapper">
            <div class="email">email:</div>
            <input type="text" size="25" name='frm_user_email' value="<?php if(isset($_POST['frm_user_email'])){echo $_POST['frm_user_email'];} ?>">
         </div>

         <div class="submit">
            <input type='submit' name='frm_user_submit' value='verstuur'>
         </div>

      </div>
   </div>
</form>
</article>

<?php require('./footer.php'); ?>
