<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
        ?>

        <table border="1">
            <tr>
                <th>username</th>
                <th>first_name</th>
                <th>last_name</th>
                <th>amount</th>

            </tr>

            <?php
                include_once "fpdb.php";

                try {
                    $db = new FPDB();
                    $db->iou_get();
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                foreach ($db as $user_iou) {
                    extract($user_iou);

                    printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>", 
                        $username, $first_name, $last_name, $amount);
                }
            ?>
        </table>
    </body>
</html>
