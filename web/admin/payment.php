<?php
    include_once "../admin/header.php";
    include_once "../fpdb/fpdb.php";

    try {
        $db = new FPDB_Admin();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <select name = "user_id">
        <?php
            try {
                $qres = $db->user_get_all();
            } catch (FPDB_Exception $e) {
                die($e->getMessage());
            }

            foreach ($qres as $user) {
                printf("<option value = %d> %s %s </option>",
                    $user["user_id"], $user["first_name"], $user["last_name"]);
            }
        ?>
    </select>
    amount: <input type="text" required="required" name="amount"/>
            <input type="submit" name="submit" value="Register"/>
</form>

<?php
    if (isset($_POST["submit"])) {
        $admin_id = $_SESSION["user_id"];
        extract($_POST);

        try {
            $db->payment_append($user_id, $admin_id, $amount);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }

        printf("Payment registered for %d kr\n", $amount);
    }
    include_once "footer.php"; 
?>