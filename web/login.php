<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            $posted_name = $_POST["user_name"];
            $posted_passwd = $_POST["password"];

            if (!$posted_name) {
                die("Please enter your user name.");
            }
            if (!$posted_passwd) {
                die("Please enter password user name.");
            }

            /* Check password and  credentials */
            try {
                include_once "fpdb.php";
                $db = new FPDB();
                $user = $db->user_get($posted_name);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            if (!$user) {
                die("Unknown user name.");
            }

            if ($user["password"] != md5($posted_passwd)) {
                die("Forgot your password? Sorry your fucked.");
            }

            /* XXX We are losing session information */
            session_start();
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["last_name"] = $user["last_name"];
            $_SESSION["credentials"] = $user["credentials"];

            include_once "credentials.php";
            switch ($user["credentials"]) {
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
