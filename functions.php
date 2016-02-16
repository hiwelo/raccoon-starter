<?php
/**
 * Raccoon methods init file
 *
 * PHP version 5
 *
 * @category Theme
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     https://codex.wordpress.org/Functions_File_Explained
 */

// vendors & classes autoloading
include 'vendor/autoload.php';

// use Raccoon theme methods
use Hiwelo\Raccoon\Theme\Helpers;

// setup theme helpers
$helpers = new Helpers();
