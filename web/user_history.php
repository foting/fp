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
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            /* List purchases */
            try {
                $db->purchase_get($user_id);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            printf("Purchases:</br>");
            foreach ($db as $purchase) {
                printf("%s: %s</br>",
                    $purchase["timestamp"], beer_name($purchase["beer_id"]));
            }
            printf("<hr>");
            
            /* List payments */
            try {
                $db->payment_get($user_id);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            printf("Payments:</br>");
            foreach ($db as $payment) {
                printf("%s: %dkr</br>",
                    $payment["timestamp"], $payment["amount"]);
            }
        ?>
    </body>
</html>
