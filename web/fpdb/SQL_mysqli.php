<?php
    class SQL_link
    {
        public $link;
        function __construct($link)
        {
            $this->link = $link;
        }
    };
    
    class SQL_result
    {
        public $result;
        function __construct($result)
        {
            $this->result = $result;
        }
        
    };

    function sql_connect($server, $username, $password, $database)
    {
        return new SQL_link(mysqli_connect($server, $username, $password, $database));
    }

    function sql_close($sql_link)
    {
        return mysqli_close($sql_link->link);
    }

    function sql_error($sql_link)
    {
        return mysqli_error($sql_link->link);
    }

    function sql_query($sql_link, $query)
    {
        $r = mysqli_query($sql_link->link, $query);
        if ($r) {
            return new SQL_result($r);
        } else {
            return False;
        }
    }

    function sql_fetch_assoc($sql_result)
    {
        return mysqli_fetch_assoc($sql_result->result);
    }
    
    if (!function_exists("mysqli_connect")) {
        throw new Exception("mysqli not found.");
    }
?>
