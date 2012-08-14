<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
            include_once "fpdb.php";

            try {
                $db = new FPDB_Admin($_SESSION["credentials"]);
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }
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
                $user_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    $db->inventory_append($user_id, $beer_id, $amount, $price);
                } catch (FPDB_Exception $e) {
                    die($e->getMessage());
                }
            }
        ?>
    </body>
</html>
