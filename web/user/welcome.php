<html>
    <head>
        <title>FridayPub User's Area</title>
    </head>
    <body>
        <?php
            include_once "../user/header.php";
            
            $first_name = $_SESSION["first_name"];
            $last_name = $_SESSION["last_name"];

            printf("Welcome %s %s\n", $first_name, $last_name); 
        ?>
    </body>
</html>