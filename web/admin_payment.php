<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php"
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <p>username: <input type="text" required="required" name="username" /></p>
            <p>amount:    <input type="text" required="required" name="amount"    /></p>
            <p>           <input type="submit"/></p>
        </form>

        <?php
            if ($_POST) {
                include_once "credentials.php";

                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $admin_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    include_once "fpdb.php";

                    $db = new FPDB();
                    $db->payment_append($username, $admin_id, $amount);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                printf("Payment of %d registered for %s\n",
                        $amount, $username);
            }
        ?>
    </body>
</html>
