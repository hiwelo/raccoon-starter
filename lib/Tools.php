<?php
/**
 * Tools & utilities methods
 *
 * PHP version 5
 *
 * @category Core
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
namespace Hwlo\Raccoon;

/**
 * Tools & utilities methods
 *
 * PHP version 5
 *
 * @category Tools
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
class Tools
{
    /**
     * Parse a string to return a real boolean for "true" or "false"
     *
     * @param string|boolean $value string or boolean to parse
     *
     * @return boolean
     *
     * @static
     */
    public static function parseBooleans(&$value)
    {
        switch ($value) {
            case "true":
                $value = true;
                break;

            case "false":
                $value = false;
                break;
        }

        return $value;
    }

    /**
     * Return a path to a specific asset type directory
     *
     * @param string $assets_type type of assets to return path
     *
     * @return string
     *
     * @link   https://codex.wordpress.org/Function_Reference/get_template_directory
     * @static
     */
    public static function assets_dir($assets_type)
    {
        return esc_url(get_template_directory_uri()). '/assets/dist/' . $assets_type;
    }
}
