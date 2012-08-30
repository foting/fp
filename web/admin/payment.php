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
            ?>
            <option value="0">&nbsp;* Select user *&nbsp;</option>
            <?php
            foreach ($qres as $user) {
                if ($_POST["user_id"] == $user["user_id"])
                    printf("<option value = %d selected=\"selected\"> %s %s </option>",
                        $user["user_id"], $user["first_name"], $user["last_name"]);
                else
                    printf("<option value = %d> %s %s </option>",
                        $user["user_id"], $user["first_name"], $user["last_name"]);
            }
        ?>
    </select>
    amount: <input type="text" required="required" name="amount" pattern="[0-9\-]*"/>
            <input type="submit" name="submit" value="Register"/>
</form>

<?php
    if (isset($_POST["submit"]) && $_POST["user_id"]) {
        $admin_id = $_SESSION["user_id"];
        extract($_POST);
        
        try {
            $db->payments_append($user_id, $admin_id, $amount);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }

        printf("Bro in above drop-down menu has payed up %d kr.<br> Pub sez KTHXBYE.\n", $amount);
    }
    include_once "footer.php"; 
?>
