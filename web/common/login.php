<?php
    session_start() || die("Failed to start session.");

    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    include_once "../fpdb/fpdb.php";
    try {
        $db = new FPDB_User();
        $qres = $db->user_get($username)->current();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }

    if (!$qres) {
        die("Unknown username: " . $username);
    }

    if ($qres["password"] != md5($password)) {
        die("Did you forget your password? Sorry your fucked.");
    }

    $_SESSION["loggedin"] = True;
    $_SESSION["username"] = $username;
    $_SESSION["user_id"] = $qres["user_id"];
    $_SESSION["first_name"] = $qres["first_name"];
    $_SESSION["last_name"] = $qres["last_name"];
    $_SESSION["credentials"] = $qres["credentials"];
    
    switch ($qres["credentials"]) {
        case CRED_ADMIN: //defined in init/credentials.inc.php through fpdb.php
            header("location: ../admin/welcome.php");
            break;
        case CRED_USER:
            header("location: ../user/welcome.php");
            break;
        default:
            /* TODO! Handle error */
            break;
    }
?> 
