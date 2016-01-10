<?php

namespace Hwlo\Raccoon;


class Core
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
     * Load all asked librairies
     * @param  array $libraries asked libraries list
     * @return void
     * @static
     */
    static function load_libraries($libraries)
    {
        foreach ($libraries as $library) {
            self::load_library($library);
        }
    }

    /**
     * Load asked library
     * @param  string $library asked library to load
     * @return void
     */
    function load_library($library)
    {
        $file = get_template_directory() . '/' . $library;
        if (file_exists($file)) {
            locate_template($library, true, true);
        }
    }

    /**
     * CSS injection in HTML HEAD
     * @param  array  $element targeted DOM element
     * @param  array  $css     CSS list
     * @return void
     */
    static function injectStyle(array $element, array $css)
    {
        $element = implode('__', $element);
        $output_begin = '<style>.' . $element . ' { ';
        $output_content = [];
        $output_end = ' } </style>';

        foreach ($css as $attr => $value) {
            $output_content[] = $attr . ': ' . $value . ';';
        }

        $output = $output_begin . implode(' ', $output_content) . $output_end;
        echo $output;
    }

    /**
     * HTML element injection in HTML DOM
     * @param  string $type    HTML element to inject
     * @param  array  $element HTML element class (row1__row2__...)
     * @param  string $content HTML element content
     * @return void
     */
    static function injectDOM($type, array $element, $content = '')
    {
        if ($element[0] === true) {
            unset($element[0]);
            $classes = '';
            foreach ($element as $class) {
                $classes .= implode('__', $class) . ' ';
            }
        } else {
            $classes = implode('__', $element);
        }

        echo '<'.$type.' class="' . $classes . '">' . $content . '</' . $type . '>';
    }

    /**
     * JS scripts injection in HTML DOM
     * @param  string $file JS scripts list file
     * @return void
     */
    static function injectScripts($file = 'scripts.json')
    {
        $file = file_get_contents(locate_template($file));
        $json = json_decode($file);
        foreach ($json->scripts as $script) {
            $url = get_bloginfo('template_url') . '/' . $script;
            $output .= '<script src="' . $url . '"></script>';
        }

        echo $output;
    }
}
