<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";

    $user_id = $_SESSION["user_id"];
    $assets = 0;

    /*
     * Returns the array of purchase data
     */
    function getPurchases($db, $user_id) {
        $qres;
            try {
            $qres = $db->purchases_get($user_id);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
        return $qres;
    }

     /*
     * Returns the array of payment data
     */
    function getPayments($db, $user_id) {
        $qres;
            try {
            $qres = $db->payments_get($user_id);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
        return $qres;
    }
    
    function formatPurchases($qres)
    {
        $p_table = "";
        $p_table .= "<div class=\"tablewrapper\">";
        $p_table .= "<h2>Purchases</h2>";
        $p_table .= "<table class=\"history\">";
        foreach ($qres as $purchase)
        {
            $p_table .= sprintf("<tr><th>%s</th><td>%s (%d)</td><td class=\"right\">%d kr</td></tr>",
                $purchase["timestamp"], $purchase["name"], $purchase["beer_id"], $purchase["price"]);
        }  
        $p_table .= "</table>";
        $p_table .= "</div>";
        
        return $p_table;
    }
    
    function formatPayments($qres)
    {
        $p_table = "";
        $p_table .= "<div class=\"tablewrapper\">";
        $p_table .= "<h2>Payments</h2>";
        $p_table .= "<table class=\"history\">";
        foreach ($qres as $payment)
        {
            $p_table .= sprintf("<tr><th>%s</th><td class=\"right\">%d kr</td></tr>",
                $payment["timestamp"], $payment["amount"]);
        }
        $p_table .= "</table>";
        $p_table .= "</div>";
        
        return $p_table;
    }
    
    $db;
    try {
        $db = new FPDB_User();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
    
    $assets = 0;

    $purchases = getPurchases($db, $user_id);
    foreach ($purchases as $p)
        $assets -= $p["price"];
    $payments = getPayments($db, $user_id);
    foreach ($payments as $p)
        $assets += $p["amount"];
        
    if ($assets >= 0) {
        printf("<div class=\"assets\"><h1>I'm good!</h1>");
        printf("Many monies in the bank:");
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
    echo formatPurchases($purchases);
    
    /* List payments */
    echo formatPayments($payments);
    
    include_once "footer.php"; 
?>
