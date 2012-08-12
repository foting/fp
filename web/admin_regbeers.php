<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php"
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table>
                <tr>
                    <td> beer_id </td>
                    <td> amount </td>
                    <td> price </td>
                </tr>
                <tr>
                    <td><input type="text" required="required" name="beer_id"/></td>
                    <td><input type="text" required="required" name="amount"/></td>
                    <td><input type="text" required="required" name="price"/></td>
                    <td><input type="submit" name="submit" value="Register"/></td>
                </tr>
            </table>
        </form>

        <?php
            if (isset($_POST["submit"])) {
                include_once "credentials.php";

                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $user_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    include_once "fpdb.php";

                    $db = new FPDB();
                    $db->inventory_append($user_id, $beer_id, $amount, $price);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }
            }
        ?>
    </body>
</html>
