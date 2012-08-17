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
    <p>Username:   <input type="text" required="required" name="username" /></p>
    <p>First name: <input type="text" required="required" name="first_name" /></p>
    <p>Last name:  <input type="text" required="required" name="last_name"  /></p>
    <p>Email:      <input type="text" required="required" name="email"      /></p>
    <p>Phone:      <input type="text" required="required" name="phone"      /></p>
    <p>Password:   <input type="text" required="required" name="password"   /></p>
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

