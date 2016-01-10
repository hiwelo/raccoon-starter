<?php

namespace Hwlo\Raccoon;

/* -----------------
 * Action hooks file
 * -----------------
 * This file contains all hooks which add a specific action to a WordPress function
 */


/* -------------
 * Setup actions
 * ------------- */

$className = __NAMESPACE__ . '\\Setup\Setup';
$class = new $className();

// this function run all theme setup instruction
add_action('after_setup_theme', array($class, 'init'));


/* ---------------
 * Cleanup actions
 * --------------- */

$className = __NAMESPACE__ . '\\CleanUp\CleanUp';
$class = new $className();

// this function cleans basic WordPress theme components
add_action('after_setup_theme', array($class, 'init'));
