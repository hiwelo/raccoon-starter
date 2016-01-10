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

$class = __NAMESPACE__ . '\\Extras\Extras';

// this function add <body> some classes
add_filter('body_class', [$class, 'body_class']);

// change the excerpt read more message
add_filter('excerpt_more', [$class, 'excerpt_more']);
