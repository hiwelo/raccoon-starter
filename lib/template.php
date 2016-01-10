<?php

namespace Hwlo\Raccoon\Template;


/**
 * Theme template wrapper
 * @link https://roots.io/sage/docs/theme-wrapper/
 */

function template_path() {
    return Template::$template;
}

function sidebar_path() {
    return new Template('templates/sidebar.php');
}

class Template
{
    /**
     * Keep the full path of the main template file
     * @var string
     */
    public static $template;

    /**
     * Basename of a template file
     * @var string
     */
    public $slug;

    /**
     * List of templates
     * @var array
     */
    public $templates = [];

    /**
     * Empty constructor for WordPress add_action or add_filter
     * @return object
     */
    public function __construct($template = 'base.php')
    {
        $this->slug = basename($template, '.php');
        $this->templates = [$template];

        if (self::$base) {
            $str = substr($template, 0, -4);
            array_unshift($this->templates, sprintf($str, '-%s.php', self::$base));
        }
    }

    /**
     * Return a template path
     * @return string
     */
    public function _toString()
    {
        global $theme;
        $this->templates = apply_filter($theme['namespace'] . '/wrap_' . $this->slug, $this->templates);
        return locate_template($this->templates);
    }

    public static function wrap($main)
    {
        // check for other filters returning null
        if (!is_string($main)) {
            return $main;
        }

        self::$template = $main;
        self::$base = basename(self::$template, '.php');

        if (self::$base === 'index') {
            self::$base = false;
        }

        return new Template();
    }
}
