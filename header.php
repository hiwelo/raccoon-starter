<?php
/**
 * Theme's header
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package raccoon
 */
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                    </h1><!-- .banner__title -->
                <?php } else { ?>
                    <p class="banner__title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                    </p><!-- .banner__title -->
                <?php } ?>
            </div><!-- .banner__branding -->
        </header><!-- .banner -->

        <main class="content">
