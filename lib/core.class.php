<?php
/**
 * Template core methods
 *
 * PHP version 5
 *
 * @category Core
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
namespace Hwlo\Raccoon;

use Symfony\Component\Debug\Debug;

/**
 * Template core methods
 *
 * PHP version 5
 *
 * @category Core
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
class Core
{
    /**
     * Theme namespace, used mainly for translation methods (_e, __, _n, _x)
     *
     * @var    string
     * @static
     */
    public static $namespace = '';

    /**
     * Manifest informations for this theme
     *
     * @var    object
     * @static
     */
    public static $manifest;

    /**
     * Environment status (development, staging, production)
     *
     * @var    string
     * @static
     */
    public static $env_status = "production";

    /**
     * Register all admin menu item to remove
     *
     * @var    array
     * @static
     */
    public static $adminMenuItemsToRemove = [];

    /**
     * Register all admin submenu item to remove
     *
     * @var    array
     * @static
     */
    public static $adminSubMenuItemsToRemove = [];

    /**
     * Register all meta box to remove
     *
     * @var    array
     * @static
     */
    public static $metaBoxToRemove = [];

    /**
     * Register all post types support to remove
     *
     * @var    array
     * @static
     */
    public static $postTypeSupportToRemove = [];

    /**
     * Register all widgets to remove
     *
     * @var    array
     * @static
     */
    public static $widgetsToRemove = [];

    /**
     * Register all sidebars to remove
     *
     * @var    array
     * @static
     */
    public static $sidebarsToRemove = [];

    /**
     * Setup this theme from a json configuration file
     *
     * @param string $file path to the json configuration file
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/add_action
     * @link   https://codex.wordpress.org/Function_Reference/is_admin
     * @static
     * @uses   Core::$adminMenuItemsToRemove
     * @uses   Core::$adminSubMenuItemsToRemove
     * @uses   Core::$env_status
     * @uses   Core::$metaBoxToRemove
     * @uses   Core::$postTypeSupportToRemove
     * @uses   Core::$sidebarsToRemove
     * @uses   Core::$widgetsToRemove
     * @uses   Core::i18nReady()
     * @uses   Core::loadCustomPostTypes()
     * @uses   Core::loadDebug()
     * @uses   Core::loadNavigations()
     * @uses   Core::loadSidebars()
     * @uses   Core::loadThemeSupport()
     * @uses   Core::loadWidgets()
     * @uses   Core::removeCommentsFeature()
     * @uses   Core::removePostType()
     * @uses   Core::removeWidgetFeature()
     */
    public static function setup($file = 'manifest.json')
    {
        // load theme manifest and store it as a static var
        self::loadManifest($file);
        // load environment status
        self::loadEnvironmentStatus();
        // load theme namespace, used mainly for translation methods
        self::loadNamespace();
        // load if necessary all debug methods
        self::loadDebug();
        // make this theme available for translation
        self::i18nReady();
        // enable theme features asked in the manifest
        self::loadThemeSupport();
        // register navigations, custom post types, sidebars and widgets
        self::loadNavigations();
        self::loadCustomPostTypes();
        self::loadSidebars();
        self::loadWidgets();
        // unregister asked post types
        self::removePostType();
        // remove comments feature completely from WordPress
        self::removeCommentsFeature();
        // remove widgets feature completely from WordPress
        self::removeWidgetFeature();

        // add remove actions
        if (count(self::$widgetsToRemove)) {
            add_action('widgets_init', [__CLASS__, 'removeWidgets'], 11);
        }
        if (count(self::$sidebarsToRemove)) {
            add_action('widgets_init', [__CLASS__, 'removeSidebars'], 11);
        }

        // if we're in admin dashboard,
        // add an action to remove elements from admin menu bar, meta boxes or
        // asked post types supports
        if (is_admin()) {
            if (count(self::$adminMenuItemsToRemove)) {
                add_action('admin_menu', [__CLASS__, 'removeAdminMenuItem']);
            }
            if (count(self::$adminSubMenuItemsToRemove)) {
                add_action('admin_menu', [__CLASS__, 'removeAdminSubMenuItem']);
            }
            if (count(self::$metaBoxToRemove)) {
                add_action('admin_init', [__CLASS__, 'removeMetaBox']);
            }
            if (count(self::$postTypeSupportToRemove)) {
                add_action('admin_init', [__CLASS__, 'removePostTypeSupport']);
            }
        }
    }

    /**
     * Remove all admin menu item from the $adminMenuItemsToRemove property
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/remove_menu_page
     * @static
     * @uses   Core::$adminMenuItemsToRemove
     */
    public static function removeAdminMenuItem()
    {
        if (count(self::$adminMenuItemsToRemove)) {
            // get all items to remove from admin menu bar
            $items = self::$adminMenuItemsToRemove;

            foreach ($items as $item) {
                remove_menu_page($item);
            }
        }
    }

    /**
     * Remove all admin sub menu item from the $adminSubMenuItemsToRemove property
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/remove_submenu_page
     * @static
     * @uses   Core::$adminSubMenuItemsToRemove
     */
    public static function removeAdminSubMenuItem()
    {
        if (count(self::$adminSubMenuItemsToRemove)) {
            // get all items to remove from admin menu bar
            $items = self::$adminSubMenuItemsToRemove;

            foreach ($items as $item) {
                remove_submenu_page($item[0], $item[1]);
            }
        }
    }

    /**
     * Remove all meta boxes from the $metaBoxToRemove property
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/remove_meta_box
     * @static
     * @uses   Core::$metaBoxToRemove
     */
    public static function removeMetaBox()
    {
        if (count(self::$metaBoxToRemove)) {
            // get all meta boxes to remove
            $items = self::$metaBoxToRemove;

            foreach ($items as $item) {
                remove_meta_box($item[0], $item[1], $item[2]);
            }
        }
    }

    /**
     * Remove all post types feature support from the $postTypeSupportToRemove
     * property
     *
     * @return void
     *
     * @link https://codex.wordpress.org/Function_Reference/remove_post_type_support
     *
     * @static
     * @uses   Core::$postTypeSupportToRemove
     */
    public static function removePostTypeSupport()
    {
        if (count(self::$postTypeSupportToRemove)) {
            // get all meta boxes to remove
            $items = self::$postTypeSupportToRemove;

            foreach ($items as $item) {
                remove_post_type_support($item[0], $item[1]);
            }
        }
    }

    /**
     * Remove all widgets from the $widgetsToRemove property
     *
     * @return void
     *
     * @link https://codex.wordpress.org/Function_Reference/unregister_widget
     *
     * @static
     * @uses   Core::$widgetsToRemove
     */
    public static function removeWidgets()
    {
        if (count(self::$widgetsToRemove)) {
            // get all widgets to remove
            $items = self::$widgetsToRemove;

            foreach ($items as $item) {
                unregister_widget($item);
            }
        }
    }

    /**
     * Remove all sidebars from the $widgetsToRemove property
     *
     * @return void
     *
     * @link https://codex.wordpress.org/Function_Reference/unregister_sidebar
     *
     * @static
     * @uses   Core::$sidebarsToRemove
     */
    public static function removeSidebars()
    {
        if (count(self::$sidebarsToRemove)) {
            // get all sidebars to remove
            $items = self::$sidebarsToRemove;

            foreach ($items as $item) {
                unregister_sidebar($item);
            }
        }
    }

    /**
     * Return a "string boolean" to a real boolean var
     *
     * @param string|boolean $value string to parse into a real boolean
     *
     * @return boolean
     *
     * @static
     */
    public static function stringToRealBooleans(&$value)
    {
        switch ($value) {
            case "true":
                $value = true;
                break;
            case "false":
                $value = false;
                break;
        }

        return $value;
    }

    /**
     * Load manifest content and store it in this object
     *
     * @param string $file manifest location
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/locate_template
     * @static
     * @uses   Core::$manifest
     */
    private static function loadManifest($file)
    {
        $file = locate_template($file);
        $file = file_get_contents($file);
        self::$manifest = json_decode($file, true);
    }

    /**
     * Search environment status if available:
     *   1. first in $_ENV
     *   2. if non available, in manifest.json (environment-status and env-status)
     *   3. if non available, environment status is set at `production`
     *
     * @global array $_ENV Environment variables
     *
     * @return void
     *
     * @static
     * @uses   Core::$env_status
     * @uses   Core::$manifest
     */
    private static function loadEnvironmentStatus()
    {
        if (array_key_exists('WP_ENV', $_ENV)) {
            self::$env_status = $_ENV['WP_ENV'];
        } elseif (array_key_exists('environment-status', self::$manifest)) {
            self::$env_status = self::$manifest['environment-status'];
        } elseif (array_key_exists('env-status', self::$manifest)) {
            self::$env_status = self::$manifest['env-status'];
        }
    }

    /**
     * Load theme namespace information from the manifest
     *
     * @return void
     *
     * @static
     * @uses   Core::$manifest
     * @uses   Core::$namespace
     */
    private static function loadNamespace()
    {
        if (!empty(self::$manifest['namespace'])) {
            self::$namespace = self::$manifest['namespace'];
        }
    }

    /**
     * Run all debug methods & scripts if we're in a development environment
     *
     * @return void
     *
     * @static
     * @uses   Core::$env_status
     * @uses   \Symfony\Component\Debug\Debug::enable()
     */
    private static function loadDebug()
    {
        // only if we're in development, we run all scripts
        if (self::$env_status === 'development') {
            // symfony OPP debug librairy
            Debug::enable();
        }
    }

    /**
     * Theme translation activation (.po & .mo)
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/load_theme_textdomain
     * @static
     * @uses   Core::$namespace
     */
    private static function i18nReady()
    {
        load_theme_textdomain(
            self::$namespace,
            get_template_directory() . '/languages'
        );
    }

    /**
     * Declare all features asked in the manifest
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/add_theme_support
     * @static
     * @uses   Core::$manifest
     */
    private static function loadThemeSupport()
    {
        if (array_key_exists('theme-support', self::$manifest)) {
            $supports = self::$manifest['theme-support'];
            foreach ($supports as $key => $value) {
                // we parse "true" string to a boolean if necessary
                if ($value === "true") {
                    $value = boolval($value);
                }
                switch (gettype($value)) {
                    case "boolean":
                        if ($value === true) {
                            add_theme_support($key);
                        }
                        break;
                    case "array":
                        add_theme_support($key, $value);
                        break;
                    case "string":
                        add_theme_support($key, $value);
                        break;
                }
            }
        }
    }

    /**
     * Register all navigations from the manifest
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menu
     * @static
     * @uses   Core::$manifest
     * @uses   Core::$namespace
     */
    private static function loadNavigations()
    {
        if (array_key_exists('navigations', self::$manifest)) {
            $navigations = self::$manifest['navigations'];
            foreach ($navigations as $location => $description) {
                register_nav_menu($location, __($description, self::$namespace));
            }
        }
    }

    /**
     * Register all custom post types from the manifest
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/register_post_type
     * @static
     * @uses   Core::$manifest
     * @uses   Core::$namespace
     */
    private static function loadCustomPostTypes()
    {
        if (array_key_exists('post-types', self::$manifest)) {
            $customPostTypes = self::$manifest['post-types'];

            // if exists, remove post type asked to unregistration
            if (array_key_exists('remove', $customPostTypes)) {
                unset($customPostTypes['remove']);
            }

            foreach ($customPostTypes as $postType => $args) {
                // parsing labels values
                if (array_key_exists('labels', $args)) {
                    $labels = $args['labels'];
                    // parsing arguments to add translation
                    foreach ($labels as $key => $value) {
                        // Keys which required a gettext with translation
                        $contextKeys = [
                            'name' => 'post type general name',
                            'singular_name' => 'post type singular name',
                            'menu_name' => 'admin menu',
                            'name_admin_bar' => 'add new on admin bar',
                        ];
                        $contextKeysList = array_keys($contextKeys);
                        // add a gettext context for some keys
                        // or simply translate a string
                        if (in_array($key, $contextKeysList)) {
                            $labels[$key] = _x(
                                $value,
                                $contextKeys[$key],
                                self::$namespace
                            );
                        } else {
                            $labels[$key] = __($value, self::$namespace);
                        }
                    }
                    $args['labels'] = $labels;
                }
                // parsing label value
                if (array_key_exists('label', $args)) {
                    $args['label'] = __($args['label'], self::$namespace);
                }
                // parsing description value
                if (array_key_exists('description', $args)) {
                    $args['description'] = __(
                        $args['description'],
                        self::$namespace
                    );
                }
                // replace "true" string value to a real boolean
                $stringBooleans = array_keys($args, "true");
                if ($stringBooleans) {
                    foreach ($stringBooleans as $key) {
                        $args[$key] = true;
                    }
                }
                // custom post type registration
                register_post_type($postType, $args);
            }
        }
    }

    /**
     * Register all sidebars from the manifest
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/register_sidebar
     * @static
     * @uses   Core::$manifest
     * @uses   Core::$namespace
     */
    private static function loadSidebars()
    {
        if (array_key_exists('sidebars', self::$manifest)) {
            $sidebars = self::$manifest['sidebars'];
            foreach ($sidebars as $args) {
                // parsing arguments to add translation for some keys
                foreach ($args as $key => $value) {
                    $i18nKeys = ['name', 'description'];
                    if (in_array($key, $i18nKeys)) {
                        $args[$key] = __($value, self::$namespace);
                    }
                }
                // sidebar registration
                register_sidebar($args);
            }
        }
    }

    /**
     * Register all widgets from the manifest
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Function_Reference/register_widget
     * @static
     * @uses   Core::$manifest
     */
    private static function loadWidgets()
    {
        if (array_key_exists('widgets', self::$manifest)) {
            $widgets = self::$manifest['widgets'];
            foreach ($widgets as $widget) {
                register_widget($widget);
            }
        }
    }

    /**
     * Unregister asked post types from the manifest
     *
     * @global array $wp_post_types List of post types
     *
     * @return void
     *
     * @link   https://codex.wordpress.org/Administration_Menus
     * @static
     * @uses   Core::$adminMenuItemsToRemove
     * @uses   Core::$manifest
     */
    private static function removePostType()
    {
        // get all register post types
        global $wp_post_types;

        if (array_key_exists('post-types', self::$manifest)
            && array_key_exists('remove', self::$manifest['post-types'])
        ) {
            $postTypesToRemove = self::$manifest['post-types']['remove'];

            foreach ($postTypesToRemove as $postType) {
                // get post type name to remove from admin menu bar
                $itemName = $wp_post_types[$postType]->name;

                // unregister asked post type
                unset($wp_post_types[$postType]);

                // remove asked post type from admin menu bar
                if ($itemName === 'post') {
                    $itemURL = 'edit.php';
                } else {
                    $itemURL = 'edit.php?post_type=' . $itemName;
                }

                // register item menu to remove
                self::$adminMenuItemsToRemove[] = $itemURL;
            }
        }
    }

    /**
     * Remove globally the comments feature if asked in the manifest
     *
     * @return void
     *
     * @link https://codex.wordpress.org/Function_Reference/get_post_types
     * @link https://codex.wordpress.org/Function_Reference/remove_meta_box
     * @link https://codex.wordpress.org/Function_Reference/remove_post_type_support
     * @link https://codex.wordpress.org/Function_Reference/update_option
     *
     * @static
     * @uses   Core::$adminMenuItemsToRemove
     * @uses   Core::$adminSubMenuItemsToRemove
     * @uses   Core::$manifest
     * @uses   Core::$metaBoxToRemove
     * @uses   Core::$postTypeSupportToRemove
     * @uses   Core::stringToRealBooleans
     */
    private static function removeCommentsFeature()
    {
        // if this action is asked in the manifest
        if (array_key_exists('theme-features', self::$manifest)
            && array_key_exists('comments', self::$manifest['theme-features'])
        ) {
            $commentsFeature = self::$manifest['theme-features']['comments'];
            Core::stringToRealBooleans($commentsFeature);

            if ($commentsFeature === false) {
                // count options to reset at 0
                $options = ['comments_notify', 'default_pingback_flag'];
                foreach ($options as $option) {
                    update_option($option, 0);
                }

                // remove post type comment support
                $postTypes = get_post_types();
                foreach ($postTypes as $postType) {
                    // remove comment status
                    self::$metaBoxToRemove[] = [
                        'commentstatusdiv',
                        $postType,
                        'normal'
                    ];
                    // remove trackbacks
                    self::$metaBoxToRemove[] = [
                        'trackbacksdiv',
                        $postType,
                        'normal'
                    ];
                    // remove all comments/trackbacks from tables
                    self::$postTypeSupportToRemove[] = [
                        $postType,
                        'comments'
                    ];
                    self::$postTypeSupportToRemove[] = [
                        $postType,
                        'trackbacks'
                    ];
                }

                // remove dashboard meta box for recents comments
                self::$metaBoxToRemove[] = [
                    'dashboard_recent_comments',
                    'dashboard',
                    'normal'
                ];

                // remove admin menu entries
                self::$adminMenuItemsToRemove[] = 'edit-comments.php';
                self::$adminSubMenuItemsToRemove[] = [
                    'options-general.php',
                    'options-discussion.php'
                ];
            }
        }
    }

    /**
     * Unregister all widgets if asked in the manifest
     *
     * @global array $wp_registered_sidebars List all sidebars
     * @global array $wp_widget_factory      List all widgets
     *
     * @return void
     *
     * @static
     * @uses   Core::$adminSubMenuItemsToRemove
     * @uses   Core::$manifest
     * @uses   Core::$sidebarsToRemove
     * @uses   Core::$widgetsToRemove
     * @uses   Core::stringToRealBooleans()
     */
    private static function removeWidgetFeature()
    {
        if (array_key_exists('theme-features', self::$manifest)
            && array_key_exists('widget', self::$manifest['theme-features'])
        ) {
            $widgetFeature = self::$manifest['theme-features']['widget'];
            self::stringToRealBooleans($widgetFeature);

            // remove defaults widget
            $defaultWidgets = [
                'WP_Widget_Pages',
                'WP_Widget_Archives',
                'WP_Widget_Meta',
                'WP_Widget_Text',
                'WP_Widget_Recent_Posts',
                'WP_Widget_Recent_Comments',
                'WP_Widget_Calendar',
                'WP_Widget_Links',
                'WP_Widget_Search',
                'WP_Widget_Categories',
                'WP_Widget_RSS',
                'WP_Widget_Tag_Cloud',
                'WP_Nav_Menu_Widget',
                'Twenty_Eleven_Ephemera_Widget',
            ];
            foreach ($defaultWidgets as $widget) {
                self::$widgetsToRemove[] = $widget;
            }

            // list all custom widgets for unregistration
            global $wp_widget_factory;
            $widgets = $wp_widget_factory->widgets;
            foreach ($widgets as $id => $widget) {
                self::$widgetsToRemove[] = $id;
            }

            // list all sidebars for unregistration
            global $wp_registered_sidebars;
            $sidebars = $wp_registered_sidebars;
            foreach ($sidebars as $id => $sidebar) {
                self::$sidebarsToRemove[] = $id;
            }

            // remove widget admin menu item
            self::$adminSubMenuItemsToRemove[] = [
                'themes.php',
                'widgets.php'
            ];
        }
    }
}
