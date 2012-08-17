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

        public function get_array()
        {
            $a = array();
            while ($iter = sql_fetch_assoc($this->sql_results)) {
                array_push($a, $iter);
            }
            return $a;
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

    }

    class FPDB_Base
    {
        private $link = False;
        private $query_ = False;
        private $result_ = False;
        private $position = 0;

        function __construct($dbn)
        {
            if ($dbn) {
                $this->connect($dbn);
            }
        }

        function __destruct()
        {
            sql_close($this->link);
        }

        public function connect($dbn)
        {
            if ($this->link) {
                throw new FPDB_Exception("Error: FPDB_Base: Already connected.");
            }

            $this->link = sql_connect(
                $dbn["server"], $dbn["username"], $dbn["password"], $dbn["database"]);

            if (!$this->link) {
                throw new FPDB_Exception(sql_error($this->link));
            }
        }

        public function query($query)
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
	    protected $inventory_q = "
	    SELECT NAMED.*, price FROM (
	    	SELECT namn, BEERS.beer_id, BEERS.count FROM
           		sbl_beer
            RIGHT JOIN (
    			SELECT beer_id, SUM(count) AS count FROM
                    (SELECT beer_id, SUM(amount) AS count
                    	FROM beers_bought
                    	GROUP BY beer_id
            		UNION
                    SELECT beer_id, -COUNT(beer_id) AS count
                   		FROM beers_sold
                   		GROUP BY beer_id) A
                GROUP BY A.beer_id ) AS BEERS
            ON BEERS.beer_id = sbl_beer.nr) AS NAMED
		LEFT JOIN
			beers_bought
		ON beers_bought.beer_id = NAMED.beer_id";

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

	    protected $purchase_history_q = "
    		SELECT namn, Purchases.* FROM
            	sbl_beer
            RIGHT JOIN (
            	SELECT * FROM (
            		SELECT
            			beers_sold.*,
            			beers_bought.price
            		FROM beers_sold LEFT JOIN beers_bought
            		ON beers_bought.beer_id = beers_sold.beer_id
            		WHERE beers_sold.timestamp > beers_bought.timestamp
            		ORDER BY beers_bought.timestamp DESC
                ) AS T WHERE T.user_id = %s
        		GROUP BY T.transaction_id ORDER BY T.timestamp DESC) AS Purchases
    		ON Purchases.beer_id = sbl_beer.nr";
	            
        protected $purchase_history_all_q = "
        	SELECT namn, Purchases.* FROM
            	sbl_beer
            RIGHT JOIN (
        	SELECT * FROM (
        		SELECT
        			beers_sold.*,
        			beers_bought.price
        		FROM beers_sold LEFT JOIN beers_bought
        		ON beers_bought.beer_id = beers_sold.beer_id
        		WHERE beers_sold.timestamp > beers_bought.timestamp
        		ORDER BY beers_bought.timestamp DESC
            ) AS T
    		GROUP BY T.transaction_id ORDER BY T.timestamp) AS Purchases
    		ON Purchases.beer_id = sbl_beer.nr";
        
        function __construct()
        {
            include "../include/user_db_credentials.php";
            if (!isset($dbn)) {
                throw new FPDB_Exception("Failed to import \$dbn from user_db_credentials.php.");
            }
            $this->connect($dbn);
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
            $q = sprintf($this->purchase_history_q, $user_id);
            return $this->query($q);
        }

        public function purchase_get_all()
        {            
            return $this->query($this->purchase_history_all_q);
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
            return $this->query($this->inventory_q);
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
                throw new FPDB_Exception("Failed to import \$dbn from admin_db_credentials.php");
            }
            $this->connect($dbn);
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
	
	public function pub_price($sbl_price) {
	    return (floor(($sbl_price + 1.0) / 5) + 1) * 5;
	}
    };

    /* Temporarily putting this functionality here */
    function sbl_insert_snapshot($fpdb, $filename)
    {
        $fpdb->sbl_nuke();

        $sbl_beers = simplexml_load_file($filename);
        if (!$sbl_beers) {
            /* When/If this function is moved throw something more appropriate */
            throw new FPDB_Exception("Error: sbl_insert_snapshot: simplexml_load_file failed");
        }

        foreach ($sbl_beers->artikel as $beer) {
            $fpdb->sbl_append($beer);
        }
    }
?>
