<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            $user_name = $_POST["user_name"];
            $password = $_POST["password"];

            /* Check user credentials */

            /* For early testing purposes, set credential string to $user_name */
            $credentials = $user_name;

            switch ($credentials) {
                case "user":
                    include_once "user_purchase.php";
                    break;
                case "admin":
                    include_once "admin_dashboard.php";
                    break;
                default:
                    /* XXX Handle error */
                    break;
            }
        ?> 
    </body>
</html>
