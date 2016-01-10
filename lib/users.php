<?php

namespace Hwlo\Raccoon;

class Users
{
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address
     * @see    http://gravatar.com/site/implement/images/php/
     * @param  string  $email the email address
     * @param  integer $s     size in pixels, defaults to 80px [1 - 2 048px]
     * @param  string  $d     default imageset to use [404 | mm | identicon | monsterid | wavatar]
     * @param  string  $r     maximum rating (inclusive) [g | pg | r | x ]
     * @param  bool    $img   true to return a complete IMG tag, false for just the URL
     * @param  array   $atts  optional, additional key/value attributes to include in the IMG tag
     * @return string         containing either just a URL or a complete image tag
     * @static
     */
    public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = [])
    {
        $url = 'https://gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?s=' . $s . '&d=' . $d . '&r=' . $r;
        if ($img) {
            $url = '<img src="' . $url . '" alt=""';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }

        return $url;
    }
}
