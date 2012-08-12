<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            session_start() || die("Failed to start session.");

            $username = $_POST["username"];
            $password = $_POST["password"];

            /* Check password and  credentials */
            try {
                include_once "fpdb.php";
                $db = new FPDB();
                $user = $db->user_get($username);
            } catch (FPDBException $e) {
                die($e->getMessage());
            }

            if (!$user) {
                die("Unknown username: " . $username);
            }

            if ($user["password"] != md5($password)) {
                die("Did you forget your password? Sorry your fucked.");
            }

            $_SESSION["username"] = $username;
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
