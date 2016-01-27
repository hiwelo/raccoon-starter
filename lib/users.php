<?php
/**
 * User related methods
 *
 * PHP version 5
 *
 * @category Users
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
namespace Hwlo\Raccoon;

/**
 * User related methods
 *
 * PHP version 5
 *
 * @category Users
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Users
{
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address
     *
     * @param string  $email the email address
     * @param integer $s     size in pixels, defaults to 80px [1 - 2 048px]
     * @param string  $d     default imageset to use
     *                       [404 | mm | identicon | monsterid | wavatar]
     * @param string  $r     maximum rating (inclusive)
     *                       [g | pg | r | x ]
     * @param boolean $img   true to return a complete IMG tag,
     *                       false for just the URL
     * @param array   $atts  optional, additional key/value attributes
     *                       to include in the IMG tag
     *
     * @return string containing either just a URL or a complete image tag
     * @see    http://gravatar.com/site/implement/images/php/
     * @static
     */
    public static function getGravatar(
        $email,
        $s = 80,
        $d = 'mm',
        $r = 'g',
        $img = false,
        $atts = []
    ) {
        $url = 'https://gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?s=' . $s . '&d=' . $d . '&r=' . $r;
        if ($img) {
            $url = '<img src="' . $url . '" alt=""';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }
        return $url;
    }
}
