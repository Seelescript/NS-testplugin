<?php

// Register Custom Taxonomy
function product_category_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Product Categories', 'Taxonomy General Name', 'NicaSource' ),
        'singular_name'              => _x( 'Product Category', 'Taxonomy Singular Name', 'NicaSource' ),
        'menu_name'                  => __( 'Product Category', 'NicaSource' ),
        'all_items'                  => __( 'All Items', 'NicaSource' ),
        'parent_item'                => __( 'Parent Item', 'NicaSource' ),
        'parent_item_colon'          => __( 'Parent Item:', 'NicaSource' ),
        'new_item_name'              => __( 'New Item Name', 'NicaSource' ),
        'add_new_item'               => __( 'Add New Item', 'NicaSource' ),
        'edit_item'                  => __( 'Edit Item', 'NicaSource' ),
        'update_item'                => __( 'Update Item', 'NicaSource' ),
        'view_item'                  => __( 'View Item', 'NicaSource' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'NicaSource' ),
        'add_or_remove_items'        => __( 'Add or remove items', 'NicaSource' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'NicaSource' ),
        'popular_items'              => __( 'Popular Items', 'NicaSource' ),
        'search_items'               => __( 'Search Items', 'NicaSource' ),
        'not_found'                  => __( 'Not Found', 'NicaSource' ),
        'no_terms'                   => __( 'No items', 'NicaSource' ),
        'items_list'                 => __( 'Items list', 'NicaSource' ),
        'items_list_navigation'      => __( 'Items list navigation', 'NicaSource' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => array( 'slug' => 'product' ),
    );
    register_taxonomy( 'product_category', array( 'products' ), $args );

}