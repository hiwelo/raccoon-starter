<?php

namespace Hwlo\Raccoon\CustomTypes;


class CustomPostType
{
    /**
     * Post type name
     * @var string
     */
    public $post_type_name;

    /**
     * Post type singular name
     * @var string
     */
    public $singular;

    /**
     * Post type plural name
     * @var string
     */
    public $plural;

    /**
     * Post type slug name
     * @var string
     */
    public $slug;

    /**
     * Post type options
     * @var array
     */
    public $options = [];

    /**
     * Taxonomies associated with this post type
     * @var array
     */
    public $taxonomies = [];

    /**
     * Taxonomy settings
     * @var array
     */
    public $taxonomy_settings = [];

    /**
     * Holds existing taxonomies
     * @var array
     */
    public $existing_taxonomies = [];

    /**
     * Taxonomy filters
     * @var array
     */
    public $filters = [];

    /**
     * Visible columns in admin screen
     * @var array
     */
    public $columns = [];

    /**
     * User functions to populate columns
     * @var array
     */
    public $custom_populate_columns = [];

    /**
     * Define which columns are sortable on the admin screen
     * @var array
     */
    public $sortable = [];

    /**
     * Theme namespace used for l10n
     * @var string
     */
    public $theme_namespace;

    /**
     * Register a new custom post type
     * @param  mixed $post_type_names custom post type names
     * @param  array $options         custom post type options
     * @global array $theme           theme vars
     * @return void
     */
    public function __construct($post_type_names, $options = [])
    {
        // we search the theme namespace
        global $theme;
        $this->theme_namespace = $theme['namespace'];

        // set names to this object
        if (is_array($post_type_names)) {
            $names = array(
                'singular',
                'plural',
                'slug',
            );
            $this->post_type_name = $post_type_names['post_type_name'];

            foreach($names as $name) {
                if (isset($post_type_names[$name])) {
                    $this->$name = $post_type_names[$name];
                } else {
                    $method = 'get_' . $name;
                    $this->$name = $this->$method();
                }
            }
        } else {
            $this->post_type_name = $post_type_names;
            $this->slug = $this->get_slug();
            $this->plural = $this->get_plural();
            $this->singular = $this->get_singular();
        }

        // set asked options to this object
        $this->options = $options;

        // register taxonomies
        $this->add_action('init', array(&$this, 'register_taxonomies'));

        // register post type
        $this->add_action('init', array(&$this, 'register_post_type'));

        // register taxonomies
        $this->add_action('init', array(&$this, 'register_existing_taxonomies'));

        // add taxonomy to admin columns
        $this->add_filter('manage-edit-' . $this->post_type_name . '_columns', array(&$this, 'add_admin_columns'));

        // populate taxonomy column with posts terms
        $this->add_action('manage_' . $this->post_type_name . '_posts_custom_column', array(&$this, 'populate_admin_columns'));

        // add filter in the admin page
        $this->add_action('restrict_manage_posts', array(&$this, 'add_taxonomy_filters'));

        // rewrite post update messages
        $this->add_filter('post_updated_messages', array(&$this, 'updated_messages'));
        $this->add_filter('bulk_post_updated_messages', array(&$this, 'bulk_updated_messages'), 10, 2);
    }

    /**
     * Get a variable
     * @param  string $var asked var name
     * @return mixed       asked var content
     */
    public function get($var)
    {
        if ($this->$var) {
            return $this->$var;
        } else {
            return false;
        }
    }

    /**
     * Set a variable
     * @param  string $var     asked var name
     * @param  mixed  $content asked var content
     * @return void
     */
    public function set($var, $content)
    {
        $reserved = [
            'config',
            'post_type_name',
            'singular',
            'plural',
            'slug',
            'options',
            'taxonomies',
        ];

        if (!in_array($var, $reserved)) {
            $this->$var = $value;
        }
    }

    /**
     * WordPress add_action() helper
     * @param  string  $action        action name
     * @param  string  $function      hooked function
     * @param  integer $priority      priority execution order
     * @param  integer $accepted_args accepted arguments number
     * @return void
     */
    private function add_action($action, $function, $priority = 10, $accepted_args = 1)
    {
        add_action($action, $function, $priority, $accepted_args);
    }

    /**
     * WordPress add_filter() helper
     * @param  string  $filter        filter name
     * @param  string  $function      hooked function
     * @param  integer $priority      priority execution order
     * @param  integer $accepted_args accepted arguments number
     * @return void
     */
    private function add_filter($filter, $function, $priority = 10, $accepted_args = 1)
    {
        add_filter($filter, $function, $priority, $accepted_args);
    }

    /**
     * return a slug (url name)
     * @param  string $name name to transform
     * @return string       result slug
     */
    private function get_slug($name = null)
    {
        if (!isset($name)) {
            $name = $this->post_type_name;
        }

        // a slug is a lowercase name
        $name = strtolower($name);

        // we remove all spaces
        $name = str_replace(' ', '-', $name);

        // we remove underscores
        $name = str_replace('_', '-', $name);

        return $name;
    }

    /**
     * return a plural name
     * @param  string $name name to transform
     * @return string       result plural
     */
    private function get_plural($name = null)
    {
        if (!isset($name)) {
            $name = $this->post_type_name;
        }

        return $this->get_friendly_name($name) . 's';
    }

    /**
     * return a singular name
     * @param  string $name name to transform
     * @return string       result singular
     */
    private function get_singular($name = null)
    {
        if (!isset($name)) {
            $name = $this->post_type_name;
        }

        return $this->get_friendly_name($name);
    }

    /**
     * get a more friendly name
     * @param  string $name name to transform
     * @return string       result name more friendly
     */
    private function get_friendly_name($name)
    {
        if (!isset($name)) {
            $name = $this->post_type_name;
        }

        return ucwords(strtolower(str_replace('-', ' ', str_replace('_', ' ', $name))));
    }

    /**
     * Register a new post type
     * @link   http://codex.wordpress.org/Function_Reference/register_post_type
     * @return void
     */
    public function register_post_type()
    {
        $plural = $this->plural;
        $singular = $this->singular;
        $slug = $this->slug;

        $labels = [
            'name' => sprintf( __('%s', $this->theme_namespace), $plural),
            'singular_name' => sprintf( __('%s', $this->theme_namespace), $singular),
            'menu_name' => sprintf( __('%s', $this->theme_namespace), $plural),
            'all_items' => sprintf( __('%s', $this->theme_namespace), $plural),
            'add_new' => __('Add New', $this->theme_namespace),
            'add_new_item' => sprintf( __('Add New %s', $this->theme_namespace), $singular),
            'edit_item' => sprintf( __('Edit %s', $this->theme_namespace), $singular),
            'new_item' => sprintf( __('New %s', $this->theme_namespace), $singular),
            'view_item' => sprintf( __('View %s', $this->theme_namespace), $singular),
            'search_items' => sprintf( __('Search %s', $this->theme_namespace), $plural),
            'not_found' => sprintf( __('No %s found', $this->theme_namespace), $plural),
            'not_found_in_trash' => sprintf( __('No %s found in Trash', $this->theme_namespace), $plural),
            'parent_item_colon' => sprintf( __('Parent %s:', $this->theme_namespace), $singular),
        ];

        $defaults = [
            'labels' => $labels,
            'public' => true,
            'rewrite' => [
                'slug' => $slug,
            ],
        ];

        $options = array_replace_recursive($defaults, $this->options);
        $this->options = $options;

        if (!post_type_exists($this->post_type_name)) {
            register_post_type($this->post_type_name, $options);
        }
    }

    /**
     * Register a new taxonomy
     * @link   http://codex.wordpress.org/Function_Reference/register_taxonomy
     * @param  mixed $taxonomy_names taxonomy name
     * @param  array $options        taxonomy options
     * @return void
     */
    public function register_taxonomy($taxonomy_names, $options = [])
    {
        $post_type = $this->post_type_name;

        $names = [
            'singular',
            'plural',
            'slug',
        ];

        if (is_array($taxonomy_names)) {
            $taxonomy_name = $taxonomy_names['taxonomy_name'];

            foreach ($names as $name) {
                if (isset($taxonomy_names[$name])) {
                    $$name = $taxonomy_names[$name];
                } else {
                    $method = 'get_' . $name;
                    $$name = $this->$method($taxonomy_name);
                }
            }
        } else {
            $taxonomy_name = $taxonomy_names;
            $singular = $this->get_singular($taxonomy_name);
            $plural = $this->get_plural($taxonomy_name);
            $slug = $this->get_slug($taxonomy_name);
        }

        $labels = [
            'name' => sprintf( __('%s', $this->theme_namespace), $plural),
            'singular_name' => sprintf( __('%s', $this->theme_namespace), $singular),
            'menu_name' => sprintf( __('%s', $this->theme_namespace), $plural),
            'all_items' => sprintf( __('%s', $this->theme_namespace), $plural),
            'edit_item' => sprintf( __('Edit %s', $this->theme_namespace), $singular),
            'view_item' => sprintf( __('View %s', $this->theme_namespace), $singular),
            'update_item' => sprintf( __('Update %s', $this->theme_namespace), $singular),
            'add_new_item' => sprintf( __('Add New %s', $this->theme_namespace), $singular),
            'new_item_name' => sprintf( __('New %s Name', $this->theme_namespace), $plural),
            'parent_item' => sprintf( __('Parent %s', $this->theme_namespace), $plural),
            'parent_item_colon' => sprintf( __('Parent %s:', $this->theme_namespace), $plural),
            'search_items' => sprintf( __('Search %s', $this->theme_namespace), $plural),
            'popular_items' => sprintf( __('Popular %s', $this->theme_namespace), $plural),
            'separate_items_with_commas' => sprintf( __('Separate %s with commas', $this->theme_namespace), $plural),
            'add_or_remove_items' => sprintf( __('Add or remove $s', $this->theme_namespace), $plural),
            'choose_from_most_used' => sprintf( __('Choose from most used %s', $this->theme_namespace), $plural),
            'not_found' => sprintf( __('No %s found', $this->theme_namespace), $plural),
        ];

        // we set taxonomy's options
        $defaults = [
            'labels' => $labels,
            'hierarchical' => true,
            'rewrite' => [
                'slug' => $slug
            ],
        ];
        $options = array_replace_recursive($defaults, $options);

        // we add this taxonomy to the taxonomies list, same for the settings
        $this->taxonomies[] = $taxonomy_name;
        $this->taxonomy_settings[$taxonomy_name] = $options;
    }

    /**
     * Register all custom taxonomies
     * @return void
     */
    function register_taxonomies()
    {
        if (is_array($this->taxonomy_settings)) {
            foreach ($this->taxonomy_settings as $taxonomy_name => $options) {
                if (!taxonomy_exists($taxonomy_name)) {
                    register_taxonomy($taxonomy_name, $this->post_type_name, $options);
                } else {
                    $this->existing_taxonomies[] = $taxonomy_name;
                }
            }
        }
    }

    /**
     * Register all existing taxonomies
     * @return void
     */
    public function register_existing_taxonomies()
    {
        if (is_array($this->existing_taxonomies)) {
            foreach ($this->existing_taxonomies as $taxonomy_name) {
                register_taxonomy_for_object_type($taxonomy_name, $this->post_type_name);
            }
        }
    }

    /**
     * Add admin columns
     * @param  array $columns columns to add in admin dashboard
     * @return array
     */
    public function add_admin_columns($columns)
    {
        if (!isset($this->column)) {
            $new_columns = [];

            if (is_array($this->taxonomies) && is_array('post_tag', $this->taxonomies) || $this->post_type_name === 'post') {
                $after = 'tags';
            } elseif (is_array($this->taxonomies) && in_array('category', $this->taxonomies) || $this->post_type_name === 'post') {
                $after = 'categories';
            } elseif (post_type_supports($this->post_type_name, 'author')) {
                $after = 'author';
            } else {
                $after = 'title';
            }

            foreach ($columns as $key => $title) {
                $new_columns[$key] = $title;

                if ($key === $after) {
                    if (is_array($this->taxonomies)) {
                        foreach ($this->taxonomies as $taxo) {
                            if ($taxo !== 'category' && $taxo !== 'post_tag') {
                                $taxonomy_object = get_taxonomy($taxo);
                                $new_columns[$taxo] = sprintf(__('%s', $this->theme_namespace), $taxonomy_object->labels->name);
                            }
                        }
                    }
                }
            }

            $columns = $new_columns;
        } else {
            $columns = $this->columns;
        }

        return $columns;
    }

    /**
     * Populate admin columns
     * @param  string  $column  name of the column
     * @param  integer $post_id post id
     * @return void
     */
    public function populate_admin_columns($column, $post_id)
    {
        global $post;

        switch ($column) {
            case (taxonomy_exists($column)) :
                $terms = get_the_terms($post_id, $column);

                if (!empty($terms)) {
                    $output = [];

                    foreach ($terms as $term) {
                        $output[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(['post_type' => $post->post_type, $column => $term->slug], 'edit.php')),
                            esc_html(sanitize_term_field('name', $term->name, $term->term_id, $column, 'display'))
                        );
                    }

                    echo join(', ', $output);
                } else {
                    $taxonomy_object = get_taxonomy($column);
                    printf(__('No %s', $this->theme_namespace), $taxonomy_object->labels->name);
                }
                break;

            case (preg_match('/^meta_/', $column) ? true : false) :
                // meta_book_author (meta key = book_author)
                $x = substr($column, 5);
                $meta = get_post_meta($post->ID, $x);
                echo join(', ', $meta);
                break;

            case 'icon':
                $link = esc_url(add_query_arg(['post' => $post->ID, 'action' => 'edit'], 'post.php'));
                if (has_post_thumbnail()) {
                    echo '<a href="' . $link . '">';
                        the_post_thumbnail([60, 60]);
                    echo '</a>';
                } else {
                    echo '<a href="' . $link . '"><img src="' . site_url('/wp-includes/images/crystal/default.png') . '" alt="' . $post->post_title . '" /></a>';
                }
                break;

            default:
                if (isset($this->custom_populate_columns) && is_array($this->custom_populate_columns)) {
                    if (isset($this->custom_populate_columns[$column]) && is_callable($this->custom_populate_columns[$column])) {
                        call_user_func_array($this->custom_populate_columns[$column], array($column, $post));
                    }
                }
                break;
        }
    }

    /**
     * Filters
     * @param  array $filters taxonomy filters to show
     * @return void
     */
    public function filters($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Add taxonomy filters
     * @global string $typenow
     * @global object $wp_query
     * @return void
     */
    public function add_taxonomy_filters()
    {
        global $typenow;
        global $wp_query;

        if ($typenow == $this->post_type_name) {
            if (is_array($this->filters)) {
                $filters = $this->filters;
            } else {
                $filters = $this->taxonomies;
            }

            if (!empty($filters)) {
                foreach ($filters as $tax_slug) {
                    $taxo = get_taxonomy($tax_slug);
                    $args = [
                        'orderby' => 'name',
                        'hide_empty' => false
                    ];
                    $terms = get_terms($tax_slug, $args);

                    if ($terms) {
                        printf('&nbsp;<select name="%s" class="postform">', $tax_slug);
                        printf('<option value="0">%s</option>', sprintf(__('Show all %s', $this->theme_namespace), $taxo->label));

                        foreach ($terms as $term) {
                            if (isset($_GET[$tax_slug]) && $_GET[$tax_slug] === $term->slug) {
                                printf('<option value="%s" selected>%s (%s)</option>', $term->slug, $term->name, $term->count);
                            } else {
                                printf('<option value="%s">%s (%s)</option>', $term->slug, $term->name, $term->count);
                            }
                        }

                        print('</select>&nbsp;');
                    }
                }
            }
        }
    }

    /**
     * Choose columns to display in admin dashboard
     * @param  array $columns columns list
     * @return void
     */
    public function columns($columns)
    {
        if (isset($columns)) {
            $this->columns = $columns;
        }
    }

    /**
     * Populate column
     * @param  string $column_name name of the column to populate
     * @param  mixed  $callback    anonymous function or callable array to call
     * @return void
     */
    public function populate_column($column_name, $callback)
    {
        $this->custom_populate_column[$column_name] = $callback;
    }

    /**
     * define which columns is sortable
     * @param  array $columns sortable columns list
     * @return void
     */
    public function sortable($columns = [])
    {
        $this->sortable = $columns;
        $this->add_filter('manage_edit-' . $this->post_type_name . '_sortable_columns', [&$this, 'make_columns_sortable']);
        $this->add_action('load-edit.php', [&$this, 'load_edit']);
    }

    /**
     * make columns sortable
     * @param  array $columns columns which have to be sortable
     * @return array
     */
    public function make_column_sortable($columns)
    {
        foreach ($this->sortable as $column => $values) {
            $sortable_columns[$column] = $values[0];
        }

        $columns = array_merge($sortable_columns, $columns);
        return $columns;
    }

    /**
     * sort columns only on edit.php page when requested
     * @link   http://codex.wordpress.org/Plugin_API/Filter_Reference/request
     * @return void
     */
    public function load_edit()
    {
        $this->add_filter('request', [&$this, 'sort_columns']);
    }

    /**
     * internal function which sorts columns on request
     * @see    load_edit()
     * @param  array $vars query vars submitted by user
     * @return array       sorted array
     */
    private function sort_columns($vars)
    {
        foreach ($this->sortable as $column => $values) {
            $meta_key = $values[0];

            if (taxonomy_exists($meta_key)) {
                $key = 'taxonomy';
            } else {
                $key = 'meta_key';
            }

            if (isset($values[1]) && true === $values[1]) {
                $orderby = 'meta_value_num';
            } else {
                $orderby = 'meta_value';
            }

            if (isset($vars['post_type']) && $this->post_type_name == $vars['post_type']) {
                if (isset($vars['orderby']) && $meta_key == $vars['orderby']) {
                    $vars = array_merge($vars, ['meta_key' => $meta_key, 'orderby' => $orderby]);
                }
            }
        }

        return $vars;
    }

    /**
     * set menu icon
     * @link   http://melchoyce.github.io/dashicons/
     * @param  string $icon dashicon name
     * @return void
     */
    private function menu_icon($icon = 'dashicons-admin-page')
    {
        if (is_string($icon) && stripos($icon, "dashicons") !== false) {
            $this->options['menu_icon'] = $icon;
        } else {
            $this->options['menu_icon'] = 'dashicons-admin-page';
        }
    }

    /**
     * Set theme namespace, used for l10n
     * @param string $namespace theme namespace used for l10n
     * @return void
     */
    public function set_namespace($namespace)
    {
        $this->theme_namespace = $namespace;
    }

    /**
     * updated messages
     * @param  array $messages post updated messages
     * @return array
     */
    public function updated_messages($messages)
    {
        $post = get_post();
        $singular = $this->singular;

        $messages[$this->post_type_name] = [
            0 => '',
            1 => sprintf(__('%s updated.', $this->theme_namespace), $singular),
            2 => __('Custom field updated.', $this->theme_namespace),
            3 => __('Custom field deleted.', $this->theme_namespace),
            4 => sprintf(__('%s updated.', $this->theme_namespace), $singular),
            5 => isset($_GET['revision']) ? sprintf(__('%2$s restored to revision from %1$s', $this->theme_namespace), wp_post_revision_title((int) $_GET['revision'], false), $singular) : false,
            6 => sprintf(__('%s updated.', $this->theme_namespace), $singular),
            7 => sprintf(__('%s saved.', $this->theme_namespace), $singular),
            8 => sprintf(__('%s submitted.', $this->theme_namespace), $singular),
            9 => sprintf(
                __('%2$s scheduled for: <strong>%1$s</strong>', $this->theme_namespace),
                date_i18n(__('M j, Y @ G:i', $this->theme_namespace), strtotime($post->post_date)),
                $singular
            ),
            10 => sprintf(__('%s draft updated.', $this->theme_namespace), $singular),
        ];

        return $messages;
    }

    /**
     * bulk updated messages
     * @param  array   $bulk_messages array of bulk updated messages
     * @param  integer $bulk_counts   number of elements
     * @return array
     */
    public function bulk_updated_messages($bulk_messages, $bulk_counts)
    {
        $singular = $this->singular;
        $plural = $this->plural;

        $bulk_messages[$this->post_type_name] = [
            'updated' => _n('%s ' . $singular . ' updated.', '%s ' . $plural . ' updated.', $bulk_counts['updated']),
            'locked' => _n('%s ' . $singular . ' not updated, somebody is editing it.', '%s ' . $plural . ' not updated, somebody is editing them.', $bulk_counts['loacked']),
            'deleted' => _n('%s ' . $singular . ' permanently deleted.', '%s ' . $plural . ' permanently deleted.', $bulk_counts['deleted']),
            'trashed' => _n('%s ' . $singular . ' moved to the Trash.', '%s ' . $plural . ' moved to the Trash', $bulk_counts['trashed']),
            'untrashed' => _n('%s ' . $singular . ' restored from the Trash.', '%s ' . $plural . ' restored from the Trash', $bulk_counts['untrashed']),
        ];

        return $bulk_messages;
    }

    /**
     * flush rewrite rules programatically
     * @return void
     */
    public function flush()
    {
        flush_rewrite_rules();
    }
}
