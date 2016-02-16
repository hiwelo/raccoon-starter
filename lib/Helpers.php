<?php
/**
 * Theme helpers methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Alsacreations.ModernWeb.Core.html
 */
namespace Hiwelo\Raccoon\Theme;

/**
 * Theme helpers methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Alsacreations.ModernWeb.Core.html
 */
class Helpers
{
    /**
     * Helper's construct method, call all private methods
     *
     * @return false
     *
     * @since 1.0.0
     * @uses  Helpers::addCharset()
     * @uses  Helpers::addViewport()
     */
    public function __construct()
    {
        // add generic meta into wp_head() (viewport + charset)
        $this->addCharset();
        $this->addViewport();
    }

    /**
     * Add charset HTML meta in wp_head()
     *
     * @return false
     *
     * @link  https://developer.wordpress.org/reference/functions/get_bloginfo
     * @since 1.0.0
     */
    private function addCharset()
    {
        add_action('wp_head', function () {
            $output = "<meta charset=\"" . get_bloginfo('charset') . "\">";
            echo $output;
        });
    }

    /**
     * Add viewport HTML meta in wp_head()
     *
     * @return false
     *
     * @link  https://developer.wordpress.org/reference/functions/get_bloginfo
     * @since 1.0.0
     */
    private function addViewport()
    {
        add_action('wp_head', function () {
            $output = "<meta name=\"viewport\" content\"width=device-width, initial-scale=1, shrink-to-fit=no\">";
            echo $output;
        });
    }
}
