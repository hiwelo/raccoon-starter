<?php
/**
 * Template management methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
namespace Hwlo\Raccoon;

/**
 * Template management methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Template
{
    /**
     * Loads footer template file
     *
     * @return void
     */
    public static function footer()
    {
        self::load('footer.php');
    }

    /**
     * Loads an asked template file
     *
     * @param string $filename asked template file
     *
     * @return void
     */
    public static function load($filename)
    {
        $file = locate_template($filename);

        if ($file) {
            global $navigations;
            include $file;
        } else {
            return false;
        }
    }

    /**
     * Loads header template file
     *
     * @return void
     */
    public static function header()
    {
        self::load('header.php');
    }
}
