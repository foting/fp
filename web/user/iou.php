<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";


    function getAssets($db, $user_id)
    {
        try {
            $qres = $db->iou_get($user_id);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
        return $qres;
    }

    /*
     * Returns the array of purchase data
     */
    function getPurchases($db, $user_id)
    {
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
    function getPayments($db, $user_id)
    {
        $qres;
        try {
            $qres = $db->payments_get($user_id);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
        return $qres;
    }

    function formatAssets($qres)
    {
        $assets = $qres->get();
        $assets = $assets[0]["assets"];

        $p_str = "";
        if ($assets >= 0) {
            $p_str .= "<div class=\"assets\"><h1>I'm good!</h1>";
            $p_str .= "Many monies in the bank:";
            $p_str .= "<h1>" . $assets . "kr</h1></div>";
            $p_str .= "<img class=\"face\" src=\"../images/good.png\">";
        } else {
            $p_str .= "<div class=\"assets\"><h1>Oh nooo!</h1> Y U NO free beer?";
            $p_str .= "<h1>" . $assets .  "kr</h1></div>";
            $p_str .= "<img class=\"face\" src=\"../images/bad.png\">";
        }

        return $p_str;
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
    
    $user_id = $_SESSION["user_id"];
    try {
        $db = new FPDB_User();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
    
    $assets = getAssets($db, $user_id);
    $purchases = getPurchases($db, $user_id);
    $payments = getPayments($db, $user_id);
        
   
    echo formatAssets($assets);
    echo formatPurchases($purchases);
    echo formatPayments($payments);
    
    include_once "footer.php"; 
?>
