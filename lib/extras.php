<?php

namespace Hwlo\Raccoon;


class Extras
{
    /**
     * Excerpt length, number of words
     * @var integer
     */
    static $excerpt_length = 30;

    /**
     * Empty constructor for WordPress add_action or add_filter
     * @return object
     */
    function __construct()
    {
        return $this;
    }

    /**
     * Manage body classes
     * @param  array $classes classes list
     * @return array
     * @static
     */
    static function body_class($classes)
    {
        // add page slug when it does exist
        if (is_single() || is_page() && !is_front_page()) {
            if (!in_array(basename(get_permalink()), $classes)) {
                $classes[] = basename(get_permalink());
            }
        }

        // add specific classes here

        return $classes;
    }

    /**
     * Change the "read more" excerpt text
     * @global string $theme Raccoon theme vars
     * @return void
     * @static
     */
    static function excerpt_more()
    {
        global $theme;
        $text = '&hellip; <a href="' . get_permalink() . '">' . __('En lire plus', $theme['namespace']) . '</a>';
        return $text;
    }

    /**
     * Define a new excerpt length
     * @param  integer $length number of words
     * @return integer
     */
    function custom_excerpt_length($length)
    {
        return self::$excerpt_length;
    }
}
