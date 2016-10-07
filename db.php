<?php 
include_once 'functions.php';
$message = '';

date_default_timezone_set('Europe/Amsterdam');

$link = mysqli_connect('p:127.0.0.1','root','welkom09','dbLOI');

if (!$link) {
    $message = melding( mysqli_connect_errno() . ': ' . mysqli_connect_error(),0); 
}
echo $message;
