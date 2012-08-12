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

                /* For each beer in inventory, insert row a in the table */
                foreach ($db as $inventory_item) {
                    extract($inventory_item);

                    try {
                        $beer = $db->snapshot_get($beer_id);
                    } catch (FPDBException $e) {
                        die($e->getMessage());
                    }

                    printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>", 
                        $username, $first_name, $last_name, $amount);
                }
            ?>
        </table>
    </body>
</html>
