<?php

    class FPDBException extends Exception {

    }

    class FPDB implements Iterator
    {
        private $link;
        private $query_ = False;
        private $result_ = False;
        private $position = 0;

        function __construct()
        {
            $ok = True;

            $ok = $ok && ($this->link = mysql_connect("steam.it.uu.se", "admin", "fp_at_polacks"));
            $ok = $ok && mysql_select_db("fp_test");

            if (!ok) {
                throw new FPDBException(mysql_error());
            }
        }

        function __destruct()
        {
            mysql_close($this->link);
        }


        public function query($query)
        {
            $this->query_ = mysql_query($query);
            if (!$this->query_) {
                throw new FPDBException(mysql_error());
            }
        }

        public function result()
        {
            $this->result_ = mysql_fetch_assoc($this->query_);
            return $this->result_;
        }

        /* Iterator interface */
        public function current()
        {
            return $this->result_;
        }

        public function next()
        {
            $this->position += 1;
            return $this->result();
        }

        public function rewind()
        {
            /* We only allow the iterator to be used once per query. */
            $this->postition = 0;
        }

        public function key()
        {
            return $this->position;
        }

        public function valid()
        {
            return $this->result_ ? True : False;
        }


        public function user_get($user_name)
        {
            /* Assuming that user_name is unique */
            $query = sprintf("SELECT * FROM users WHERE user_name = '%s'", $user_name);
            $this->query($query);
            return $this->result();
        }

        public function user_append($user_name, $password, $first_name, $last_name, $email, $phone)
        {
            include_once "credentials.php";
            $query = sprintf("INSERT INTO users
                     (credentials, password, user_name, first_name, last_name, email, phone)
                     VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s')",
                     CRED_USER, md5($password), $user_name, $first_name, $last_name, $email, $phone);
            $this->query($query);
        }


        public function purchase_get($user_id)
        {
            $query = sprintf("SELECT * FROM beers_sold WHERE user_id = '%s'", $user_id);
            $this->query($query);
            return $this->result();
        }

        public function purchase_append($user_id, $beer_id, $timestamp)
        {
            $query = sprintf("INSERT INTO beers_sold (user_id, beer_id, timestamp)
                     VALUES ('%d', '%d', '%d')", $user_id, $beer_id, $timestamp);
            $this->query($query);
        }


        public function inventory_get()
        {
            /* Return user list instead of inventory for testing purposes */
            $query = sprintf("SELECT * FROM users");
            $this->query($query);
            return $this->result();
        }

        public function inventory_append($user_id, $beer_id, $amount, $price)
        {
            $query = sprintf("INSERT INTO beers_bougth (admin_id, beer_id, amount, price)
                    VALUES ('%d', '%d', '%d', '%.2f')", $user_id, $beer_id, $amount, $price);
            $this->query($query);
        }



        public function snapshot_get($beer_id)
        {
            $query = sprintf("SELECT * FROM snapshot WHERE beer_id = '%s'", $beer_id);
            $this->query($query);
            return $this->result();
        }

        public function snapshot_append()
        {

        }
    };

?>
