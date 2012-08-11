<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            $posted_name = $_POST["user_name"];
            $posted_passwd = $_POST["password"];

            /* Check password and  credentials */
            include_once "my_mysql.php";
            if (!my_mysql_connect()) {
                die("Couldn't connect to database: " . mysql_error());
            }

            $query = sprintf("SELECT user_id, credentials, password FROM users
                              WHERE user_name = '%s';", $posted_name);

            $result = mysql_query($query);
            if (!$result) {
                die("Invalid query: " . mysql_error());
            }

            if (mysql_num_rows($result) < 1) {
                /* XXX Handle this. Go back to login page? */
                die("User not found");
            }

            $record = mysql_fetch_assoc($result);
            extract($record); /* To get: $user_id, $credentials, $password */

            mysql_free_result($result);
            mysql_close();

            if ($password != md5($posted_passwd)) {
                /* XXX Handle this. Go back to login page? */
                die("Wrong password");
            }

            $_SESSION["user_id"] = $user_id;
            $_SESSION["credentials"] = $credentials;

            include_once "credentials.php";
            switch ($credentials) {
                case CRED_ADMIN:
                    include_once "admin_dashboard.php";
                    break;
                case CRED_USER:
                    include_once "user_purchase.php";
                    break;
                default:
                    /* XXX Handle error */
                    break;
            }
        ?> 
    </body>
</html>
