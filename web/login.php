<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            $posted_name = $_POST["user_name"];
            $posted_passwd = $_POST["password"];

            /* Check password and  credentials */
            include_once "fpdb.php";
            try {
                $fpdb = new FPDB();
                $fpdb_result = $fpdb->user_get($posted_name);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }
            extract($fpdb_result);

            if ($password != md5($posted_passwd)) {
                /* XXX Handle this. Go back to login page? */
                die("Wrong password");
            }

            $_SESSION["user_id"] = $user_id;
            $_SESSION["first_name"] = $first_name;
            $_SESSION["last_name"] = $last_name;
            $_SESSION["credentials"] = $credentials;

            include_once "credentials.php";
            switch ($credentials) {
                case CRED_ADMIN:
                    include_once "admin_welcome.php";
                    break;
                case CRED_USER:
                    include_once "user_welcome.php";
                    break;
                default:
                    /* XXX Handle error */
                    break;
            }
        ?> 
    </body>
</html>
