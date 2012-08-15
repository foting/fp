<?php

    include_once "../include/credentials.php";

    /* Not all server at uni supports mysqli hence these wrappers */
    function sql_connect($server, $username, $password, $database)
    {
        if (function_exists(mysqli_connect)) {
            return  mysqli_connect($server, $username, $password, $database);
        } else {
            $link = mysql_connect($server, $username, $password);
            if (!$link || !mysql_select_db($database)) {
                return False;
            }
            return $link;
        }
    }

    function sql_close($link)
    {
        if (function_exists(mysqli_error)) {
            return mysqli_close($link);
        } else {
            return mysql_close();
        }
    }

    function sql_error($link)
    {
        if (function_exists(mysqli_error)) {
            return mysqli_error($link);
        } else {
            return mysql_error();
        }
    }

    function sql_query($link, $query)
    {
        if (function_exists(mysqli_query)) {
            return mysqli_query($link, $query);
        } else {
            return mysql_query($query);
        }
    }

    function sql_fetch_assoc($results)
    {
        if (function_exists(mysqli_fetch_assoc)) {
            return mysqli_fetch_assoc($results);
        } else {
            return mysql_fetch_assoc($results);
        }
    }


    class FPDB_Exception extends Exception {

    }

    class FPDB_Results implements Iterator
    {
        private $sql_results;
        private $iterator;
        private $position;

        function  __construct($sql_results)
        {
            $this->sql_results = $sql_results;
            $this->iterator = True; // In case valid is called before rewind
            $this->position = 0;
        }

        /* Iterator interface */
        public function current()
        {
            return $this->iterator;
        }

        public function next()
        {
            $this->iterator = sql_fetch_assoc($this->sql_results);
            $this->position++;
            return $this->iterator; // Not requred by Iterator
        }

        public function rewind()
        {
            $this->iterator = sql_fetch_assoc($this->sql_results);
            $this->position = 0;
        }

        public function key()
        {
            return $this->position;
        }

        public function valid()
        {
            return $this->iterator ? True : False;
        }
    };

    class FPDB_Base
    {
        private $link;
        private $query_ = False;
        private $result_ = False;
        private $position = 0;

        function __construct($dbn)
        {
            $this->link = sql_connect(
                $dbn["server"], $dbn["username"], $dbn["password"], $dbn["database"]);

            if (!$this->link) {
                throw new FPDB_Exception(sql_error($this->link));
            }
        }

        function __destruct()
        {
            sql_close($this->link);
        }


        protected function query($query)
        {
            $results = sql_query($this->link, $query);
            if (!$results) {
                throw new FPDB_Exception(sql_error($this->link) . ": " . $query);
            }
            return new FPDB_Results($results);
        }
    };


    class FPDB_User extends FPDB_Base
    {
	    protected $beers_bought_q = "
            CREATE TEMPORARY TABLE beers_bought_tmp AS (
                SELECT   beer_id, SUM(amount) AS count
                FROM     beers_bought
                GROUP BY beer_id
	        )";

	    protected $beers_sold_q = "
            CREATE TEMPORARY TABLE beers_sold_tmp AS (
                SELECT   beer_id, COUNT(beer_id) AS count
                FROM     beers_sold
                GROUP BY beer_id
	        )";

	    protected $inventory_q = "
	        CREATE TEMPORARY TABLE inventory_tmp AS (
		        SELECT    beers_bought_tmp.beer_id,
			              COALESCE(beers_bought_tmp.count, 0) - COALESCE(beers_sold_tmp.count, 0) AS count
		        FROM      beers_bought_tmp
		        LEFT JOIN beers_sold_tmp ON beers_bought_tmp.beer_id = beers_sold_tmp.beer_id
	    )";


        protected $time_charged_q = "
            CREATE TEMPORARY TABLE time_charged_tmp AS (
                SELECT  bs.user_id,
                        bs.beer_id,
                        bs.timestamp AS time_sold,
                        (SELECT MAX(bb.timestamp)
                         FROM   beers_bought bb
                         WHERE  bb.beer_id = bs.beer_id and
                                bb.timestamp <= bs.timestamp
                        ) as time_bought
                FROM beers_sold bs
                ORDER BY bs.user_id
            )";

    	protected $beers_sold_at_price_q = "
	        CREATE TEMPORARY TABLE beers_sold_at_price_tmp AS (
		        SELECT  tc.user_id,
			            tc.beer_id,
			            u.username,
			            u.first_name,
			            u.last_name,
			            bb.price,
			            tc.time_sold,
			            tc.time_bought
		        FROM    time_charged_tmp tc,
			            beers_bought bb,
                        users u
		        WHERE   tc.beer_id = bb.beer_id and
			    tc.time_bought = bb.timestamp and
		        u.user_id = tc.user_id
         )";

	    protected $beers_bought_total_q = "
	        CREATE TEMPORARY TABLE beers_bought_total_tmp AS (
		        SELECT  user_id,
			            username,
			            first_name,
			            last_name,
			            SUM(price) AS amount
		        FROM beers_sold_at_price_tmp
		        GROUP BY user_id
		        ORDER BY amount DESC
	        )";

	    protected $payments_total_q = "
	        CREATE TEMPORARY TABLE payments_total_tmp AS (
		        SELECT  user_id,
			            SUM(amount) as total
		        FROM payments
		        GROUP BY user_id
	        )";

    	protected $iou_q = "
			SELECT users.user_id, first_name, last_name, assets FROM users RIGHT JOIN (
                SELECT user_id, SUM(assets) AS assets FROM (
                    SELECT user_id, SUM(amount) AS assets
                            FROM payments GROUP BY user_id
                    UNION
                    SELECT user_id, -SUM(price) FROM (
                            SELECT * FROM (
                                SELECT
                                    beers_sold.user_id,
                                    beers_sold.transaction_id,
                                    beers_bought.price #,
                                FROM beers_sold LEFT JOIN beers_bought
                                    ON beers_bought.beer_id = beers_sold.beer_id
                                    WHERE beers_sold.timestamp > beers_bought.timestamp
                                    ORDER BY beers_bought.timestamp DESC
                                ) AS T
                            GROUP BY T.transaction_id
                    ) AS S GROUP BY user_id
                ) AS TOTAL_ASSETS GROUP BY user_id
            ) AS DEBT_LIST
            ON users.user_id = DEBT_LIST.user_id
            WHERE DEBT_LIST.user_id='%s'";
    	    
	    protected $iou_all_q = "
	        SELECT username, first_name, last_name, assets FROM users RIGHT JOIN (
                SELECT user_id, SUM(assets) AS assets FROM (
                    SELECT user_id, SUM(amount) AS assets
                            FROM payments GROUP BY user_id
                    UNION
                    SELECT user_id, -SUM(price) FROM (
                            SELECT * FROM (
                                SELECT
                                    beers_sold.user_id,
                                    beers_sold.transaction_id,
                                    beers_bought.price #,
                                FROM beers_sold LEFT JOIN beers_bought
                                    ON beers_bought.beer_id = beers_sold.beer_id
                                    WHERE beers_sold.timestamp > beers_bought.timestamp
                                    ORDER BY beers_bought.timestamp DESC
                                ) AS T
                            GROUP BY T.transaction_id
                    ) AS S GROUP BY user_id
                ) AS TOTAL_ASSETS GROUP BY user_id
            ) AS DEBT_LIST
            ON users.user_id = DEBT_LIST.user_id
            ORDER BY assets ASC";


        function __construct()
        {
            include "../include/user_db_credentials.php";
            if (!isset($dbn)) {
                throw new FPDB_Exception("Data base credentials not found.");
            }
            parent::__construct($dbn);
        }


        public function user_get($username)
        {
            /* Assuming that username is unique */
            $q = sprintf("SELECT * FROM users WHERE username = '%s'", $username);
            return $this->query($q);
        }

        public function user_get_all()
        {
            return $this->query("SELECT * FROM users");
        }


        public function purchase_get($user_id)
        {
            $this->query($this->time_charged_q);
            $this->query($this->beers_sold_at_price_q);
            $q = sprintf("SELECT * FROM beers_sold_at_price_tmp WHERE user_id = '%s'", $user_id);
            return $this->query($q);
        }

        public function purchase_get_all()
        {
            $this->query($this->time_charged_q);
            $this->query($this->beers_sold_at_price_q);
            return $this->query("SELECT * FROM beers_sold_at_price_tmp");
        }

        /* Only *_append method exposed to users */
        public function purchase_append($user_id, $beer_id)
        {
            $q = sprintf("INSERT INTO beers_sold
                          (user_id, beer_id)
                          VALUES ('%d', '%d')",
                          $user_id, $beer_id);
            $this->query($q);
        }


        public function payment_get($user_id)
        {
            $q = sprintf("SELECT * FROM payments WHERE user_id = '%s'", $user_id);
            return $this->query($q);
        }

        public function payment_get_all()
        {
            return $this->query("SELECT * FROM payments");
        }


        public function inventory_get_all()
        {
            $this->query($this->beers_bought_q);     
            $this->query($this->beers_sold_q);           
            $this->query($this->inventory_q);
            return $this->query("SELECT * FROM inventory_tmp;");
        }


        public function iou_get($user_id)
        {
            $q = sprintf($this->iou_q, $user_id);
            return $this->query($q);
	    }

        public function iou_get_all()
        {
            return $this->query($this->iou_all_q);
	    }
    };

    class FPDB_Admin extends FPDB_User
    {
        function __construct()
        {
            include "../include/admin_db_credentials.php";
            if (!isset($dbn)) {
                throw new FPDB_Exception("Data base credentials not found.");
            }
            parent::__construct($dbn);
        }

        public function user_append($username, $password, $first_name, $last_name, $email, $phone)
        {
            $q = sprintf("INSERT INTO users
                 (credentials, password, username, first_name, last_name, email, phone)
                 VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s')",
                 CRED_USER, md5($password), $username, $first_name, $last_name, $email, $phone);
            $this->query($q);
        }

        public function payment_append($user_id, $admin_id, $amount)
        {
            $q = sprintf("INSERT INTO payments
                         (user_id, admin_id, amount)
                         VALUES ('%d', '%d', '%d')",
                         $user_id, $admin_id, $amount);
            $this->query($q);
        }

        public function inventory_append($user_id, $beer_id, $amount, $price)
        {
            $q = sprintf("INSERT INTO beers_bought
                          (admin_id, beer_id, amount, price)
                          VALUES ('%d', '%d', '%d', '%.2f')",
                          $user_id, $beer_id, $amount, $price);
            $this->query($q);
        }

    	public function sbl_append($beer)
        {
            $q = "INSERT INTO sbl_beer (
                    nr,
                    Artikelid,
                    Varnummer,
                    Namn,
                    Namn2,
                    Prisinklmoms,
                    Saljstart,
                    Slutlev,
                    Varugrupp,
                    Forpackning,
                    Forslutning,
                    Ursprung,
                    Ursprunglandnamn,
                    Producent,
                    Leverantor,
                    Argang,
                    Provadargang,
                    Alkoholhalt,
                    Modul,
                    Sortiment,
                    Ekologisk,
                    Koscher
                ) VALUES (
                    \"$beer->nr\",
                    \"$beer->Artikelid\",
                    \"$beer->Varnummer\",
                    \"$beer->Namn\",
                    \"$beer->Namn2\",
                    \"$beer->Prisinklmoms\",
                    \"$beer->Saljstart\",
                    \"$beer->Slutlev\",
                    \"$beer->Varugrupp\",
                    \"$beer->Forpackning\",
                    \"$beer->Forslutning\",
                    \"$beer->Ursprung\",
                    \"$beer->Ursprunglandnamn\",
                    \"$beer->Producent\",
                    \"$beer->Leverantor\",
                    \"$beer->Argang\",
                    \"$beer->Provadargang\",
                    \"$beer->Alkoholhalt\",
                    \"$beer->Modul\",
                    \"$beer->Sortiment\",
                    \"$beer->Ekologisk\",
                    \"$beer->Koscher\"
                )";

            $this->query($q);
        }

        public function sbl_nuke()
        {
            $this->query("TRUNCATE TABLE sbl_beer");
        }
    };

    /* Temporarily putting this functionality here */
    function sbl_insert_snapshot($fpdb, $filename)
    {
        $fpdb->sbl_nuke();

        $sbl_beers = simplexml_load_file($filename);
        if (!$sbl_beers) {
            /* When/If this function is moved throw something more appropriate */
            throw new FPDB_Exception("simplexml_load_file failed");
        }

        foreach ($sbl_beers->artikel as $beer) {
            $fpdb->sbl_append($beer);
        }
    }
?>
