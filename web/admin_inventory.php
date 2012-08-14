<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
            include_once "fpdb.php";
            include_once "snapshot_hack.php";

            try {
                $db = new FPDB($_SESSION["credentials"]);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }
        ?>

        <table border="1">
            <tr>
                <th>Beer</th>
                <th>Amount</th>
            </tr>

            <?php
                try {
                    $qres = $db->inventory_get();
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                /* For each beer in inventory, insert row a in the table */
                foreach ($qres as $inventory_item) {
                    $beer_id = $inventory_item["beer_id"];
                    $count = $inventory_item["count"];

                    printf("<tr><td>%s</td><td>%d</td></tr>",
                        beer_name($beer_id), $count);
                }
            ?>

        </table>
    </body>
</html>
