<html>
    <head>
        <title>FridayPub Users's Area</title>
    </head>
    <body>
        <?php
            include_once "user_header.php";
            include_once "fpdb.php";

            $user_id = $_SESSION["user_id"];
            try {
                $db = new FPDB($_SESSION["credentials"]);
                $iou = $db->iou_get($user_id)->next();
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            extract($iou);
            printf("<h1>%dkr</h1></br>", $amount);


        ?>
    </body>
</html>
