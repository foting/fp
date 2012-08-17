<?php
    include_once "../fpdb/fpdb.php";
    include_once "../include/credentials.php";

    define("DEBUG", True);

    class API_Reply
    {
        public $type;
        public $payload;
        
        function __construct($type, $payload = array())
        {
            $this->type = $type;
            $this->payload = $payload;
        }
    }

    function debug_output($msg, $var)
    {
        if (DEBUG) {
            echo "$msg: ";
            if ($var) {
                print_r($var);
            } else {
                echo "empty";
            }
            echo "</br>";
        }
    }

    function return_error($msg)
    {
        $jres = new API_Reply("error", array("error" => $msg));
        //echo json_encode($jres);
        print_r($jres);
        exit(-1);
    }

    function check_credentials($cred)
    {
        if ($_SESSION["credentials"] > $cred) {
            return_error("Not enough credentails");
        }
    }

    /*
     * Functions to handle requests
     */
    function api_login($db)
    {
        $username = $_GET["username"];
        $password = $_GET["password"];
 
        $qres = $db->user_get($username)->next();

        if (!$qres) {
            return_error("User name not found");
        }

        if ($qres["password"] == md5($password)) {
            $_SESSION["active"] = True;
            $_SESSION["user_id"] = $qres["user_id"];
            $_SESSION["credentials"] = $qres["credentials"];
        } else {
            return_error("Login failed");
        }

        $jres = new API_Reply("login");
        //echo json_encode($jres);
        print_r($jres);
    }

    function api_inventory_get($db)
    {
        check_credentials(CRED_ADMIN);
        $qres = $db->inventory_get_all()->get_array();
        $jres = new API_Reply("inventory_get", $qres);
        //echo json_encode($jres);
        print_r($jres);

    }

    function api_iou_get($db)
    {
        check_credentials(CRED_USER);
        $user_id = $_SESSION["user_id"];
        $qres = $db->iou_get($user_id)->get_array();
        $jres = new API_Reply("iou_get", $qres);
        //echo json_encode($jres);
        print_r($jres);
    }

    function api_iou_get_all($db)
    {
        check_credentials(CRED_ADMIN);
        $qres = $db->iou_get_all()->get_array();
        $jres = new API_Reply("iou_get_all", $qres);
        //echo json_encode($jres);
        print_r($jres);
    }

    if (!session_start()) {
        return_error("Failed to start session");
    }

    $action = $_GET["action"];

    debug_output("action", $action);

    if ($action != "login" and !isset($_SESSION["active"])) {
        return_error("Session timed out");
    }

    try {
        $cred = $_SESSION["credentials"];
        if ($action == "login" or $cred == CRED_USER) {
            $db = new FPDB_User();
        } else {
            $db = new FPDB_Admin();
        }
    } catch (FPDB_Exception $e) {
        return_error("Faild to connect to database");
    }

    try {
        switch ($action) {
            case "login":
                api_login($db);
                break;
            case "inventory_get":
                api_inventory_get($db);
                break;
            case "iou_get":
                api_iou_get($db);
                break;
            case "iou_get_all":
                api_iou_get_all($db);
                break;
            default:
                return_error("Unknown action requested");
                break;
        }
    } catch (FPDB_Exception $e) {
        return_error("Database query failed");
    }
?>
