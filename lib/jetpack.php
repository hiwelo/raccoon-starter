<?php

namespace Hwlo\Raccoon\Jetpack;


class Jetpack
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
     * Jetpack items setup function
     * @return void
     * @static
     */
    static function setup()
    {
        self::infinite_scroll_activation();
    }

    /**
     * Jetpack Infinite Scroll activation
     * @link https://jetpack.me/support/infinite-scroll/
     * @return void
     */
    function infinite_scroll_activation()
    {
        $params = [
            'container' => 'main',
            'render' => array(__CLASS__, 'infinite_scroll_render'),
            'footer' => 'page',
        ];
        add_theme_support('infinite-scroll', $params);
    }

    /**
     * Jetpack Infinite Scroll custom render function
     * @return void
     */
    function infinite_scroll_render()
    {
        while (have_posts()) {
            the_post();
        }
    }
}