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

$class = __NAMESPACE__ . '\\Setup\Setup';

// this function run all theme setup instruction
add_action('after_setup_theme', $class . '::init');


/* ---------------
 * Cleanup actions
 * --------------- */

$class = __NAMESPACE__ . '\\CleanUp\CleanUp';

// this function cleans basic WordPress theme components
add_action('after_setup_theme', $class . '::init');


/* ---------------
 * Jetpack actions
 * --------------- */

$class = __NAMESPACE__ . '\\Jetpack\Jetpack';

// this function cleans basic WordPress theme components
add_action('after_setup_theme', $class . '::setup');
