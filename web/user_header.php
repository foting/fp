<?php
    session_start() or die("Couldn't start session");
    $username = $_SESSION["username"];

    echo "FridayPub User's Area - logged in as: ${username}</br>";
?>
    <a href="user_buybeer.php">BUY BEER</a>
    <a href="user_iou.php">IOU</a>
    <a href="user_history.php">HISTORY</a>
    <hr>
