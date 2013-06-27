<?php

    include_once "../include/credentials.php";

    class FPDB_Exception extends Exception {

    }

    class FPDB_Result implements Iterator
    {
        private $result;
        private $position;

        function  __construct($pdo_result)
        {
            $this->result = $pdo_result;
            $this->position = 0;
        }

        public function get()
        {
            return $this->result;
        }

        /* Iterator interface */
        public function current()
        {
            return $this->result[$this->position];
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
            return isset($this->result[$this->position]);
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

        private function execute($query, $param)
        {
            try {
                $sth = $this->dbh->prepare($query);
                $sth->execute($param);
            } catch (PDOException $e) {
                throw new FPDB_Exception($e->getMessage());
            }
            return $sth;
        }

        public function select($query, $param = array())
        {
            $sth = $this->execute($query, $param);
            $res = $sth->fetchAll(PDO::FETCH_ASSOC);
            return new FPDB_Result($res);
        }

        public function insert($query, $param = array())
        {
            $this->execute($query, $param);
            return new FPDB_Result(array());
        }
    };


    class FPDB_User extends FPDB_Base
    {
	    protected $inventory_q = "
            SELECT
                bb.beer_id,
                concat(sb.namn, ' ', sb.namn2) AS name,
                sb.prisinklmoms AS price,
                SUM(bb.count) AS count
            FROM
                (SELECT
                    beer_id,
                    SUM(amount) AS count
                FROM
                    beers_bought
                GROUP BY
                    beer_id
                UNION ALL
                SELECT
                    beer_id,
                    -COUNT(beer_id) AS count
                FROM
                    beers_sold
                GROUP BY
                    beer_id
                ) AS bb
            LEFT JOIN
                sbl_beer AS sb
            ON
                bb.beer_id = sb.nr
            GROUP BY
                bb.beer_id
            ORDER BY
                count DESC";
	    
    	protected $iou_q = "
            SELECT
                tr.user_id     AS user_id,
                tr.first_name  AS first_name,
                tr.last_name   AS last_name,
                SUM(tr.amount) AS assets
            FROM
                (SELECT
                    sp.user_id               AS user_id,
                    sp.first_name            AS first_name,
                    sp.last_name             AS last_name,
                    -SUM(fp_price(sp.price)) AS amount
                FROM
                    sales_price AS sp
                GROUP BY
                    sp.user_id
                UNION ALL
                SELECT
                    pa.user_id     AS user_id,
                    null           AS first_name,
                    null           AS last_name,
                    SUM(pa.amount) AS amount
                FROM
                    payments AS pa
                GROUP BY
                    pa.user_id
                 ) AS tr
            WHERE
                tr.user_id LIKE :user_id
            GROUP BY
                tr.user_id
            ORDER BY
                assets ASC";

	    protected $purchase_history_q = "
            SELECT
                concat(sb.namn, ' ', sb.namn2) AS name,
                bs.transaction_id,        
                bs.user_id,
                bs.beer_id,
                bs.timestamp,
                sp.price
            FROM
                beers_sold  AS bs,
                sales_price AS sp,
                sbl_beer    AS sb
            WHERE
                bs.transaction_id = sp.transaction_id and
                sp.beer_id = sb.nr and
                bs.user_id LIKE :user_id
            ORDER BY
                timestamp DESC";

        
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
            $q = "SELECT * FROM users WHERE username LIKE :username";
            $p = array(":username" => $username);
            return $this->select($q, $p);
        }

        public function user_get_all()
        {
            return $this->user_get("%");
        }


        public function purchases_get($user_id)
        {
            $q = $this->purchase_history_q;
            $p = array(":user_id" => $user_id);
            return $this->select($q, $p);
        }

        public function purchases_get_all()
        {            
            return $this->purchases_get("%");
        }


        /* Only *_append method exposed to users */
        public function purchases_append($user_id, $beer_id)
        {
            $q = "INSERT INTO beers_sold (user_id, beer_id) VALUES(:user_id, :beer_id)";
            $p = array(":user_id" => $user_id, ":beer_id" => $beer_id);
            $this->insert($q, $p);
        }


        public function payments_get($user_id)
        {
            $q = "SELECT * FROM payments WHERE user_id LIKE :user_id";
            $p = array("user_id" => $user_id);
            return $this->select($q, $p);
        }

        public function payments_get_all()
        {
            return $this->payments_get("%");
        }


        public function inventory_get_all()
        {
            $q = $this->inventory_q;
            $p = array();
            return $this->select($q, $p);
        }

        public function beer_data_get($beer_id)
        {
            $q = "SELECT * FROM sbl_beer WHERE nr LIKE :beer_id";
            $p = array(":beer_id" => $beer_id);
            return $this->select($q, $p);
        }

        public function beer_data_get_all()
        {
            return $this->beer_data_get("%");
        }
        
        public function iou_get($user_id)
        {
            $q = $this->iou_q;
            $p = array(":user_id" => $user_id);
            return $this->select($q, $p);
	    }

        public function iou_get_all()
        {
            return $this->iou_get("%");
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
            $q = "INSERT INTO users (
                    credentials,
                    username,
                    password,
                    first_name,
                    last_name,
                    email,
                    phone
                 ) VALUES (
                     :credentials,
                     :username,
                     :password,
                     :first_name,
                     :last_name,
                     :email,
                     :phone
                 )";
            $p = array(
                    ":credentials" => CRED_USER,
                    ":username" => $username,
                    ":password" => md5($password),
                    ":first_name" => $first_name,
                    ":last_name" => $last_name,
                    ":email" => $email,
                    ":phone" => $phone
                );
            $this->insert($q, $p);
        }

        public function payments_append($user_id, $admin_id, $amount)
        {
            $q = "INSERT INTO payments (user_id, admin_id, amount) VALUES (:uid, :aid, :amount)";
            $p = array(":uid" => $user_id, ":aid" => $admin_id, ":amount" => $amount);
            $this->insert($q, $p);
        }

        public function inventory_append($user_id, $beer_id, $amount, $price)
        {
            $q = "INSERT INTO beers_bought (
                    admin_id,
                    beer_id,
                    amount,
                    price
                 ) VALUES (
                    :admin_id,
                    :beer_id,
                    :amount,
                    :price
                 )"; 
            $p = array(
                    ":admin_id" => $admin_id,
                    ":beer_id" => $beer_id,
                    ":amount" => $amount,
                    ":price" => $price
                 );
            $this->insert($q);
        }

    	public function sbl_append($beer)
        {
            /* Systembolaget sometimes have a few duplicates in their XML file.
             * Therefore, we use REPLACE instead of INSERT to not insert
             * duplicates. */
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
