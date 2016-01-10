<?php

namespace Hwlo\Raccoon;


/*
 * Theme global var, used by this theme scripts
 */
$theme = [
    'namespace' => 'raccoon', // used mainly for translation methods (_e, __, _n, etc.)
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
    'lib/titles.php',
    'lib/posts.php',
    'lib/users.php',
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

/*
 * We load custom post types and custom taxonomies facilities
 */
locate_template('lib/custom-post-type.php', true, true);

/*
 * We declare new custom post types or custom taxonomies
 */
// $book_args = [
//     'post_type_name' => 'book',
//     'singular' => 'Book',
//     'plural' => 'Books',
//     'slug' => 'books',
// ];
// $book_supports = ['title', 'editor', 'thumbnail', 'comments'];
// $books = new CustomPostType($book_args, $book_supports);
// // $books->register_taxonomy('genres');
// $books->register_taxonomy([
//     'taxonomy_name' => 'genre',
//     'singular' => 'Genre',
//     'plural' => 'Genres',
//     'slug' => 'genre',
// ]);
