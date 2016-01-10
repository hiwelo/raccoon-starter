<?php

namespace Hwlo\Raccoon\Setup;


function setup() {
    /**
     * Enable plugins to manage the document title
     * @see http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnails
     * @see http://codex.wordpress.org/Post_Thumbnails
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable post formats
     * @see http://codex.wordpress.org/Post_Formats
     */
    $postFormats = ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'];
    add_theme_support('post-formats', $postFormats);

    /**
     * Enable HTML5 markup support
     * @see http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
     */
    $html5Support = ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form'];
    add_theme_support('html5', $html5Support);

    /**
     * Use main stylesheet for visual editor
     * to add custom styles, edit assets/src/css/_tinymce.less
     */
    add_editor_style(Assets\asset_path('dist/css/styles.css'));
}
