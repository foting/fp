<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
            include_once "fpdb.php";

            try {
                $db = new FPDB();
            } catch (FPDBException $e) {
                die($e->getMessage());
            }
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <select name = "user_id">
                <?php
                    /* User dropdown */
                    try {
                        $db->user_get();
                    } catch (FPDBException $e) {
                        die($e->getMessage());
                    }

                    foreach ($db as $user) {
                        printf("<option value = %d> %s %s </option>",
                            $user["user_id"], $user["first_name"], $user["last_name"]);
                    }
                ?>
            </select>
            <select name = "beer_id">
                <?php
                    /* Beer dropdown */
                    try {
                        $db->inventory_get();
                    } catch (FPDBException $e) {
                        die($e->getMessage());
                    }

                    foreach ($db as $inventory_item) {
                        $beer_id = $inventory_item["beer_id"];

                        try {
                            $beer = $db->snapshot_get($beer_id);
                        } catch (FPDBException $e) {
                            die($e->getMessage());
                        }

                        printf("<option value = %d> %s </option>", $beer_id, $beer);
                    }
                ?>
            </select>
            <input type="submit" name="submit" value="Register"/>
        </form>

        <?php
            if (isset($_POST["submit"])) {
                include_once "credentials.php";

                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $admin_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    $db->purchase_append($user_id, $beer_id);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }
            }
        ?>
    </body>
</html>
