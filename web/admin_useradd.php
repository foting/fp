<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";

        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <p>First Name: <input type="text" required="required" name="first_name" /></p>
            <p>Last Name: <input type="text" required="required" name="last_name" /></p>
            <p>Email: <input type="text" required="required" name="email" /></p>
            <p>Password: <input type="text" required="required" name="password" /></p>
            <p><input type="submit"/></p>
        </form>

        <?php
            if ($_POST) {
                include_once "credentials.php";
                $credentials = $_SESSION["credentials"];
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                extract($_POST); /* $first_name, $last_name, $password, $email*/
                $user_name = $email;

                include_once "fpdb.php";
                try {
                    $fpdb = new FPDB();
                    $fpdb->user_set($user_name, $password, $first_name, $last_name, $email);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                printf("User %s successfully added to database", $user_name);
            }
        ?>

    </body>
</html>
