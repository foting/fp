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
                $credentials = $_SESSION["credentials"];

                include_once "credentials.php";
                if ($credentials != CRED_ADMIN) {
                    die("BUG: Non-admin user is accessing admin area");
                }

                $first_name = $_POST["first_name"];
                $last_name = $_POST["last_name"];
                $email = $_POST["email"];
                $user_name = $email;
                $password = $_POST["password"];

                include_once "my_mysql.php";
                if (!my_mysql_connect()) {
                    die("Couldn't connect to database: " . mysql_error());
                }

                $query = sprintf("INSERT INTO users
                         (credentials, password, user_name, first_name, last_name, email)
                         VALUES (%d, '%s', '%s', '%s', '%s', '%s')",
                         CRED_USER, md5($password), $user_name, $first_name, $last_name, $email);

                if (!mysql_query($query)) {
                    die("Couldn't add user: " .  mysql_error());
                }

                printf("User %s successfully added to database", $user_name);

                mysql_close();
            }
        ?>

    </body>
</html>
