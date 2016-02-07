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
use Hwlo\Raccoon\Raccoon;

/*
 * PSR-4 autoloading system
 */
require 'vendor/autoload.php';

/*
 * Raccoon WordPress theme setup
 */
// Core::setup();
$raccoon = new Raccoon();

var_dump($raccoon);
