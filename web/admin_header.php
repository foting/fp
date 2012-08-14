<?php
    include_once "include/credentials.php";
    
    if (!session_start()) {
        die("Couldn't start session");
    }

    if (!isset($_SESSION["loggedin"])) {
        head("index.html");
    }

    $credentials = $_SESSION["credentials"];
    if ($credentials != CRED_ADMIN) {
        die("BUG: Non-admin user is accessing admin area");
    }

    echo "FridayPub Admin's Area</br>";
    echo "Logged in as: <b>${_SESSION["username"]}</b>";
?>

<a href="logout.php">(logout)</a> </br>
<a href="admin_inventory.php">INVENTORY</a>
<a href="admin_iou.php">IOU</a>
<a href="admin_regbeers.php">REGISTER BEERS</a>
<a href="admin_payment.php">REGISTER PAYMENT</a>
<a href="admin_purchase.php">REGISTER PURCHASE</a>
<a href="admin_useradd.php">ADD USER</a>
<hr>

