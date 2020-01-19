<?php
// Register Custom Post Type brands
function brands_post_type() {

    $labels = array(
        'name'                  => _x( 'Brands', 'Post Type General Name', 'NicaSource' ),
        'singular_name'         => _x( 'Brand', 'Post Type Singular Name', 'NicaSource' ),
        'menu_name'             => __( 'Brands', 'NicaSource' ),
        'name_admin_bar'        => __( 'Brand', 'NicaSource' ),
        'archives'              => __( 'Item Archives', 'NicaSource' ),
        'parent_item_colon'     => __( 'Parent Brand:', 'NicaSource' ),
        'all_items'             => __( 'All Brands', 'NicaSource' ),
        'add_new_item'          => __( 'Add New Brand', 'NicaSource' ),
        'add_new'               => __( 'Add New Brand', 'NicaSource' ),
        'new_item'              => __( 'New Brand', 'NicaSource' ),
        'edit_item'             => __( 'Edit Brand', 'NicaSource' ),
        'update_item'           => __( 'Update Brand', 'NicaSource' ),
        'view_item'             => __( 'View Brand', 'NicaSource' ),
        'search_items'          => __( 'Search Brand', 'NicaSource' ),
        'not_found'             => __( 'Not found', 'NicaSource' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'NicaSource' ),
        'featured_image'        => __( 'Featured Image', 'NicaSource' ),
        'set_featured_image'    => __( 'Set featured image', 'NicaSource' ),
        'remove_featured_image' => __( 'Remove featured image', 'NicaSource' ),
        'use_featured_image'    => __( 'Use as featured image', 'NicaSource' ),
        'insert_into_item'      => __( 'Insert into Brand', 'NicaSource' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Brand', 'NicaSource' ),
        'items_list'            => __( 'Items list', 'NicaSource' ),
        'items_list_navigation' => __( 'Items list navigation', 'NicaSource' ),
        'filter_items_list'     => __( 'Filter items list', 'NicaSource' ),
    );
    $args = array(
        'label'                 => __( 'Brand', 'NicaSource' ),
        'description'           => __( 'Brands', 'NicaSource' ),
        'labels'                => $labels,
        // keep 'page-attributes' in the array support and hierarchical true for hierarchical post type
        'supports'              => array( 'title', 'editor', 'thumbnail', 'page-attributes'),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-screenoptions',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'rewrite'               =>  array('slug' => 'brands', 'with_front' => false),
    );
    register_post_type( 'brands', $args );
    add_rewrite_rule( 
        '^brands/([^/]+)/?$',
        'index.php?post_type=brands&name=$matches[1]',
        'top'
    );

}