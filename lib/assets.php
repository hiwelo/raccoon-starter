<?php

namespace Hwlo\Raccoon;

class Assets
{
    /**
     * Empty constructor for WordPress add_action or add_filter
     * @return object
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * Get path for an asset file
     * @param  string $filename asked filename
     * @return string
     * @static
     */
    public static function asset_path($filename)
    {
        $assets_path = '/assets/dist/';
        $dist_path = get_template_directory_uri() . $assets_path;
        $directory = dirname($filename) . '/';
        $file = basename($filename);

        return $dist_path . $directory . $file;
    }
}
