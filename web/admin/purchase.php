<?php
    include_once "header.php";
    include_once "../common/fpdb.php";

    try {
        $db = new FPDB_Admin();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <select name = "user_id">

        <?php
            /* User dropdown */
            try {
                $qres = $db->user_get_all();
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            foreach ($qres as $user) {
                printf("<option value = %d> %s %s </option>",
                    $user["user_id"], $user["first_name"], $user["last_name"]);
            }
        ?>

    </select>
    <select name = "beer_id">
        
        <?php
            /* Beer dropdown */
            try {
                $qres = $db->inventory_get_all();
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            foreach ($qres as $inventory_item) {
                $beer_name = $inventory_item["namn"];
                $beer_id = $inventory_item["beer_id"];

                printf("<option value = %d> %s </option>", 
                    $beer_id, $beer_name);
            }
        ?>

    </select>
    <input type="submit" name="submit" value="Register"/>
</form>


<?php
    if (isset($_POST["submit"])) {
        $admin_id = $_SESSION["user_id"];
        extract($_POST);

        try {
            $db->purchase_append($user_id, $beer_id);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
    }
    include_once "footer.php"; 
?>

