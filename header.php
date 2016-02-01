<?php
/**
 * Header template file
 *
 * PHP version 5
 *
 * @category Theme
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     https://codex.wordpress.org/Template_Hierarchy
 */

use \Hwlo\Raccoon\Tools;

?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="page">
        <header class="banner">
            <div class="banner__branding">
                <?php if (is_front_page() && is_home()) { ?>
                    <h1 class="banner__title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1><!-- .banner__title -->
                <?php } else { ?>
                    <p class="banner__title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <?php bloginfo('name'); ?>
                        </a>
                    </p><!-- .banner__title -->
                <?php } ?>
            </div><!-- .banner__branding -->
            <?php /*$navigations->menu('primary');*/ ?>
        </header><!-- .banner -->

        <main class="content">
