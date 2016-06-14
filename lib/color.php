<?php

    const BLACK = "\33[0;30m";
    const GRAY = "\33[1;30m";
    const MAROON = "\33[0;31m";
    const RED = "\33[1;31m";
    const GREEN = "\33[0;32m";
    const LIME = "\33[1;32m";
    const YELLOW = "\33[1;33m";
    const BROWN = "\33[0;33m";
    const NAVY = "\33[0;34m";
    const BLUE = "\33[1;34m";
    const PURPLE = "\33[0;35m";
    const PINK = "\33[1;35m";
    const CYAN = "\33[0;36m";
    const AQUA = "\33[1;36m";
    const SILVER = "\33[0;37m";
    const WHITE = "\33[1;37m";

    function color ($text, $color)
    {
    	return $color.$text."\033[0;39m";
    }

/*    echo color ('BLACK', BLACK);
    echo color ('GRAY', GRAY);
    echo color ('MAROON', MAROON);
    echo color ('RED', RED);
    echo color ('GREEN', GREEN);
    echo color ('LIME', LIME);
    echo color ('YELLOW', YELLOW);
    echo color ('BROWN', BROWN);
    echo color ('NAVY', NAVY);
    echo color ('BLUE', BLUE);
    echo color ('PURPLE', PURPLE);
    echo color ('PINK', PINK);
    echo color ('CYAN', CYAN);
    echo color ('AQUA', AQUA);
    echo color ('SILVER', SILVER);
    echo color ('WHITE', WHITE);
    echo "\n";
*/
?>