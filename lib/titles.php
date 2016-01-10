<?php

namespace Hwlo\Raccoon\Titles;


class Titles
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
     * Generate an appropriate page title, by page type
     * @global string $theme Raccoon theme vars
     * @return string
     * @static
     */
    static function content()
    {
        global $theme;

        if (is_home()) {
            if (get_option('page_for_posts', true)) {
                return get_the_title(get_option('page_for_posts', true));
            } else {
                return __('Latest Posts', $theme['namespace']);
            }
        } elseif (is_archive()) {
            return get_the_archive_title();
        } elseif (is_search()) {
            return sprintf(__('Search Results for %s', $theme['namespace']), get_search_query());
        } elseif (is_404()) {
            return __('Not Found', $theme['namespace']);
        } else {
            return get_the_title();
        }
    }
}
