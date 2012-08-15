<?php
    include_once "header.php";
    
    $first_name = $_SESSION["first_name"];
    $last_name = $_SESSION["last_name"];

    printf("Welcome %s %s\n", $first_name, $last_name);
    include_once "footer.php";

?>

