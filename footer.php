<footer>
<span style="margin:5px;float:left;">
<?php if (isset($_SESSION['username'])) { echo "Ingelogd als: " . $_SESSION['username']; } ?>
</span>
<span style="margin:5px;float:right;"><?php echo date("y-m-d H:m"); ?></span>
</footer>
