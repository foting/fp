<?php
    include_once "../include/credentials.php";
    
    if (!session_start()) {
        die("Couldn't start session");
    }

    if (!isset($_SESSION["loggedin"])) {
        head("../index.html");
    }
    include_once "../fpdb/fpdb.php";

    try {
        $db = new FPDB_Admin($_SESSION["credentials"]);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
    $beer_id = $_GET["beer_id"];
    $qres = $db->beer_data_get($beer_id);
    foreach ($qres as $beer) {
        echo $beer["namn"];
        echo " ";
        echo $beer["namn2"];
        echo "<input type=text class=\"beer_price\" name=\"price\" value=\"".$beer["prisinklmoms"]."\">";
    }
?>