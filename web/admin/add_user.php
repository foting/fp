<?php
    include_once "header.php";
    include_once "../fpdb/fpdb.php";

    try {
        $db = new FPDB_Admin($_SESSION["credentials"]);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <p><input type="text" required="required" name="username" placeholder="User name"/></p>
    <p><input type="text" required="required" name="first_name" placeholder="First name"/></p>
    <p><input type="text" required="required" name="last_name" placeholder="Family name"/></p>
    <p><input type="email" required="required" name="email" placeholder="Email"/></p>
    <p><input type="tel" required="required" name="phone" pattern="[+0-9\-]*" placeholder="Phone"/></p>
    <p><input type="password" required="required" name="password" placeholder="Password"/></p>
    <p><input type="submit" name="submit" value="Register"/></p>
</form>

<?php
    if (isset($_POST["submit"])) {
        extract($_POST);

        try {
            $db->user_append($username, $password, $first_name, $last_name, $email, $phone);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }

        printf("User %s successfully added to database", $username);
    }
    include_once "footer.php"; 
?>

