<?php
    include_once "../fpdb/fpdb.php";
    include_once "../include/credentials.php";

    define("ERROR_USERNAME",  1);
    define("ERROR_PASSWORD",  2);
    define("ERROR_CRED",      3);
    define("ERROR_DATABASE",  4);
    define("ERROR_ACTION",    5);
    define("ERROR_ARGUMENTS", 6);

    $error_strings = array(
            ERROR_USERNAME  => "Username not found",
            ERROR_PASSWORD  => "Wrong password (your fucked)",
            ERROR_CRED      => "Not enough credentials",
            ERROR_DATABASE  => "Database error",
            ERROR_ACTION    => "Unknown action requested",
            ERROR_ARGUMENTS => "Wrong number or type of arguments",
    );

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

    /*
     * Helper functions
     */ 
    function return_error($code)
    {
        global $error_strings;

        $payload = array(array("code" => $code, "msg" => $error_strings[$code]));

        $reply = new API_Reply("error", $payload);
        echo my_json_encode($reply);
        exit(-1);
    }

    function check_credentials($have, $need)
    {
        if ($have > $need) {
            return_error(ERROR_CRED);
        }
    }

    /* Not all servers at uni have json_encode, hence this wrapper */
    function my_json_encode($reply)
    {
        if (function_exists(json_encode)) {
            return json_encode($reply);
        } else {
            $json = "{\"type\" : \"$reply->type\", \"payload\" : [";
            foreach ($reply->payload as $record) {
                $json .= "{";
                foreach ($record as $key => $val) {
                    $json .= "\"$key\" : \"$val\",";
                }
                $json = rtrim($json, ",");
                $json .= "},";
            }
            $json = rtrim($json, ",");
            $json .= "]}";
            return $json;
        }
    }

    /*
     * Functions to handle action requests
     */
    function action_inventory_get($db, $user_id)
    {
        $qres = $db->inventory_get_all()->get_array();
        return new API_Reply("inventory_get", $qres);
    }

    function action_beer_data_get($db, $beer_id)
    {
        $qres = $db->beer_data_get($beer_id)->get_array();
        return new API_Reply("beer_data_get", $qres);
    }

    function action_purchases_get($db, $user_id)
    {
        $qres = $db->purchases_get($user_id)->get_array();
        return new API_Reply("purchases_get", $qres);
    }

    function action_purchases_get_all($db, $user_id)
    {
        $qres = $db->purchases_get_all()->get_array();
        return new API_Reply("purchases_get_all", $qres);
    }

    function action_purchases_append($db, $user_id)
    {
        $beer_id = $_GET["beer_id"];
        if (!$beer_id) {
            return_error(ERROR_ARGUMENTS);
        }
        $db->purchases_append($user_id, $beer_id);
        return new API_Reply("empty");
    }

    
    function action_payments_get($db, $user_id)
    {
        $qres = $db->payments_get($user_id)->get_array();
        return new API_Reply("payments_get_all", $qres);
    }

    function action_payments_get_all($db, $user_id)
    {
        $qres = $db->payments_get_all()->get_array();
        return new API_Reply("payments_get_all", $qres);
    }

    function action_payments_append($db, $admin_id)
    {
        $user_id = $_GET["user_id"];
        $amount  = $_GET["amount"];
        if (!$user_id || !$amount) {
            return_error(ERROR_ARGUMENTS);
        }
        /* XXX We might want to check if the supplied user_id is valid */
        $db->payments_append($user_id, $admin_id, $amount);
        return new API_Reply("empty");
    }


    function action_iou_get($db, $user_id)
    {
        $qres = $db->iou_get($user_id)->get_array();
        return new API_Reply("iou_get", $qres);
    }

    function action_iou_get_all($db, $user_id)
    {
        $qres = $db->iou_get_all()->get_array();
        return new API_Reply("iou_get_all", $qres);
    }


    $username = $_GET["username"];
    $password = $_GET["password"];

    if (!$username || !$password) {
        return_error(ERROR_ARGUMENTS);
    }
    
    /* Check username and password */
    try {
        $db = new FPDB_User();
        $qres = $db->user_get($username)->current();
    } catch (FPDB_Exception $e) {
        return_error(ERROR_DATABASE);
    }

    if (!$qres) {
        return_error(ERROR_USERNAME);
    }

    if ($qres["password"] != md5($password)) {
        return_error(ERROR_PASSWORD);
    }

    $user = $qres["user_id"];
    $cred = $qres["credentials"];

    /* Reconnect to database with correct credentials */
    try {
        if ($cred == CRED_ADMIN) {
            unset($db); /* Close connection */
            $db = new FPDB_Admin();
        }
    } catch (FPDB_Exception $e) {
        return_error(ERROR_DATABASE);
    }

    $action = $_GET["action"];
    if (!$action) {
        return_error(ERROR_ARGUMENTS);
    }

    /* Perform requested action */
    try {
        switch ($action) {
            case "inventory_get":
                check_credentials($cred, CRED_USER);
                $reply = action_inventory_get($db, $user);
                break;

            case "purchases_get":
                check_credentials($cred, CRED_USER);
                $reply = action_purchases_get($db, $user);
                break;

            case "purchases_get_all":
                check_credentials($cred, CRED_ADMIN);
                $reply = action_purchases_get_all($db, $user);
                break;

            case "purchases_append":
                check_credentials($cred, CRED_USER);
                $reply = action_purchases_append($db, $user);
                break;

            case "payments_get":
                check_credentials($cred, CRED_USER);
                $reply = action_payments_get($db, $user);
                break;

            case "payments_get_all":
                check_credentials($cred, CRED_ADMIN);
                $reply = action_payments_get_all($db, $user);
                break;

            case "payments_append";
                check_credentials($cred, CRED_USER);
                $reply = action_payments_append($db, $user);
                break;

            case "iou_get":
                check_credentials($cred, CRED_USER);
                $reply = action_iou_get($db, $user);
                break;

            case "iou_get_all":
                check_credentials($cred, CRED_ADMIN);
                $reply = action_iou_get_all($db, $user);
                break;
            case "beer_data_get":
                //currently not used. Need to think about password passing in javascript
                check_credentials($cred, CRED_USER);
                $reply = action_beer_data_get($db, $_GET["beer_id"]);
                break;

            default:
                return_error(ERROR_ACTION);
                break;
        }
    } catch (FPDB_Exception $e) {
        return_error(ERROR_DATABASE);
    }

    echo my_json_encode($reply);
?>
