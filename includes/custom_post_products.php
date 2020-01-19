<?php

function products_post_type()
{

    $labels = array(
        'name' => _x('Products', 'Post Type General Name', 'NicaSource'),
        'singular_name' => _x('Product', 'Post Type Singular Name', 'NicaSource'),
        'menu_name' => __('Products', 'NicaSource'),
        'name_admin_bar' => __('Product', 'NicaSource'),
        'archives' => __('Item Archives', 'NicaSource'),
        'parent_item_colon' => __('Parent Product:', 'NicaSource'),
        'all_items' => __('All Products', 'NicaSource'),
        'add_new_item' => __('Add New Product', 'NicaSource'),
        'add_new' => __('New Product', 'NicaSource'),
        'new_item' => __('New Item', 'NicaSource'),
        'edit_item' => __('Edit Product', 'NicaSource'),
        'update_item' => __('Update Product', 'NicaSource'),
        'view_item' => __('View Product', 'NicaSource'),
        'search_items' => __('Search products', 'NicaSource'),
        'not_found' => __('No products found', 'NicaSource'),
        'not_found_in_trash' => __('No products found in Trash', 'NicaSource'),
        'featured_image' => __('Featured Image', 'NicaSource'),
        'set_featured_image' => __('Set featured image', 'NicaSource'),
        'remove_featured_image' => __('Remove featured image', 'NicaSource'),
        'use_featured_image' => __('Use as featured image', 'NicaSource'),
        'insert_into_item' => __('Insert into item', 'NicaSource'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'NicaSource'),
        'items_list' => __('Items list', 'NicaSource'),
        'items_list_navigation' => __('Items list navigation', 'NicaSource'),
        'filter_items_list' => __('Filter items list', 'NicaSource'),
    );
    $args = array(
        'label' => __('Product', 'NicaSource'),
        'description' => __('Product information pages.', 'NicaSource'),
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
    flush_rewrite_rules();
}

function my_post_type_link_filter_function($post_link, $id = 0, $leavename = FALSE) {
    //filter to replace the strings for values stored in the metadata of the post
    if (strpos('%brand_slug%', $post_link) === FALSE && strpos('%product_slug%', $post_link) === FALSE ) {
        $post = &get_post($id);
        $brand_name = get_post(get_post_meta($post->ID,'brand',true));
        $bran_slug = $brand_name->post_name;
        $name= $post->post_name;
        if(empty($bran_slug)){$bran_slug = 'default';} //use default as brand if no brand register
        $post_link = str_replace('%brand_slug%',$bran_slug, $post_link);
        $post_link = str_replace('%product_slug%',$name, $post_link);
        return $post_link;
    }
}
add_filter('post_type_link', 'my_post_type_link_filter_function', 10, 2);