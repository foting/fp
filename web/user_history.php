<html>
    <head>
        <title>FridayPub Users's Area</title>
    </head>
    <body>
        <?php
            include_once "user_header.php";
            include_once "fpdb.php";

            include_once "snapshot_hack.php";

            $user_id = $_SESSION["user_id"];
            
            try {
                $db = new FPDB($_SESSION["credentials"]);
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            /* List purchases */
            try {
                $qres = $db->purchase_get($user_id);
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            printf("Purchases:</br>");
            foreach ($qres as $purchase) {
                printf("%s: %s %d</br>",
                    $purchase["time_bought"], beer_name($purchase["beer_id"]), $purchase["price"]);
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
        ?>
    </body>
</html>
