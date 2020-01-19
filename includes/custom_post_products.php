<?php

function products_post_type()
{

    $labels = array(
        'name' => _x('Products', 'Post Type General Name', 'rain_agency'),
        'singular_name' => _x('Product', 'Post Type Singular Name', 'rain_agency'),
        'menu_name' => __('Products', 'rain_agency'),
        'name_admin_bar' => __('Product', 'rain_agency'),
        'archives' => __('Item Archives', 'rain_agency'),
        'parent_item_colon' => __('Parent Product:', 'rain_agency'),
        'all_items' => __('All Products', 'rain_agency'),
        'add_new_item' => __('Add New Product', 'rain_agency'),
        'add_new' => __('New Product', 'rain_agency'),
        'new_item' => __('New Item', 'rain_agency'),
        'edit_item' => __('Edit Product', 'rain_agency'),
        'update_item' => __('Update Product', 'rain_agency'),
        'view_item' => __('View Product', 'rain_agency'),
        'search_items' => __('Search products', 'rain_agency'),
        'not_found' => __('No products found', 'rain_agency'),
        'not_found_in_trash' => __('No products found in Trash', 'rain_agency'),
        'featured_image' => __('Featured Image', 'rain_agency'),
        'set_featured_image' => __('Set featured image', 'rain_agency'),
        'remove_featured_image' => __('Remove featured image', 'rain_agency'),
        'use_featured_image' => __('Use as featured image', 'rain_agency'),
        'insert_into_item' => __('Insert into item', 'rain_agency'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'rain_agency'),
        'items_list' => __('Items list', 'rain_agency'),
        'items_list_navigation' => __('Items list navigation', 'rain_agency'),
        'filter_items_list' => __('Filter items list', 'rain_agency'),
    );
    $args = array(
        'label' => __('Product', 'rain_agency'),
        'description' => __('Product information pages.', 'rain_agency'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'revisions',),
        'hierarchical' => true,
        'rewrite' => array('slug' => '%brand_slug%/%product_slug%', 'with_front' => false),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-products',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    register_post_type('products', $args);
    add_rewrite_rule("^brand-([^/]+)/product-([^/]+)/([^/]+)?",'index.php?post_type=products&brand_slug=$matches[1]&product_slug=$matches[2]&products=$matches[3]','top');
}