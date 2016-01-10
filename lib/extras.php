<?php

namespace Hwlo\Raccoon\Extras;


class Extras
{
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
     * @return void
     * @static
     */
    static function excerpt_more()
    {
        $text = '&hellip; <a href="' . get_permalink() . '">' . __('En lire plus', 'raccoon') . '</a>';
        return $text;
    }
}
