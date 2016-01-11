<?php

namespace Hwlo\Raccoon;

class Navigations
{
    /**
     * Registered custom navigations
     * @var array
     */
    private $navigations = [];

    /**
     * Theme namespace, used for l10n
     * @var string
     */
    private $namespace = '';

    /**
     * Constructor, registers a new list of navigation menus
     * @param array $list menu list (menu location identifier as key => menu description as value)
     * @return void
     */
    public function __construct(array $list)
    {
        // search the theme namespace
        global $theme;
        $this->namespace = $theme['namespace'];

        // saves navigation menus declared here
        if (is_array($list)) {
            $this->register($list);
        }

        // Navigation menus Wordpress registration
        $this->register_nav_menus();
    }

    /**
     * get an object's variable
     * @param  string $var var name
     * @return mixed       var value
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
     * set an object's variable
     * @param  string $var   var name
     * @param  mixed  $value var value
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
     * @param  string $location menu id, name or slug
     * @param  array  $args     optional arguments
     * @return array
     */
    public function get_menu($location, $args = [])
    {
        return wp_get_nav_menu_items($location, $args);
    }

    /**
     * WordPress has_nav_menu() method helper
     * @link   https://codex.wordpress.org/Function_Reference/has_nav_menu
     * @param  string  $location menu location identifier slug
     * @return boolean
     */
    public function has_nav_menu($location)
    {
        if ($this->is_registered($location)) {
            return has_nav_menu($location);
        }
    }

    /**
     * Is this navigation menu already registered ?
     * @param  string  $location menu location identifier slug
     * @return boolean
     */
    public function is_registered($location)
    {
        return array_key_exists($location, $this->navigations);
    }

    /**
     * Returns a navigation menu, WordPress wp_nav_menu() method helper
     * @link   https://codex.wordpress.org/Function_Reference/wp_nav_menu
     * @param  string $location theme location to be used
     * @param  array  $args     array of nav menu arguments
     * @return void
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

        if ($this->is_registered($location)) {
            $args['theme_location'] = $location;
            wp_nav_menu($args);
        }
    }

    /**
     * Registers a list of navigation menus
     * @param  array  $list navigation menus list
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
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menu/
     * @param  string $location    menu location identifier, like a slug
     * @param  string $description menu location descriptive text
     * @return bool
     */
    public function register_nav_menu($location, $description = '')
    {
        // controls vars
        if (!is_string($location)) {
            return false;
        }
        if (empty($description)) {
            $description = $this->get_description($location);
        }

        // register this navigation menu
        if (!array_key_exists($location, $this->navigations)) {
            $this->navigations[$location] = __($description, $this->namespace);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Registers the navigation menus declared for this theme
     * this function is an helper for WordPress register_nav_menus()
     * @link   https://codex.wordpress.org/Function_Reference/register_nav_menus/
     * @return void
     */
    public function register_nav_menus()
    {
        // get registered navigations
        $navigations = $this->navigations;

        // navigations registration
        register_nav_menus($navigations);
    }

    /**
     * Unregisters a specified navigation menu
     * @link   https://codex.wordpress.org/Function_Reference/unregister_nav_menu
     * @param  string $location menu location identifier slug
     * @return void
     */
    public function unregister($location)
    {
        if (array_key_exists($location, $this->navigations)) {
            unset($this->navigations[$location]);
            unregister_nav_menu($location);
        }
    }

    /**
     * Unregisters all declared navigation menus
     * @return void
     */
    public function unregister_all()
    {
        foreach ($this->navigations as $location => $description) {
            $this->unregister($location);
        }
    }

    /**
     * Returns a human-friendly text from the location data
     * @param  string $location menu location identifier, like a slug
     * @return string           menu location identifier more "human-friendly"
     */
    private function get_description($location)
    {
        // transforms location to a more human-friendly text
        $description = str_replace('_', ' ', $location);
        $description = str_replace('-', ' ', $description);
        $description = ucwords(strtolower($location));

        return $description;
    }

    public function __debugInfo()
    {
        return [
            'namespace' => $this->namespace,
            'navigations' => $this->navigations,
        ];
    }
}
