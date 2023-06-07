<?php

// PrintArray - used for fast debug output
function pa($mixed, $title = false)
{
    if ($title) {
        echo $title . ':';
    }
    echo '<pre>';
    if (gettype($mixed) == 'boolean') {
        print_r($mixed ? 'true' : 'false');
    }
    else {
        print_r(!is_null($mixed) ? $mixed : 'NULL');
    }
    echo '</pre>';
}
