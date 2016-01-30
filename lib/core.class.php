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
    public static $env_status;

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
     * Setup this theme from a json configuration file
     *
     * @param string $file path to the json configuration file
     *
     * @return void
     *
     * @static
     * @uses   Core::$env_status
     * @uses   Core::$manifest
     * @uses   Core::$namespace
     * @uses   Core::_i18nReady()
     * @uses   Core::_loadCustomPostTypes()
     * @uses   Core::_loadDebug()
     * @uses   Core::_loadNavigations()
     * @uses   Core::_loadSidebars()
     * @uses   Core::_loadThemeSupport()
     * @uses   Core::_loadWidgets()
     * @uses   Core::_removeCommentsFeature()
     * @uses   Core::_removePostType()
     */
    public static function setup($file = 'manifest.json')
    {
        // load theme manifest and store it as a static var
        $file = locate_template($file);
        $file = file_get_contents($file);
        self::$manifest = json_decode($file, true);
        // load environment status
        self::$env_status = $_ENV['WP_ENV'];
        // load theme namespace, used mainly for translation methods
        if (!empty(self::$manifest['namespace'])) {
            self::$namespace = self::$manifest['namespace'];
        }
        // load if necessary all debug methods
        self::_loadDebug();
        // make this theme available for translation
        self::_i18nReady();
        // enable theme features asked in the manifest
        self::_loadThemeSupport();
        // register navigations, custom post types, sidebars and widgets
        self::_loadNavigations();
        self::_loadCustomPostTypes();
        self::_loadSidebars();
        self::_loadWidgets();
        // unregister asked post types
        self::_removePostType();
        // remove comments feature completely from WordPress
        self::_removeCommentsFeature();

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
     * Run all debug methods & scripts if we're in a development environment
     *
     * @return void
     *
     * @static
     * @uses   Core::$env_status
     * @uses   \Symfony\Component\Debug\Debug::enable()
     */
    private static function _loadDebug()
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
    private static function _i18nReady()
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
    private static function _loadThemeSupport()
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
    private static function _loadNavigations()
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
    private static function _loadCustomPostTypes()
    {
        if (array_key_exists('custom-post-types', self::$manifest)) {
            $customPostTypes = self::$manifest['custom-post-types'];

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
    private static function _loadSidebars()
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
    private static function _loadWidgets()
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
    private static function _removePostType()
    {
        // get all register post types
        global $wp_post_types;

        if (array_key_exists('remove', self::$manifest['custom-post-types'])) {
            $postTypesToRemove = self::$manifest['custom-post-types']['remove'];

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
    private static function _removeCommentsFeature()
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
}
