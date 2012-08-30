<?php
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
    	<meta charset="UTF-8">
    	<link rel="stylesheet" href="../css/friday_pub.css">
    	<link href='http://fonts.googleapis.com/css?family=Fredoka+One' rel='stylesheet' type='text/css'>
        <title>FridayPub</title>
    </head>
    <body>
    	<h1>FridayPub</h1>
        <div class="logout">Logged in as:
            <?php echo $_SESSION["username"]; ?>
        	<a href="../common/logout.php">(logout)</a>
        </div>
        <ul class="menu">
            <li><a href="../user/buy_beer.php">BUY BEER</a></li>
            <li><a href="../user/iou.php">BANK</a></li>
        </ul>
        <div class="clearfix"></div>
