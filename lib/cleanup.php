<?php

namespace Hwlo\Raccoon;


class CleanUp
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
     * Cleaning functions initialization
     * @return void
     * @static
     */
    static function init()
    {
        /*
         * WP_Admin elements cleanups
         */
        add_action('admin_menu', array(__CLASS__, 'dashboard_widget_removal'));
        add_action('admin_menu', array(__CLASS__, 'menu_items_deletion'));
        add_action('admin_menu', array(__CLASS__, 'menu_subitems_deletion'));
        add_action('admin_menu', array(__CLASS__, 'meta_boxes_customization'));
        add_filter('manage_posts_columns', array(__CLASS__, 'custom_posts_columns'));
        add_filter('manage_pages_columns', array(__CLASS__, 'custom_pages_columns'));
        add_action('wp_before_admin_bar_render', array(__CLASS__, 'admin_bar_customization'));
        add_action('widgets_init', array(__CLASS__, 'unregister_widgets'));

        /*
         * selfish freshstart plugins code parts
         */
        add_action('admin_notices', array(__CLASS__, 'update_notifications_removal'));
        add_action('pre_ping', array(__CLASS__, 'self_pings_removal'));
        add_action('admin_init', array(__CLASS__, 'remove_hello_dolly'));
        add_filter('user_contactmethods', array(__CLASS__, 'user_socialmedias'));

        /*
         * wp-login enhancements
         */
        add_filter('login_headerurl', array(__CLASS__, 'raccoon_login_url'));
        add_filter('login_headertitle', array(__CLASS__, 'raccoon_login_title'));

        /*
         * wp_head() cleanups
         */
        self::head_cleanup();

        /*
         * miscellaneous cleanups
         */
        add_action('init', array(__CLASS__, 'remove_l10n'));
    }

    /**
     * WP Admin dashboard homepage asked widget removal
     * @return void
     */
    function dashboard_widget_removal()
    {
        remove_meta_box('dashboard_right_now','dashboard','core');// right now overview box
        remove_meta_box('dashboard_incoming_links','dashboard','core');// incoming links box
        remove_meta_box('dashboard_quick_press','dashboard','core');// quick press box
        remove_meta_box('dashboard_plugins','dashboard','core');// new plugins box
        remove_meta_box('dashboard_recent_drafts','dashboard','core');// recent drafts box
        remove_meta_box('dashboard_recent_comments','dashboard','core');// recent comments box
        remove_meta_box('dashboard_primary','dashboard','core');// wordpress development blog box
        remove_meta_box('dashboard_secondary','dashboard','core');// other wordpress news box
    }

    /**
     * Remove some items in the admin navigation
     * @link http://codex.wordpress.org/Function_Reference/remove_menu_page
     * @return void
     */
    function menu_items_deletion()
    {
        //remove_menu_page('index.php'); // Dashboard
        //remove_menu_page('edit.php'); // Posts
        //remove_menu_page('upload.php'); // Media
        //remove_menu_page('link-manager.php'); // Links
        //remove_menu_page('edit.php?post_type=page'); // Pages
        //remove_menu_page('edit-comments.php'); // Comments
        //remove_menu_page('themes.php'); // Appearance
        //remove_menu_page('plugins.php'); // Plugins
        //remove_menu_page('users.php'); // Users
        //remove_menu_page('tools.php'); // Tools
        //remove_menu_page('options-general.php'); // Settings
    }

    /**
     * Remove some items in the admin subnavigation
     * @link http://codex.wordpress.org/Function_Reference/remove_submenu_page
     * @return void
     */
    function menu_subitems_deletion()
    {
        //remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' ); // remove tags from edit
    }

    /**
     * Remove some meta boxes from posts, pages or links
     * @link http://codex.wordpress.org/Function_Reference/remove_meta_box
     * @return void
     */
    function meta_boxes_customization()
    {
        /* Remove meta boxes in posts page */
        //remove_meta_box('postcustom','post','normal'); // custom fields metabox
        //remove_meta_box('trackbacksdiv','post','normal'); // trackbacks metabox
        //remove_meta_box('commentstatusdiv','post','normal'); // comment status metabox
        //remove_meta_box('commentsdiv','post','normal'); // comments  metabox
        //remove_meta_box('postexcerpt','post','normal'); // post excerpts metabox
        //remove_meta_box('authordiv','post','normal'); // author metabox
        //remove_meta_box('revisionsdiv','post','normal'); // revisions  metabox
        //remove_meta_box('tagsdiv-post_tag','post','normal'); // tags
        //remove_meta_box('slugdiv','post','normal'); // slug metabox
        //remove_meta_box('categorydiv','post','normal'); // comments metabox
        //remove_meta_box('postimagediv','post','normal'); // featured image metabox
        //remove_meta_box('formatdiv','post','normal'); // format metabox

        /* Remove meta boxes in pages page */
        //remove_meta_box('postcustom','page','normal'); // custom fields metabox
        //remove_meta_box('trackbacksdiv','page','normal'); // trackbacks metabox
        //remove_meta_box('commentstatusdiv','page','normal'); // comment status metabox
        //remove_meta_box('commentsdiv','page','normal'); // comments  metabox
        //remove_meta_box('authordiv','page','normal'); // author metabox
        //remove_meta_box('revisionsdiv','page','normal'); // revisions  metabox
        //remove_meta_box('postimagediv','page','side'); // featured image metabox
        //remove_meta_box('slugdiv','page','normal'); // slug metabox

        /* Remove meta boxes for links */
        //remove_meta_box('linkcategorydiv','link','normal');
        //remove_meta_box('linkxfndiv','link','normal');
        //remove_meta_box('linkadvanceddiv','link','normal');
    }

    /**
     * Choose informations to remove from posts list
     * @param  array $defaults columns list
     * @return array
     */
    function custom_posts_columns($defaults)
    {
        unset($defaults['comments']);
        // unset($defaults['author']);
        unset($defaults['tags']);
        // unset($defaults['date']);
        // unset($defaults['categories']);

        return $defaults;
    }

    /**
     * Choose informations to remove from pages list
     * @param  array $defaults columns list
     * @return array
     */
    function custom_pages_columns($defaults)
    {
        unset($defaults['comments']);
        // unset($defaults['author']);
        // unset($defaults['date']);

        return $defaults;
    }

    /**
     * Remove item into the admin bar
     * @global object $wp_admin_bar Admin bar object
     * @return void
     */
    function admin_bar_customization()
    {
        global $wp_admin_bar;
        // $wp_admin_bar->remove_menu('wp-logo'); // remove the whole wordpress logo, help, etc. menu
        $wp_admin_bar->remove_menu('comments'); // remove comments submenu
    }

    /**
     * Choose widget to unregister in the WordPress Admin dashboard
     * @link http://wpmu.org/how-to-remove-default-wordpress-widgets-and-clean-up-your-widgets-page/
     * @return void
     */
    function unregister_widgets()
    {
        // unregister_widget('WP_Widget_Pages');
        // unregister_widget('WP_Widget_Calendar');
        // unregister_widget('WP_Widget_Archives');
        // unregister_widget('WP_Widget_Links');
        // unregister_widget('WP_Widget_Meta');
        // unregister_widget('WP_Widget_Search');
        // unregister_widget('WP_Widget_Text');
        // unregister_widget('WP_Widget_Categories');
        // unregister_widget('WP_Widget_Recent_Posts');
        // unregister_widget('WP_Widget_Recent_Comments');
        // unregister_widget('WP_Widget_RSS');
        // unregister_widget('WP_Widget_Tag_Cloud');
        // unregister_widget('WP_Nav_Menu_Widget');
        // unregister_widget('Twenty_Eleven_Ephemera_Widget');
    }

    /**
     * Remove update notifications for everyone except admin users
     * @return void
     */
    function update_notifications_removal()
    {
        remove_action('admin_notices', 'update_nag', 3);
    }

    /**
     * Disable self trackbacking links
     * @param  array $links trackback links
     * @return void
     */
    function self_pings_removal(&$links)
    {
        foreach ($links as $l => $link) {
            if (0 === strpos($link, home_url())) {
                unset($links[$l]);
            }
        }
    }

    /**
     * Hello Dolly plugin removal
     * @return void
     */
    function remove_hello_dolly()
    {
        if (is_admin() && file_exists(WP_PLUGIN_DIR . '/hello.php')) {
            @unlink(WP_PLUGIN_DIR . '/hello.php');
        }
    }

    /**
     * Remove old IRC-like contact methods and add social media fields
     * @param  array $contactmethods contact method fields
     * @return array
     */
    function user_socialmedias($contactmethods)
    {
        // we unset yim, aim and jabber fields
        unset($contactmethods['yim']);
        unset($contactmethods['aim']);
        unset($contactmethods['jabber']);

        // we set a facebook and a twitter field
        $contactmethods['raccoon_twitter'] = 'Twitter';
        $contactmethods['raccoon_facebook'] = 'Facebook';

        return $contactmethods;
    }

    /**
     * Set WP Login header url to the home url
     * @return string
     */
    function raccoon_login_url()
    {
        return home_url('/');
    }

    /**
     * Set WP Login header title to the website title
     * @return string
     */
    function raccoon_login_title()
    {
        return get_option('blogname');
    }

    /**
     * Remove l10n script used for javascript translation
     * @return void
     */
    function remove_l10n()
    {
        if (!is_admin()) {
            wp_deregister_script('l10n');
        }
    }

    /**
     * WordPress head meta cleanup
     * @return void
     */
    private function head_cleanup()
    {
        // remove cat feeds
        remove_action('wp_head', 'feed_links_extra', 3);

        // remove post & comments feeds
        // remove_action('wp_head', 'feed_links', 2);

        // EditURI link
        remove_action('wp_head', 'rsd_link');

        // window live writer
        remove_action('wp_head', 'wlwmanifest_link');

        // previous link
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);

        // start link
        remove_action('wp_head', 'start_post_rel_link', 10, 0);

        // link for adjacent posts
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

        // WordPress version
        remove_action('wp_head', 'wp_generator');

        // remove WP version from CSS & scripts
        add_filter('style_loader_src', [__CLASS__, 'remove_wordpress_version_css_js'], 10);
        add_filter('script_loader_src', [__CLASS__, 'remove_wordpress_version_css_js'], 10);
    }

    /**
     * Remove WordPress version from CSS & Scripts
     * @param  string $src css & script url
     * @return string
     */
    function remove_wordpress_version_css_js($src)
    {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
}
