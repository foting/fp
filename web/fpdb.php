<?php

    class FPDBException extends Exception {

    }

    class FPDB {

        function __construct()
        {
            $ok = True;

            $ok = $ok && mysql_connect("steam.it.uu.se", "admin", "fp_at_polacks");
            $ok = $ok && mysql_select_db("fp_test");

            if (!ok) {
                throw new FPDBException(mysql_error());
            }
        }

        function __destruct()
        {
            mysql_close();
        }

        public function query($query, $fetch = False)
        {
            $ok = True;

            $ok = $ok && ($query_result = mysql_query($query));
            if ($fetch) {
                $ok = $ok && ($fetch_result = mysql_fetch_assoc($query_result));
            }

            if (!ok) {
                throw new FPDBException(mysql_error());
            }
            return $fetch_result;
        }


        public function user_get($user_name)
        {
            /* Assuming that user_name is unique */
            $query = sprintf("SELECT * FROM users WHERE user_name = '%s'", $user_name);
            return $this->query($query, True);
        }

        public function user_set($user_name, $password, $first_name, $last_name, $email)
        {
            include_once "credentials.php";
            $query = sprintf("INSERT INTO users
                     (credentials, password, user_name, first_name, last_name, email)
                     VALUES (%d, '%s', '%s', '%s', '%s', '%s')",
                     CRED_USER, md5($password), $user_name, $first_name, $last_name, $email);
            $this->query($query, False);
        }

        public function purchase_get()
        {

        }

        public function purchase_set()
        {

        }
    };

?>
