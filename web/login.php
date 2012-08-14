<?php
    session_start() || die("Failed to start session.");

    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    include_once "fpdb.php";
    try {
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
    
    switch ($user["credentials"]) {
        case CRED_ADMIN: //defined in init/credentials.inc.php through fpdb.php
            header("location: admin_welcome.php");
            break;
        case CRED_USER:
            header("location: user_welcome.php");
            break;
        default:
            /* TODO! Handle error */
            break;
    }
?> 
