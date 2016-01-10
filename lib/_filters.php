<?php

namespace Hwlo\Raccoon;

/* -----------------
 * Filter hooks file
 * -----------------
 * This file contains all hooks which add a specific filter to a WordPress function
 */


/* -------------
 * Extra filters
 * ------------- */

$className = __NAMESPACE__ . '\\Extras\Extras';
$class = new $className();

// this function add <body> some classes
add_filter('body_class', array($class, 'body_class'));

// change the excerpt read more message
add_filter('excerpt_more', array($class, 'excerpt_more'));
