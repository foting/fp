<?php
    session_start() or die("Couldn't start session");
    $username = $_SESSION["username"];

    echo "FridayPub Admin's Area - logged in as: ${username}</br>";
?>
    <a href="admin_inventory.php">INVENTORY</a>
    <a href="admin_iou.php">IOU</a>
    <a href="admin_regbeers.php">REGISTER BEERS</a>
    <a href="admin_payment.php">REGISTER PAYMENT</a>
    <a href="admin_useradd.php">ADD USER</a>
    <hr>

