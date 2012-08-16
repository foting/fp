<?php
    include_once "header.php";
    include_once "../common/fpdb.php";
    include_once "../common/snapshot_hack.php";

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
    foreach ($qres as $purchase) {
        //TODO! Fix so that beer name is retrieved in the query.
        printf("%s: %s %d</br>",
            $purchase["timestamp"], beer_name($purchase["beer_id"]), $purchase["price"]);
    }
    printf("<hr>");
    
    /* List payments */
    try {
        $qres = $db->payment_get($user_id);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    printf("Payments:</br>");
    foreach ($qres as $payment) {
        printf("%s: %dkr</br>",
            $payment["timestamp"], $payment["amount"]);
    }
    include_once "footer.php"; 
?>