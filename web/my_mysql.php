<?php
    function my_mysql_connect()
    {
        $rc = true;

        /* XXX Get this data from a file */
        $db_server = "steam.it.uu.se";
        $db_user = "admin";
        $db_passwd = "fp_at_polacks";
        $db_database = "fp_test";

        $rc = $rc and mysql_connect($db_server, $db_user, $db_passwd);
        $rc = $rc and mysql_select_db($db_database);

        return $rc;
    }
?>

