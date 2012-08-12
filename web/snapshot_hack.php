<?php

    /* This function is only used to lookup beer names when displaying
     * information returned from a database query. Once we have the snapshot
     * database table, the aforementioned should then join with the snapshot
     * table to get the names of the beers 
     */
    function beer_name($beer_id)
    {
        $names = array(
            1 => "Steam",
            2 => "Punk",
            3 => "Kung",
            4 => "Javer"
        );
        return array_key_exists($beer_id, $names) ? $names[$beer_id] : "Prips";
    }

?>
