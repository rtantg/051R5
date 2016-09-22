<?php
date_default_timezone_set("Europe/Amsterdam");

$link = mysqli_connect('p:127.0.0.1','root','welkom09','dbLOI');

if (!link) {
  echo "Error: unable to connect to the database." . PHP_EOL;
  echo mysqli_errno . ": " . mysqli_error() . PHP_EOL; 
  exit();
}
?>
