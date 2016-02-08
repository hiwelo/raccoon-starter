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
class Raccoon
{
    /**
     * Theme namespace, used mainly for translation methods (_e, __, _n, _x)
     *
     * @var string
     */
    public $namespace = 'raccoon';

    /**
     * Environment status (development, staging, production)
     *
     * @var string
     */
    public $environment = 'production';

    /**
     * Manifest informations for this theme, contain all theme configuration
     *
     * @var array
     */
    public $manifest = [];

    /**
     * Setup this theme with all informations available in the manifest, a JSON
     * configuration file
     *
     * @param string $file manifest filename
     *
     * @return void
     */
    public function __construct($file = '')
    {
        // load theme manifest and store it
        if (!$this->loadManifest($file)) {
            return;
        }

        // load environment status
        $this->loadEnvironmentStatus();

        // load namespace if a specific one is specified
        $this->loadNamespace();

        // load internationalization if exists
        $this->i18nReady();

        // declare all theme features
        $this->loadThemeSupports();

        // declare all navigations
        $this->loadNavigations();
    }

    /**
     * Returned array used for object debug informations
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'manifest' => $this->manifest,
        ];
    }

    /**
     * Load manifest.json content and store it
     *
     * @param string $file filename, must be at theme root folder
     *
     * @return boolean true if the file exists, false otherwise
     *
     * @link https://developer.wordpress.org/reference/functions/locate_template/
     * @uses Raccoon::$manifest
     */
    private function loadManifest($file = '')
    {
        if (empty($file)) {
            $file = 'manifest.json';
        }

        $file = locate_template($file);

        // verify if file exists
        if (!file_exists($file)) {
            return false;
        }

        $file = file_get_contents($file);

        // verify if file isn't empty
        if (empty($file)) {
            return false;
        }

        // parsing json file
        $this->manifest = json_decode($file, true);

        return true;
    }

    /**
     * Load the namespace specific information from the manifest
     *
     * @return boolean true if a namespace is specified, false otherwise
     *
     * @uses Raccoon::$manifest
     * @uses Raccoon::$namespace
     */
    private function loadNamespace()
    {
        if (array_key_exists('namespace', $this->manifest)) {
            if (empty($this->manifest['namespace'])) {
                return false;
            }

            $this->namespace = $this->manifest['namespace'];
            return true;
        }
    }

    /**
     * Search environment status if avanlable and apply specific methods
     *   1. first in $_ENV
     *   2. if non available, in manifest.json (environment-status and env-status)
     *   3. if non available, environment status is set at `production`
     *
     * @global array $_ENV Environment variables
     *
     * @return void
     *
     * @uses Raccoon::$environment
     * @uses Raccoon::$manifest
     * @uses Raccoon::loadDebugMethod()
     */
    private function loadEnvironmentStatus()
    {
        // we load a specific environment status from the environment or the manifest
        if (array_key_exists('WP_ENV', $_ENV)) {
            $this->environment = $_ENV['WP_ENV'];
        } elseif (array_key_exists('environment-status', $this->manifest)) {
            $this->environment = $this->manifest['environment-status'];
        } elseif (array_key_exists('env-status', $this->manifest)) {
            $this->environment = $this->manifest['env-status'];
        }

        switch ($this->environment) {
            case "development":
                $this->loadDebugMethod();
                break;
        }
    }

    /**
     * Run all development environment status specific methods
     *
     * @return void
     *
     * @uses Raccoon::$environment
     * @uses \Symfony\Component\Debug\Debug::enable()
     */
    private function loadDebugMethod()
    {
        if ($this->environment === 'development') {
            // Symfony OOP debug librairy
            Debug::enable();
        }
    }

    /**
     * Theme translation activation (with .po & .mo files)
     *
     * @return void
     *
     * @link https://developer.wordpress.org/reference/functions/load_theme_textdomain/
     * @uses Raccoon::$manifest
     * @uses Raccoon::$namespace
     */
    private function i18nReady()
    {
        if (array_key_exists('languages-directory', $this->manifest)) {
            $i18nDirectory = get_template_directory() . $this->manifest['languages-directory'];
        } else {
            $i18nDirectory = get_template_directory() . '/languages';
        }

        load_theme_textdomain($this->namespace, $i18nDirectory);
    }

    /**
     * Declare all features asked in the manifest
     *
     * @return void
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/
     * @uses Raccoon::$manifest
     */
    private function loadThemeSupports()
    {
        if (array_key_exists('theme-support', $this->manifest)) {
            $supports = $this->manifest['theme-support'];

            foreach ($supports as $key => $value) {
                Tools::parseBooleans($value);

                switch (gettype($value)) {
                    case "boolean":
                        if ($value === true) {
                            add_theme_support($key);
                        }
                        break;

                    default:
                        add_theme_support($key, $value);
                        break;
                }
            }
        }
    }

    /**
     * Register all navigations trom the manifest
     *
     * @return void
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menu/
     * @uses Raccoon::$manifest
     * @uses Raccoon::$namespace
     */
    private function loadNavigations()
    {
        if (array_key_exists('navigations', $this->manifest)) {
            $navigations = $this->manifest['navigations'];

            foreach ($navigations as $location => $description) {
                register_nav_menu($location, __($description, $this->namespace));
            }
        }
    }
}
