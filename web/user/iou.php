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
        printf("<div class=\"assets\"><h1>I'm good!</h1>");
        printf("Many money in the bank:");
        printf("<h1>%d kr</h1></div>", $assets);
        printf("<img class=\"face\" src=\"../images/good.png\">");
    }
    else
        {
        printf("<div class=\"assets\"><h1>Oh nooo!</h1> Y U NO free beer?");
        printf("<h1>%d kr</h1></div>", $assets);
        printf("<img class=\"face\" src=\"../images/bad.png\">");
    }
    

        /* List purchases */
    try {
        $qres = $db->purchase_get($user_id);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    printf("<div class=\"tablewrapper\">");
    printf("<h2>Purchases</h2>");
    printf("<table class=\"history\">");
    foreach ($qres as $purchase) {
        printf("<tr><th>%s</th><td>%s (%d)</td><td class=\"right\">%d kr</td></tr>",
            $purchase["timestamp"], $purchase["namn"], $purchase["beer_id"], $purchase["price"]);
    }
    printf("</table>");
    
    /* List payments */
    try {
        $qres = $db->payment_get($user_id);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    printf("<h2>Payments</h2>");
    printf("<table class=\"history\">");
    foreach ($qres as $payment) {
        printf("<tr><th>%s</th><td class=\"right\">%d kr</td></tr>",
            $payment["timestamp"], $payment["amount"]);
    }
    printf("</table>");
    printf("</div>");
    include_once "footer.php"; 
?>