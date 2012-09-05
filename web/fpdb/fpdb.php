<?php

    include_once "../include/credentials.php";

    class FPDB_Exception extends Exception {

    }

    class FPDB_Result implements Iterator
    {
        private $results_array;
        private $position;

        function  __construct($pdo_results)
        {
            $this->results_array = $pdo_results;
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
        private $dbh = null;
        private $position = 0;

        function __construct($dbn)
        {
            if ($dbn) {
                $this->connect($dbn);
            }
        }

        function __destruct()
        {
            $this->dbh = null;
        }

        protected function connect($dbn)
        {
            if ($this->dbh) {
                throw new FPDB_Exception("Error: FPDB_Base: Already connected.");
            }
            extract($dbn);

            try {
                $this->dbh = new PDO("mysql:dbname=$database;host=$server", $username, $password);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new FPDB_Exception($e->getMessage);
            }
        }

        private function execute($query)
        {
            try {
                $sth = $this->dbh->prepare($query);
                $sth->execute();
            } catch (PDOException $e) {
                throw new FPDB_Exception($e->getMessage());
            }
            return $sth;
        }

        public function select($query)
        {
            $sth = $this->execute($query);
            $res = $sth->fetchAll(PDO::FETCH_ASSOC);
            return new FPDB_Result($res);
        }

        public function insert($query)
        {
            $this->execute($query);
            return new FPDB_Results(array());
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
            SELECT
                transactions.user_id     AS user_id,
                transactions.first_name  AS first_name,
                transactions.last_name   AS last_name,
                SUM(transactions.amount) AS assets
            FROM
                (SELECT
                    sales_price.user_id     AS user_id,
                    sales_price.first_name  AS first_name,
                    sales_price.last_name   AS last_name,
                    -SUM(sales_price.price) AS amount
                FROM
                    sales_price
                GROUP BY
                    sales_price.user_id
                UNION ALL
                SELECT
                    payments.user_id     AS user_id,
                    null                 AS first_name,
                    null                 AS last_name,
                    SUM(payments.amount) AS amount
                FROM
                    payments
                GROUP BY
                    payments.user_id
                 ) AS transactions
            WHERE
                transactions.user_id LIKE %s
            GROUP BY
                transactions.user_id
            ORDER BY assets ASC";

	    protected $purchase_history_q = "
            SELECT
                concat(sbl_beer.namn, ' ', sbl_beer.namn2) AS name,
                beers_sold.transaction_id,
                beers_sold.user_id,
                beers_sold.beer_id,
                beers_sold.timestamp,
                sales_price.price
            FROM
                beers_sold,
                sales_price,
                sbl_beer
            WHERE
                beers_sold.transaction_id = sales_price.transaction_id and
                sales_price.beer_id = sbl_beer.nr and
                beers_sold.user_id LIKE %s
            ORDER BY
                beers_sold.timestamp DESC";

        
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
            return $this->select($q);
        }

        public function user_get_all()
        {
            return $this->select("SELECT * FROM users ORDER BY first_name");
        }


        public function purchases_get($user_id)
        {
            $q = sprintf($this->purchase_history_q, $user_id);
            return $this->select($q);
        }

        public function purchases_get_all()
        {            
            $q = sprintf($this->purchase_history_q, "'%'");
            return $this->select($q);
        }

        /* Only *_append method exposed to users */
        public function purchases_append($user_id, $beer_id)
        {
            $q = sprintf("INSERT INTO beers_sold
                          (user_id, beer_id)
                          VALUES ('%d', '%d')",
                          $user_id, $beer_id);
            $this->insert($q);
        }


        public function payments_get($user_id)
        {
            $q = sprintf("SELECT * FROM payments WHERE user_id = '%s'", $user_id);
            return $this->select($q);
        }

        public function payments_get_all()
        {
            return $this->select("SELECT * FROM payments");
        }


        public function inventory_get_all()
        {
            return $this->select($this->inventory_q);
        }

        public function beer_data_get($beer_id)
        {
            return $this->select(sprintf("SELECT * FROM sbl_beer WHERE nr = %s", $beer_id));
        }

        public function beer_data_get_all()
        {
            return $this->select("SELECT nr, namn, prisinklmoms FROM sbl_beer");
        }
        
        public function iou_get($user_id)
        {
            $q = sprintf($this->iou_q, $user_id);
            return $this->select($q);
	    }

        public function iou_get_all()
        {
            $q = sprintf($this->iou_q, "'%'");
            return $this->select($q);
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
            $this->insert($q);
        }

        public function payments_append($user_id, $admin_id, $amount)
        {
            $q = sprintf("INSERT INTO payments
                         (user_id, admin_id, amount)
                         VALUES ('%d', '%d', '%d')",
                         $user_id, $admin_id, $amount);
            $this->insert($q);
        }

        public function inventory_append($user_id, $beer_id, $amount, $price)
        {
            $q = sprintf("INSERT INTO beers_bought
                          (admin_id, beer_id, amount, price)
                          VALUES ('%d', '%d', '%d', '%.2f')",
                          $user_id, $beer_id, $amount, $price);
            $this->insert($q);
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

            $this->insert($q);
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
