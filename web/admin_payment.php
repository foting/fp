<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php"
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <p>user_name: <input type="text" required="required" name="user_name" /></p>
            <p>amount:    <input type="text" required="required" name="amount"    /></p>
            <p>           <input type="submit"/></p>
        </form>

        <?php
            if ($_POST) {
                include_once "credentials.php";

                session_start();
                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $admin_id = $_SESSION["user_id"];
                extract($_POST);

                try {
                    include_once "fpdb.php";

                    $db = new FPDB();
                    $db->payment_append($user_name, $admin_id, $amount);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                printf("Payment of %d registered for %s\n",
                        $amount, $user_name);
            }
        ?>
    </body>
</html>
