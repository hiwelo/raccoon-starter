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

        // we load environment status
        self::$env_status = $_ENV['WP_ENV'];

        // we load theme namespace, used mainly for translation methods
        self::$namespace = self::$manifest['namespace'];

        // we load if necessary all debug methods
        self::_loadDebug();

        // we make this theme available for translation
        self::_i18nReady();

        // we enable theme features asked in the manifest
        self::_loadThemeSupport();
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
     * @static
     */
    public static function loadThemeSupport()
    {
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
