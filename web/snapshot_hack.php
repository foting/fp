<?php

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
