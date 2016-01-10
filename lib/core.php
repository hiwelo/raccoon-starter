<?php

namespace Hwlo\Raccoon;


class Core
{
    /**
     * Empty constructor for WordPress add_action or add_filter
     * @return object
     */
    function __construct()
    {
        return $this;
    }

    /**
     * Load all asked librairies
     * @param  array $libraries asked libraries list
     * @return void
     * @static
     */
    static function load_libraries($libraries)
    {
        foreach ($libraries as $library) {
            self::load_library($library);
        }
    }

    /**
     * Load asked library
     * @param  string $library asked library to load
     * @return void
     */
    function load_library($library)
    {
        $file = get_template_directory() . '/' . $library;
        if (file_exists($file)) {
            locate_template($library, true, true);
        }
    }
}
