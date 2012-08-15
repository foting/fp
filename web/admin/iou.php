<?php
    include_once "header.php";
    include_once "../common/fpdb.php";

    try {
        $db = new FPDB_Admin();
    } catch (FPDB_Exception $e) {
        die($e->getMessage());
    }
?>

<table>
    <tr>
        <th>username</th>
        <th>first_name</th>
        <th>last_name</th>
        <th>assets</th>

    </tr>

    <?php
        try {
            $qres = $db->iou_get_all();
        } catch (FPDB_Exception $e) {
            die($e->getMessage());
        }

        foreach ($qres as $user_iou) {
            extract($user_iou);

            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>", 
                $username, $first_name, $last_name, $assets);
        }
    ?>
</table>
<?php 
    include_once "footer.php"; 
?>