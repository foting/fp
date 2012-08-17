<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";

    try {
        $db = new FPDB_Admin();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<table>
    <tr>
        <th>Beer</th>
        <th>Amount</th>
    </tr>

    <?php
        try {
            $qres = $db->inventory_get_all();
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }

        /* For each beer in inventory, insert row a in the table */
        foreach ($qres as $inventory_item) {
            $beer_name = $inventory_item["namn"];
            $beer_id = $inventory_item["beer_id"];
            $count = $inventory_item["count"];

            printf("<tr><td>%s (%s)</td><td>%d</td></tr>",
                $beer_name, $beer_id, $count);
        }
    ?>

</table>
<?php 
    include_once "footer.php"; 
?>
