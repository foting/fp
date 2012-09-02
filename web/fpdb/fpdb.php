<?php

    include_once "../include/credentials.php";
    try {
        include_once "SQL_mysqli.php";    
    } catch (Exception $e) {
        die($e);
    }
    

    class FPDB_Exception extends Exception {

    }

    class FPDB_Results implements Iterator
    {
        private $results_array;
        private $position;

        function  __construct($sql_results)
        {
            $this->results_array = array();
            while ($iter = sql_fetch_assoc($sql_results)) {
                array_push($this->results_array, $iter);
            }
            $this->position = 0;
        }

        public function get_array()
        {
            return $this->results_array;
        }

        /* Iterator interface */
        public function current()
        {
            return $this->results_array[$this->position];
        }

        public function next()
        {
            ++$this->position;
        }

        public function rewind()
        {
            $this->position = 0;
        }

        public function key()
        {
            return $this->position;
        }

        public function valid()
        {
            return isset($this->results_array[$this->position]);
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
		ON beers_bought.beer_id = NAMED.beer_id
		GROUP BY beer_id ORDER BY count DESC";
	    
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
            return $this->query("SELECT * FROM users ORDER BY first_name");
        }


        public function purchases_get($user_id)
        {
            $q = sprintf($this->purchase_history_q, $user_id);
            return $this->query($q);
        }

        public function purchases_get_all()
        {            
            return $this->query($this->purchase_history_all_q);
        }

        /* Only *_append method exposed to users */
        public function purchases_append($user_id, $beer_id)
        {
            $q = sprintf("INSERT INTO beers_sold
                          (user_id, beer_id)
                          VALUES ('%d', '%d')",
                          $user_id, $beer_id);
            $this->query($q);
        }


        public function payments_get($user_id)
        {
            $q = sprintf("SELECT * FROM payments WHERE user_id = '%s'", $user_id);
            return $this->query($q);
        }

        public function payments_get_all()
        {
            return $this->query("SELECT * FROM payments");
        }


        public function inventory_get_all()
        {
            return $this->query($this->inventory_q);
        }

        public function beer_data_get($beer_id)
        {
            return $this->query(sprintf("SELECT * FROM sbl_beer WHERE nr = %s", $beer_id));
        }

        public function beer_data_get_all()
        {
            return $this->query("SELECT nr, namn, prisinklmoms FROM sbl_beer");
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

        public function payments_append($user_id, $admin_id, $amount)
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
            /* Systembolaget sometimes have a few duplicates in their XML file.
             * Therefore, we use REPLACE instead of INSERT to not insert
             *duplicates. */
            $q = "REPLACE INTO sbl_beer (
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
