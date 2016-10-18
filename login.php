<?php
session_start();
require_once('./db.php');
require_once('./functions.php');

$message = '';

if (!$link){
    $message = melding(mysqli_connect_errno() . ': ' . mysqli_connect_error(),2);
}
else {
    // login toggle. 
    if (isset($_SESSION['username'])){
        unset($_SESSION['username']);
    }
    if (isset($_POST['frm_login_submit'])) {

        $username = isset($_POST['frm_login_username'])?$_POST['frm_login_username']:'';
        $password = isset($_POST['frm_login_password'])?$_POST['frm_login_password']:'';

        if (!empty($username) && !empty($password)) {
            $select = "SELECT * FROM gebruikers WHERE gebruikersnaam='$username'";

            $result=mysqli_query($link,$select);

            if ($result==FALSE) {
                $message=  melding(mysqli_errno($link) . ': ' . mysqli_error($link),0);
            }
            elseif(!mysqli_num_rows($result)) {
                $message = melding("'$username' niet gevonden.",2);
            }
            else {
                $row = mysqli_fetch_row($result);

                if (md5($password) == $row[2]) {
                    $_SESSION['username']=$username;

                    if (isset($_SESSION['last_page'])) {
                        $last_page = basename($_SESSION['last_page']);
                        unset($_SESSION['last_page']);
                        header("Location: $last_page");
                        exit;
                    }
                    else {
                        header('Location: ./index.php');
                        exit;
                    }
                 }
                else {
                    $message = melding('Ongeldig wachtwoord.',2);
                }
            }
        }
        else {
            $message = melding('Niet alle velden zijn ingevuld.',2);
        }
    }
}// no db connection
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset='UTF-8'>
      <title>Inzendopdracht 051R5</title>
      <link rel='stylesheet' type='text/css' href='./css/style.css' />
   </head>

<body>
<?php require('./header.php');?>

<article id='content'>
<?php 
echo $message;
if (!isset($_SESSION['username'])) {
    $username = isset($_POST['frm_login_username']) ? $_POST['frm_login_username'] : '';
    $password = isset($_POST['frm_login_password']) ? $_POST['frm_login_password'] : '';
    ?>
    <form id='login-form' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
        <div class='display-board'>
            <h2> Inloggen</h2>
            <div class='sub-board'>
                
                <div class='login-wrapper'>
                <div class='line-wrapper'>
                    <div class='username'>gebruikersnaam:</div>
                    <div>
                    <input type='text' name='frm_login_username' value='<?php if (isset($username)) { echo $username; } ?>'>
                    </div>
                </div>
                <div class='line-wrapper'>
                    <div class='password'>wachtwoord:</div> 
                    <div>
                    <input type='password' name='frm_login_password' value='<?php if (isset($password)){echo $password;} ?>'>
                    </div>
                </div>
                </div>
                <div class='line-wrapper'>
                    <div class='submit'>
                    <input type='submit' id='frm_login_submit' name='frm_login_submit' value='login'>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php } ?>
</article>
<?php require_once('./footer.php');?>
</body>
</html>
