<?php

namespace Hwlo\Raccoon\Setup;

use Hwlo\Raccoon\Assets;


class Setup
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
     * Raccoon theme setup actions
     * @return void
     * @static
     */
     static function init()
     {
         /**
          * Enable plugins to manage the document title
          * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
          */
         add_theme_support('title-tag');

         /**
          * Enable post thumbnails
          * @link http://codex.wordpress.org/Post_Thumbnails
          */
         add_theme_support('post-thumbnails');

         /**
          * Enable post formats
          * @link http://codex.wordpress.org/Post_Formats
          */
         $postFormats = ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'];
         add_theme_support('post-formats', $postFormats);

         /**
          * Enable Post Thumbnail support for posts and pages
          * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
          */
         add_theme_support('post-thumbnails');

         /**
          * Enable HTML5 markup support
          * @link http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
          */
         $html5Support = ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form'];
         add_theme_support('html5', $html5Support);

         /**
          * Use main stylesheet for visual editor
          * to add custom styles, edit assets/src/css/_tinymce.less
          * @link https://codex.wordpress.org/add_editor_style
          */
         $cssCustomPath = Assets\Assets::asset_path('dist/css/styles.css');
         add_editor_style($cssCustomPath);

         /**
          * Make this theme available for translation (in /languages dir)
          * @link https://codex.wordpress.org/load_theme_textdomain
          */
         load_theme_textdomain('raccoon', get_template_directory() . '/languages');

         /**
          * Automatically add default posts and comments RSS feed links into wp_head()
          * @link https://codex.wordpress.org/Automatic_Feed_Links
          */
         add_theme_support('automatic-feed-links');
     }
}
