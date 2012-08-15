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
                $db = new FPDB_User();
                $iou = $db->iou_get($user_id)->next();
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            extract($iou);
            printf("<h1>%dkr</h1></br>", $assets);


        ?>
    </body>
</html>
