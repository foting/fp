<?php
    include_once "../include/credentials.php";
    
    if (!session_start()) {
        die("Couldn't start session");
    }

    if (!isset($_SESSION["loggedin"])) {
        head("../index.html");
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
    	<link rel="stylesheet" href="../css/friday_pub.css">
    	<link href='http://fonts.googleapis.com/css?family=Fredoka+One' rel='stylesheet' type='text/css'>
	
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
    <?php 
        $credentials = $_SESSION["credentials"];
        if ($credentials != CRED_ADMIN) {
            die("BUG: Non-admin user is accessing admin area");
        }
    ?>
        <h1>FridayPub Admin</h1>
        <div class="logout">Logged in as:
            <?php echo $_SESSION["username"]; ?>
        	<a href="../common/logout.php">(logout)</a>
        </div>
        <ul class="menu">
            <li><a href="../admin/inventory.php">INVENTORY</a></li>
            <li><a href="../admin/iou.php">BANK</a></li>
            <li><a href="../admin/payment.php">U PAY UP</a></li>
            <li><a href="../admin/purchase.php">U BUY</a></li>
            <li><a href="../admin/add_user.php">ADD U</a></li>
		</ul>

