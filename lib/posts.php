<?php
/**
 * Posts custom methods
 *
 * PHP version 5
 *
 * @category Posts
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
namespace Hwlo\Raccoon;

/**
 * Posts custom methods
 *
 * PHP version 5
 *
 * @category Posts
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Posts
{
    /**
     * Return thumbnail URL for an asked post
     *
     * @param integer $post_id asked post id (or thumbnail size)
     * @param string  $size    thumbnail size
     *
     * @return string thumbnail URL
     * @static
     */
    public static function getThumbnail($post_id = null, $size = 'thumbnail')
    {
        if (!is_numeric($post_id) && is_string($post_id)) {
            $size = $post_id;
            $post_id = null;
        }

        $thumb['id'] = get_post_thumbnail_id($post_id);
        $thumb['url'] = wp_get_attachment_image_src($thumb_id, $size);

        return $thumb['url'][0];
    }
}
