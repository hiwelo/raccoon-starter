<?php

namespace Hwlo\Raccoon;

class Titles
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
     * Generate an appropriate page title, by page type
     * @global string $theme Raccoon theme vars
     * @return string
     * @static
     */
    public static function content()
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

    /**
     * Customize head title
     * @link   https://developer.wordpress.org/reference/functions/wp_title/
     * @param  string $title initial wp_title()
     * @return string        customized wp_title()
     * @static
     */
    public static function custom_head_title($title)
    {
        $site = [
            'name' => get_bloginfo('name'),
            'desc' => get_bloginfo('description'),
            'page' => get_the_title(),
            'separator' => ' - ',
        ];

        if (is_front_page()) {
            $title = $site['name'] . $site['separator'] . $site['desc'];
        } elseif (is_home()) {
            $page = new WP_Query(['pagename' => get_query_var('pagename')]);
            $page = $page->queried_object;
            $blogname = $page->post_title;

            $title = $blogname . $site['separator'] . $site['name'];
        } elseif (is_category() || is_archive()) {
            $catname = get_cat_name(get_query_var('cat'));
            $title = $catname . $site['separator'] . $site['name'];
        } else {
            $title = $site['page'] . $site['separator'] . $site['name'];
        }

        return $title;
    }
}
