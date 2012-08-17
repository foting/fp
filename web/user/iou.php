<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";

    $user_id = $_SESSION["user_id"];
    try {
        $db = new FPDB_User();
        $iou = $db->iou_get($user_id)->next();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    extract($iou);
    if ($assets >= 0) {
        printf("<img class=\"face\" src=\"../images/good.png\">");
        printf("<h1>I'm good!</h1>");
        printf("Many money in the bank:");
    }
    else
        {
        printf("<img class=\"face\" src=\"../images/bad.png\">");
        printf("<h1>Oh nooo!</h1> Y U put me in debt?");
    }
    echo "<h1>$assets kr</h1>";
    //printf("%d kr", $assets); 

    include_once "footer.php"; 
?>