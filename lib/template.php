<?php

namespace Hwlo\Raccoon;

class Template
{
    /**
     * loads footer template file
     * @return void
     */
    public static function footer()
    {
        self::load('footer.php');
    }

    /**
     * loads an asked template file
     * @param  string $filename asked template file
     * @return void
     */
    public static function load($filename)
    {
        $file = locate_template($filename);

        if ($file) {
            global $navigations;
            include($file);
        } else {
            return false;
        }
    }

    /**
     * loads header template file
     * @return void
     */
    public static function header()
    {
        self::load('header.php');
    }
}
