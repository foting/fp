<?php
    include_once "header.php";
    include_once "../common/fpdb.php";

    try {
        $db = new FPDB_Admin($_SESSION["credentials"]);
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table>
        <tr>
            <td> beer_id </td>
            <td> amount </td>
            <td> price </td>
        </tr>
        <tr>
            <td><input type="text" required="required" name="beer_id"/></td>
            <td><input type="text" required="required" name="amount"/></td>
            <td><input type="text" required="required" name="price"/></td>
            <td><input type="submit" name="submit" value="Register"/></td>
        </tr>
    </table>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Update database with the lastest info from systembolaget:
    <input type="submit" name="submit_sbl" value="update"/>
</form>

<?php
    if (isset($_POST["submit"])) {
        $user_id = $_SESSION["user_id"];
        extract($_POST);

        try {
            $db->inventory_append($user_id, $beer_id, $amount, $price);
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
    } 

    if (isset($_POST["submit_sbl"])) {
        try {
            /* Hardcode file path for now. */
            sbl_insert_snapshot($db, "../sbl-latest.xml");
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }
    }
    include_once "footer.php"; 
?>