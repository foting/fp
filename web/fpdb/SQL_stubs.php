<?php
    class SQL_link
    {
        function __construct($link)
        {
        }
    };
    
    class SQL_result
    {
        function __construct($result)
        {
        }
        
    };

    /* Returns a new instance of SQL_link */
    function sql_connect($server, $username, $password, $database)
    {
    }

    /* sql_link: SQL_link instance returned by sql_connect
     * Returns TRUE on success or FALSE on failure. 
     */
    function sql_close($sql_link)
    {
    }

    
    /* sql_link: SQL_link instance returned by sql_connect
     * Returns string describing the error
     */
    function sql_error($sql_link)
    {
    }

    /* sql_link: SQL_link instance returned by sql_connect
     * query: SQL command as string
     * Returns a new instance of SQL_result
     */
    function sql_query($sql_link, $query)
    {
    }

    /* sql_results: SQL_result result returned by sql_query
     * Returns an associative array representing one record
     */
    function sql_fetch_assoc($sql_results)
    {
    }
    
    if (True) {
        throw new Exception("Database backend not found.");
    }
?>
