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
                <th>Beer</th>
                <th>Amount</th>
            </tr>

            <?php
                include_once "fpdb.php";
                try {
                    $db = new FPDB();
                    $db->inventory_get();
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                /* For each beer in inventory, insert row a in the table */
                foreach ($db as $inventory_item) {
                    $beer_id = $inventory_item["beer_id"];
                    $count = $inventory_item["count"];

                    try {
                        $beer = $db->snapshot_get($beer_id);
                    } catch (FPDBException $e) {
                        die($e->getMessage());
                    }

                    printf("<tr><td>%s</td><td>%d</td></tr>", $beer, $count);
                }
            ?>

        </table>
    </body>
</html>
