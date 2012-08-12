<?php
    session_start() or die("Couldn't start session");

    echo "FridayPub User's Area</br>";
    echo "Logged in as: <b>${_SESSION["username"]}</b> ";
?>

<a href="logout.php">(logout)</a> </br>
<a href="user_buybeer.php">BUY BEER</a>
<a href="user_iou.php">IOU</a>
<a href="user_history.php">HISTORY</a>
<hr>
