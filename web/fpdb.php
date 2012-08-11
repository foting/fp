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

        public function purchase_append($user_id, $beer_id)
        {
            $query = sprintf("INSERT INTO beers_sold (user_id, beer_id)
                     VALUES ('%d', '%d')", $user_id, $beer_id);
            $this->query($query);
        }


        public function inventory_get()
        {
            $q1 = "
                CREATE TEMPORARY TABLE beers_bought_tmp AS (
                    SELECT   beer_id, SUM(amount) AS count
                    FROM     beers_bougth
                    GROUP BY beer_id
                )";
            $q2 = "
                CREATE TEMPORARY TABLE beers_sold_tmp AS (
                    SELECT   beer_id, COUNT(beer_id) AS count
                    FROM     beers_sold
                    GROUP BY beer_id
                )";
            $q3 = "
                CREATE TEMPORARY TABLE inventory_tmp AS (
                    SELECT    beers_bought_tmp.beer_id,
                              COALESCE(beers_bought_tmp.count, 0) - COALESCE(beers_sold_tmp.count, 0) AS count
                    FROM      beers_bought_tmp
                    LEFT JOIN beers_sold_tmp ON beers_bought_tmp.beer_id = beers_sold_tmp.beer_id
                )";

            $this->query($q1);     
            $this->query($q2);           
            $this->query($q3);

            $this->query("SELECT * FROM inventory_tmp;");
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
            $foo = array(
                1 => "Steam",
                2 => "Punk",
                3 => "Kung",
                4 => "Javer"
            );
            return array_key_exists($beer_id, $foo) ? $foo[$beer_id] : "Prips";
        }

        public function snapshot_append()
        {

        }
    };

?>
