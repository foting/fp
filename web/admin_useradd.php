<html>
    <head>
        <title>FridayPub Admin's Area</title>
    </head>
    <body>
        <?php
            include_once "admin_header.php";
            include_once "fpdb.php";

            try {
                $db = new FPDB($_SESSION["credentials"]);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <p>Username:   <input type="text" required="required" name="username" /></p>
            <p>First name: <input type="text" required="required" name="first_name" /></p>
            <p>Last name:  <input type="text" required="required" name="last_name"  /></p>
            <p>Email:      <input type="text" required="required" name="email"      /></p>
            <p>Phone:      <input type="text" required="required" name="phone"      /></p>
            <p>Password:   <input type="text" required="required" name="password"   /></p>
            <p><input type="submit" name="submit" value="Register"/></p>
        </form>

        <?php
            if (isset($_POST["submit"])) {
                extract($_POST);

                try {
                    $db->user_append($username, $password, $first_name, $last_name, $email, $phone);
                } catch (FPDBException $e) {
                    die($e->getMessage());
                }

                printf("User %s successfully added to database", $username);
            }
        ?>
    </body>
</html>
