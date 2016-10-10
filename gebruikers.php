<?php
session_start();
require_once('./db.php');
require_once('./functions.php');
$message = '';

if (!$link) {
    $message .= melding(mysqli_connect_errno() . ': ' . mysqli_connect_error(),1);
}
else {

    if (isset($_POST['frm_user_submit'])) {
        $name     = isset($_POST["frm_user_name"])?$_POST["frm_user_name"]:"";
        $username = isset($_POST["frm_user_username"])?$_POST["frm_user_username"]:"";
        $password = isset($_POST["frm_user_password"])?$_POST["frm_user_password"]:"";
        $email    = isset($_POST["frm_user_email"])?$_POST["frm_user_email"]:"";

        if (!empty($name) &&
            !empty($username) && 
            !empty($password) &&
            !empty($email)) {

            // Form has been submitted
            // process arguments
            $valid_input = TRUE;

            if (!preg_match('/[[:alpha:]]+/',$name)) {
                $message .= melding("uw naam moet uit alphanumerieke karakters bestaan.",1);
                $valid_input = FALSE;
            }
            if (!preg_match('/[[:alpha:]]{3,20}/', $username)) {
                $message .= melding("gebruikersnaam: '" . $username . "' moet uit minstens 3 alphanumerieke karakters bestaan.",1);
                $valid_input = FALSE;
            }
            if (!preg_match('/[!-~]{8,32}/',$password)) {
                $message .= melding("het wachtwoord moet uit minstens 8 karakters bestaan (!-~)",1);
                $valid_input = FALSE;
            }
            if (!preg_match('/[[:alpha:]]{2,}@[[:alpha:]]{2,}\.nl/',$email)) {
                $message .= melding("emailadres: '" . $email . "' moet volgens volgend formaat:\n\n&nbsp;&nbsp;&nbsp;&lt;naam&gt;@&lt;domein&gt;.nl\n\n waar 'naam' en 'domein' beide uit minstens 2 alphanumerieke karakters bestaan.",1);
                $valid_input = FALSE;
            }
            if ($valid_input) {
                $md5password = md5($password);
                $user_insert = "INSERT INTO gebruikers (gebruikersnaam,naam,wachtwoord,email) VALUES('$username','$name','$md5password','$email')";

                if (mysqli_query($link,$user_insert)==FALSE) {
                    $message .= melding(mysqli_errno($link) . ": " . mysqli_error($link),0);
                }
                else {
                    $_SESSION['users_name'] = $name;
                    $_SESSION['users_username'] = $username;
                    $_SESSION['users_email'] = $email;

                    header('Location: ./gebruikers.php');
                    exit;
                }
            }
        }
        else {
            $message .= melding("niet alle velden zijn ingevuld.",1);
        }
    }
} // end no database connection 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Inzendopdracht 051R5</title>
<link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>

<body>

<?php require('./header.php'); ?>

<article id="content">
<?php 
if (isset($_SESSION['users_name']) && 
    isset($_SESSION['users_username']) &&
    isset($_SESSION['users_email'])) {
        $message .= melding($_SESSION['users_name'] . ", " . $_SESSION['users_username'] . ', ' . $_SESSION['users_email'] . ' succesvol toegevoegd.',2);
        unset($_SESSION['users_naam']);
        unset($_SESSION['users_usernaam']);
        unset($_SESSION['users_email']);
}
echo $message; 
?>
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
</body>
</html>
