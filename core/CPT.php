<?php

namespace qtwp\core;

defined( 'ABSPATH' ) or exit;

use Carbon_Fields\Container as Container;

abstract class CPT extends Carbon {
    static $count = 4;
    public function __construct() {
        $this->register();
        add_action( 'carbon_fields_register_fields', [$this, 'register_meta'] );
    }

    abstract public function register_meta();
    abstract public function register();

    /**
     * Creates a custom post type
     *
     * @param string $singular
     * @param string $plural
     * @param string $taxanomy
     * @param string $icon
     * @return void
     */
    public function create_post_type( $singular, $plural, $taxanomy = '', $icon = 'dashicons-list-view' ) {
        $title_singular = ucfirst( $singular );
        $title_plural   = ucfirst( $plural );
        $name_singular  = strtolower( $singular );
        $name_plural    = strtolower( $plural );

        return register_post_type(
            $name_singular,
            [
                'labels'            => [
                    'name'               => __( $title_plural ),
                    'singular_name'      => __( $title_singular ),
                    'add_new'            => __( sprintf( 'New %s', $name_singular ) ),
                    'add_new_item'       => __( sprintf( 'Add new %s', $name_singular ) ),
                    'edit_item'          => __( sprintf( 'Edit %s', $name_singular ) ),
                    'new_item '          => __( sprintf( 'New %s', $name_singular ) ),
                    'view_item'          => __( sprintf( 'View %s', $name_singular ) ),
                    'view_items'         => __( sprintf( 'View %s', $name_plural ) ),
                    'search_items'       => __( sprintf( 'Search %s', $name_plural ) ),
                    'not_found'          => __( sprintf( 'No %s found.', $name_singular ) ),
                    'not_found_in_trash' => __( sprintf( 'No %s found in trash.', $name_singular ) ),
                    'parent_item_colon'  => __( sprintf( 'Parent %s: ', $name_singular ) ),
                    'all_items'          => __( sprintf( 'All %s', $name_plural ) ),
                    'featured_image'     => __( sprintf( '%s thumbnail', $title_singular ) ),
                    'set_featured_image' => __( sprintf( 'Set %s thumbnail', $name_singular ) ),
                ],
                'public'            => true,
                'hierarchical'      => true,
                'has_archive'       => true,
                'rewrite'           => ['slug' => $name_singular],
                'taxonomies'        => is_array( $taxanomy ) ? $taxanomy : [$taxanomy],
                'supports'          => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes'],
                'menu_icon'         => $icon,
                'menu_position'     => 4,
                'show_in_nav_menus' => true,
                'show_in_rest'      => true,
            ]
        );
    }

    /**
     * Creates a category | can be included with existing posts or custom post types
     *
     * @param string $taxanomy
     * @param string $object
     * @param string $title
     * @return void
     */
    public function create_category( $taxanomy, $object, $title ) {
        $title_singular = ucfirst( $title );
        $title_plural   = ucfirst( $taxanomy );
        $name_singular  = strtolower( $taxanomy );
        $name_plural    = strtolower( $title );

        return register_taxonomy( $name_singular, $object, [
            'hierarchical'      => true,
            'labels'            => [
                'name'              => _x( sprintf( '%s categories', $title_singular ), 'taxonomy general name' ),
                'singular_name'     => _x( sprintf( '%s category', $title_singular ), 'taxonomy singular name' ),
                'search_items'      => __( 'Search Categories' ),
                'all_items'         => __( 'All Categories' ),
                'parent_item'       => __( 'Parent Category' ),
                'parent_item_colon' => __( 'Parent Category:' ),
                'edit_item'         => __( 'Edit Category' ),
                'update_item'       => __( 'Update Category' ),
                'add_new_item'      => __( 'Add New Category' ),
                'new_item_name'     => __( sprintf( 'New %s Category', $title_singular ) ),
                'menu_name'         => __( 'Category' ),
            ],
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => $name_singular],
        ] );
    }

    /**
     * Add meta fields | can be included with existing posts or custom post types
     *
     * @param string $table_title
     * @param string $post_type
     * @param array $metas
     * @param string $context
     * @return void
     */
    public function add_meta( $table_title, $post_type = 'post', $metas = [], $context = 'side' ) {
        return Container::make( 'post_meta', __( $table_title ) )
            ->where( 'post_type', '=', $post_type )
            ->add_fields( $metas )
            ->set_context( $context );
    }
}
