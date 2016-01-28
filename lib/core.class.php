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
 * @link     https://codex.wordpress.org/Functions_File_Explained
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
 * @link     https://codex.wordpress.org/Functions_File_Explained
 */
class Core
{
    /**
     * Theme namespace, used mainly for translation methods (_e, __, _n)
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
     * Setup this theme from a json configuration file
     *
     * @param string $file path to the json configuration file
     *
     * @return void
     * @static
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
        self::$namespace = self::$manifest['namespace'];

        // load if necessary all debug methods
        self::_loadDebug();

        // make this theme available for translation
        self::_i18nReady();

        // enable theme features asked in the manifest
        self::_loadThemeSupport();

        // register navigations and custom post types
        self::_loadNavigations();
        self::_loadCustomPostTypes();
    }

    /**
     * Run all debug methods & scripts if we're in a development environment
     *
     * @return void
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
     * @static
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
     * @link   https://codex.wordpress.org/Function_Reference/add_theme_support
     * @static
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
                }
            }
        }
    }

    /**
     * Register all navigations from the manifest
     *
     * @return void
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menu
     * @static
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
     * @link   https://codex.wordpress.org/Function_Reference/register_post_type
     * @static
     */
    private static function _loadCustomPostTypes()
    {
        if (array_key_exists('custom-post-types', self::$manifest)) {
            $customPostTypes = self::$manifest['custom-post-types'];

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
                            $labels[$key] = _x($value, $contextKeys[$key], self::$namespace);
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
}
