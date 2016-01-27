<?php
/**
 * Navigation helper class
 *
 * PHP version 5
 *
 * @category Navigations
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
namespace Hwlo\Raccoon;

/**
 * Navigation helper class
 *
 * PHP version 5
 *
 * @category Navigations
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Navigations
{
    /**
     * Registered custom navigations
     *
     * @var array
     */
    private $_navigations = [];

    /**
     * Theme namespace, used for l10n
     *
     * @var string
     */
    private $_namespace = '';

    /**
     * Constructor, registers a new list of navigation menus
     *
     * @param array $list menu list (menu location identifier as
     *                    key => menu description as value)
     *
     * @return void
     */
    public function __construct(array $list)
    {
        // search the theme namespace
        global $theme;
        $this->_namespace = $theme['namespace'];

        // saves navigation menus declared here
        if (is_array($list)) {
            $this->register($list);
        }

        // Navigation menus Wordpress registration
        $this->registerMenus();
    }

    /**
     * Get an object's variable
     *
     * @param string $var var name
     *
     * @return mixed var value
     */
    public function get($var)
    {
        if ($this->$var) {
            return $this->$var;
        } else {
            return false;
        }
    }

    /**
     * Set an object's variable
     *
     * @param string $var   var name
     * @param mixed  $value var value
     *
     * @return boolean
     */
    public function set($var, $value)
    {
        $reserved = [
            'config',
            'options',
            'slug',
            'location',
            'description',
            'namespace',
        ];

        if (!in_array($var, $reserved)) {
            $this->$var = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * WordPress wp_get_nav_menu_items() method helper
     *
     * @param string $location menu id, name or slug
     * @param array  $args     optional arguments
     *
     * @return array
     */
    public function getMenu($location, $args = [])
    {
        return wp_get_nav_menu_items($location, $args);
    }

    /**
     * WordPress has_nav_menu() method helper
     *
     * @param string $location menu location identifier slug
     *
     * @return boolean
     * @link   https://codex.wordpress.org/Function_Reference/has_nav_menu
     */
    public function hasNavMenu($location)
    {
        if ($this->isRegistered($location)) {
            return has_nav_menu($location);
        }
    }

    /**
     * Is this navigation menu already registered ?
     *
     * @param string $location menu location identifier slug
     *
     * @return boolean
     */
    public function isRegistered($location)
    {
        return array_key_exists($location, $this->_navigations);
    }

    /**
     * Returns a navigation menu, WordPress wp_nav_menu() method helper
     *
     * @param string $location theme location to be used
     * @param array  $args     array of nav menu arguments
     *
     * @return void
     * @link   https://codex.wordpress.org/Function_Reference/wp_nav_menu
     */
    public function menu($location, $args = [])
    {
        $defaults = [
            'container' => 'nav',
            'container_class' => 'menu__container',
            'echo' => 0,
            'fallback_cb' => false,
        ];
        $args = array_replace_recursive($defaults, $args);

        if ($this->isRegistered($location)) {
            $args['theme_location'] = $location;
            wp_nav_menu($args);
        }
    }

    /**
     * Registers a list of navigation menus
     *
     * @param array $list navigation menus list
     *
     * @return void
     */
    public function register(array $list)
    {
        foreach ($list as $location => $description) {
            $this->register_nav_menu($location, $description);
        }
    }

    /**
     * Registers a new navigation menu
     *
     * @param string $location    menu location identifier, like a slug
     * @param string $description menu location descriptive text
     *
     * @return boolean
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menu/
     */
    public function registerMenu($location, $description = '')
    {
        // controls vars
        if (!is_string($location)) {
            return false;
        }
        if (empty($description)) {
            $description = $this->_getDesc($location);
        }

        // register this navigation menu
        if (!array_key_exists($location, $this->_navigations)) {
            $this->_navigations[$location] = __($description, $this->_namespace);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Registers the navigation menus declared for this theme
     * this function is an helper for WordPress register_nav_menus()
     *
     * @return void
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menus/
     */
    public function registerMenus()
    {
        // get registered navigations
        $navigations = $this->_navigations;

        // navigations registration
        register_nav_menus($navigations);
    }

    /**
     * Unregisters a specified navigation menu
     *
     * @param string $location menu location identifier slug
     *
     * @return void
     * @link   https://codex.wordpress.org/Function_Reference/unregister_nav_menu
     */
    public function unregister($location)
    {
        if (array_key_exists($location, $this->_navigations)) {
            unset($this->_navigations[$location]);
            unregister_nav_menu($location);
        }
    }

    /**
     * Unregisters all declared navigation menus
     *
     * @return void
     */
    public function unregisterAll()
    {
        foreach ($this->_navigations as $location => $description) {
            $this->unregister($location);
        }
    }

    /**
     * Returns a human-friendly text from the location data
     *
     * @param string $location menu location identifier, like a slug
     *
     * @return string menu location identifier more "human-friendly"
     */
    private function _getDesc($location)
    {
        // transforms location to a more human-friendly text
        $description = str_replace('_', ' ', $location);
        $description = str_replace('-', ' ', $description);
        $description = ucwords(strtolower($location));

        return $description;
    }

    /**
     * Return some default debug informations
     *
     * @return void
     */
    public function __debugInfo()
    {
        return [
            'namespace' => $this->_namespace,
            'navigations' => $this->_navigations,
        ];
    }
}
