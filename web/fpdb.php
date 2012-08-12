<?php

    include_once "credentials.php";

    class FPDBException extends Exception {

    }

    class FPDB implements Iterator
    {
        private $link;
        private $query_ = False;
        private $result_ = False;
        private $position = 0;

        function __construct($credentails = CRED_USER)
        {
            $ok = True;

            switch ($credentails) {
                case CRED_USER:
                    $dbn = array(
                        "server" => "steam.it.uu.se",
                        "username" => "admin",
                        "password" => "fp_at_polacks",
                        "database" => "fp_test",
                    );
                    break;
                case CRED_ADMIN:
                    $dbn = array(
                        "server" => "steam.it.uu.se",
                        "username" => "admin",
                        "password" => "fp_at_polacks",
                        "database" => "fp_test",
                    );
                    break;
            }

            $ok = $ok && ($this->link = mysqli_connect(
                $dbn["server"], $dbn["username"], $dbn["password"]));

            $ok = $ok && mysqli_select_db($dbn["database"]);

            if (!$ok) {
                throw new FPDBException(mysqli_error());
            }
        }

        function __destruct()
        {
            mysqli_close($this->link);
        }


        public function query($query)
        {
            $this->query_ = mysqli_real_escape_string(mysqli_query($query));
            if (!$this->query_) {
                throw new FPDBException(mysqli_error());
            }
        }

        public function result()
        {
            $this->result_ = mysqli_fetch_assoc($this->query_);
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


        public function user_get($username = "")
        {
            if ($username) {    
                /* Assuming that username is unique */
                $query = sprintf("SELECT * FROM users WHERE username = '%s'", $username);
            } else {
                $query = sprintf("SELECT * FROM users");
            }
            $this->query($query);
            return $this->result();
        }

        public function user_append($username, $password, $first_name, $last_name, $email, $phone)
        {
            include_once "credentials.php";
            $query = sprintf("INSERT INTO users
                     (credentials, password, username, first_name, last_name, email, phone)
                     VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s')",
                     CRED_USER, md5($password), $username, $first_name, $last_name, $email, $phone);
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

        public function payment_get($user_id)
        {
            $query = sprintf("SELECT * FROM payments WHERE user_id = '%s'", $user_id);
            $this->query($query);
            return $this->result();
        }

        public function payment_append($user_id, $admin_id, $amount)
        {
            $query = sprintf("INSERT INTO payments (user_id, admin_id, amount)
                     VALUES ('%d', '%d', '%d')", $user_id, $admin_id, $amount);
            $this->query($query);
        }


        public function inventory_get()
        {
$beers_bought_q = <<<EOT
    CREATE TEMPORARY TABLE beers_bought_tmp AS (
        SELECT   beer_id, SUM(amount) AS count
        FROM     beers_bougth
        GROUP BY beer_id
    )
EOT;

$beers_sold_q = <<<EOT
    CREATE TEMPORARY TABLE beers_sold_tmp AS (
        SELECT   beer_id, COUNT(beer_id) AS count
        FROM     beers_sold
        GROUP BY beer_id
    )
EOT;

$inventory_q = <<<EOT
    CREATE TEMPORARY TABLE inventory_tmp AS (
        SELECT    beers_bought_tmp.beer_id,
                  COALESCE(beers_bought_tmp.count, 0) - COALESCE(beers_sold_tmp.count, 0) AS count
        FROM      beers_bought_tmp
        LEFT JOIN beers_sold_tmp ON beers_bought_tmp.beer_id = beers_sold_tmp.beer_id
    )
EOT;

            $this->query($beers_bought_q);     
            $this->query($beers_sold_q);           
            $this->query($inventory_q);

            $this->query("SELECT * FROM inventory_tmp;");
            return $this->result();
        }

        public function inventory_append($user_id, $beer_id, $amount, $price)
        {
            $query = sprintf("INSERT INTO beers_bougth (admin_id, beer_id, amount, price)
                    VALUES ('%d', '%d', '%d', '%.2f')", $user_id, $beer_id, $amount, $price);
            $this->query($query);
        }

        public function iou_get($user_id = 0)
        {
$time_charged_q = <<<EOT
    CREATE TEMPORARY TABLE time_charged_tmp AS (
        SELECT  bs.user_id,
                bs.beer_id,
                bs.timestamp AS time_sold,
                (SELECT MAX(bb.timestamp)
                    FROM   beers_bougth bb
                    WHERE  bb.beer_id = bs.beer_id and
                    bb.timestamp <= bs.timestamp
                ) as time_bougth
        FROM beers_sold bs
        ORDER BY bs.user_id
    )
EOT;

$beers_sold_at_price_q = <<<EOT
    CREATE TEMPORARY TABLE beers_sold_at_price_tmp AS (
        SELECT  tc.user_id,
                tc.beer_id,
                u.username,
                u.first_name,
                u.last_name,
                bb.price
        FROM    time_charged_tmp tc,
                beers_bougth bb, users u
        WHERE   tc.beer_id = bb.beer_id and
                tc.time_bougth = bb.timestamp and
        u.user_id = tc.user_id
    )
EOT;

$beers_bougth_total_q = <<<EOT
    CREATE TEMPORARY TABLE beers_bougth_total_tmp AS (
        SELECT  user_id,
                username,
                first_name,
                last_name,
                SUM(price) AS amount
        FROM beers_sold_at_price_tmp
        GROUP BY user_id
        ORDER BY amount DESC
    )
EOT;

$payments_total_q = <<<EOT
    CREATE TEMPORARY TABLE payments_total_tmp AS (
        SELECT  user_id,
                SUM(amount) as total
        FROM payments
        GROUP BY user_id
    )
EOT;

$iou_tmp_q = <<<EOT
    CREATE TEMPORARY TABLE iou_tmp AS (
        SELECT  bb.user_id,
                bb.username,
                bb.first_name,
                bb.last_name,
                COALESCE(bb.amount, 0) - COALESCE(pa.total, 0) AS amount
        FROM      beers_bougth_total_tmp bb
        LEFT JOIN payments_total_tmp pa
        ON        bb.user_id = pa.user_id
    )
EOT;

            
            $this->query($time_charged_q);
            $this->query($beers_sold_at_price_q);
            $this->query($beers_bougth_total_q);
            $this->query($payments_total_q);
            $this->query($iou_tmp_q);

            if ($user_id) {
                $this->query("SELECT * FROM iou_tmp WHERE user_id = " . $user_id);
            } else {
                $this->query("SELECT * FROM iou_tmp;");
            }
            return $this->result();
        }
    };

?>
