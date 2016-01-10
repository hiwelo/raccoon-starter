<?php
/**
 * PHP librairies to include in this file
 * @var array
 */
$includes = [
    'lib/setup.php',
];

// called librairies inclusion
foreach ($includes as $file) {
    locate_template($file, true, true);
}

// wordpress theme setup
add_action('after_setup_theme', Hwlo\Raccoon\Setup\\setup);
