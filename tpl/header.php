<?php
/**
 * Global header file
 *
 * PHP version 5
 *
 * @category Theme
 * @package  Raccoon
 * @author   Damien Senger <damien@alsacreations.fr>
 * @license  ./readme.md#license see this file
 * @link     https://codex.wordpress.org/Template_Hierarchy
 */

// navigation arguments
$navigation = [
    'theme_location' => 'primary',
    'container' => 'nav',
    'container_class' => 'header__menu'
];
?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <?php wp_head(); ?>
    <link rel="stylesheet" media="all" href="<?php echo ModernWeb::stylesheet(); ?>">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header__branding">
                <a class="unstyled" href="<?php bloginfo('url'); ?>">Lewebmodern</a>
            </div><!-- .header__branding -->
            <?php wp_nav_menu($navigation); ?>
        </div><!-- .container -->
    </header><!-- .header -->
