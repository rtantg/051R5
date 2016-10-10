<?php
session_start();
require_once('./db.php');
require_once('./functions.php');

$message = '';


if (!$link){
    $message = melding(mysqli_connect_errno() . ': ' . mysqli_connect_error(),1);
    
}
else {
    // login toggle. 
    if (isset($_SESSION['login']) && 
        isset($_SESSION['username']) &&
        isset($_SESSION['name'])) {

        $message = melding("$_SESSION[name] ($_SESSION[username]): u bent nu uitgelogd.",2);
        unset($_SESSION['login']);
        unset($_SESSION['username']);
        unset($_SESSION['name']);
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
                $message = melding("'$username' niet gevonden.",1);
            }
            else {
                $row = mysqli_fetch_row($result);

                if (md5($password) == $row[2]) {
                    $_SESSION['login']=TRUE;
                    $_SESSION['username']=$username;
                    $_SESSION['name']=$row[1];
                    $message = melding("$row[1] ('$username'): u bent nu ingelogd.",2);

                    if (isset($_SESSION['last_page'])) {
                        $last_page = basename($_SESSION['last_page']);
                        unset($_SESSION['last_page']);
                        header("Location: $last_page");
                        exit;
                    }
                 }
                else {
                    $message = melding('ongeldig wachtwoord.',1);
                }
            }
        }
        else {
            $message = melding('niet alle velden zijn ingevuld.',1);
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
if (!isset($_SESSION['login'])) {
    $username = isset($_POST['frm_login_username'])?$_POST['frm_login_username']:'';
    $password = isset($_POST['frm_login_password'])?$_POST['frm_login_password']:'';
    ?>
    <form id='login-form' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
        <div class='display-board'>
            <h2> Inloggen</h2>
            <div class='sub-board'>
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
                <div class='line-wrapper'>
                    <div class='submit'>
                    <input type='submit' name='frm_login_submit' value='login'>
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
