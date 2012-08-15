<?php
    include_once "../include/credentials.php";
    
    if (!session_start()) {
        die("Couldn't start session");
    }

    if (!isset($_SESSION["loggedin"])) {
        head("../index.html");
    }

    $credentials = $_SESSION["credentials"];
    if ($credentials != CRED_ADMIN) {
        die("BUG: Non-admin user is accessing admin area");
    }

    echo "FridayPub Admin's Area</br>";
    echo "Logged in as: <b>${_SESSION["username"]}</b>";
?>

<a href="../common/logout.php">(logout)</a> </br>
<a href="../admin/inventory.php">INVENTORY</a>
<a href="../admin/iou.php">IOU</a>
<a href="../admin/reg_beers.php">REGISTER BEERS</a>
<a href="../admin/payment.php">REGISTER PAYMENT</a>
<a href="../admin/purchase.php">REGISTER PURCHASE</a>
<a href="../admin/add_user.php">ADD USER</a>
<hr>

