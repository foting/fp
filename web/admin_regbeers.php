<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php"
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <p>beer_id: <input type="text" required="required" name="beer_id" /></p>
            <p>amount:  <input type="text" required="required" name="amount"  /></p>
            <p>price:   <input type="text" required="required" name="price"   /></p>
            <p>         <input type="submit"/></p>
        </form>

        <?php
            if ($_POST) {
                include_once "credentials.php";

                session_start();
                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $user_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    include_once "fpdb.php";

                    $fpdb = new FPDB();
                    $fpdb->inventory_append($user_id, $beer_id, $amount, $price);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }
            }
        ?>
    </body>
</html>
