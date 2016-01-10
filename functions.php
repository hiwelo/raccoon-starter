<?php

namespace Hwlo\Raccoon;


/*
 * Theme global var, used by this theme scripts
 */
$theme = [
    'namespace' => 'raccoon', // used mainly for translation methods (_e, __, etc.)
];

/**
 * PHP librairies to include in this file
 * @var array
 */
$libraries = [
    'lib/assets.php',
    'lib/cleanup.php',
    'lib/setup.php',
    'lib/extras.php',
    'lib/jetpack.php',
    'lib/template.php',
    'lib/titles.php',
];

/*
 * We load the core system and the called libraries through it
 */
locate_template('lib/core.php', true, true);
Core::load_libraries($libraries);

/*
 * We load actions & filters calls
 */
locate_template('lib/_actions.php', true, true);
locate_template('lib/_filters.php', true, true);
