<?php
require_once('/var/www/vendor/autoload.php');

/**
 * PrintArray - used for fast debug output
 */
if (!function_exists('pa')) {
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
}

/**
 * Load file content
 */
if(!function_exists('load_file_content')) {
    function load_file_content($filename)
    {
        $content = sprintf('File %s not found', $filename);

        if (file_exists($filename)) {
            $content =file_get_contents($filename);
        }

        return htmlspecialchars($content, ENT_SUBSTITUTE);
    }
}
