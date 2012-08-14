<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
            include_once "fpdb.php";

            try {
                $db = new FPDB($_SESSION["credentials"]);
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }
        ?>

        <table border="1">
            <tr>
                <th>username</th>
                <th>first_name</th>
                <th>last_name</th>
                <th>amount</th>

            </tr>

            <?php
                try {
                    $qres = $db->iou_get();
                } catch (FPDB_Exception $e) {
                    die($e->getMessage());
                }

                foreach ($qres as $user_iou) {
                    extract($user_iou);

                    printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>", 
                        $username, $first_name, $last_name, $amount);
                }
            ?>
        </table>
    </body>
</html>
