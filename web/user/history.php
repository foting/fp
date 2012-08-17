<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";

    try {
        $db = new FPDB_User();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    $user_id = $_SESSION["user_id"];

    /* List purchases */
    try {
        $qres = $db->purchase_get($user_id);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    printf("Purchases:</br>");
    printf("<table class=\"history\">");
    foreach ($qres as $purchase) {
        printf("<tr><th>%s</th><td>%s (%d)</td><td>%d kr</td></tr>",
            $purchase["timestamp"], $purchase["namn"], $purchase["beer_id"], $purchase["price"]);
    }
    printf("</table><hr>");
    
    /* List payments */
    try {
        $qres = $db->payment_get($user_id);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    printf("Payments:</br>");
    printf("<table class=\"history\">");
    foreach ($qres as $payment) {
        printf("<tr><th>%s</th><td>%d kr</td></tr>",
            $payment["timestamp"], $payment["amount"]);
    }
    printf("</table><hr>");
    include_once "footer.php"; 
?>