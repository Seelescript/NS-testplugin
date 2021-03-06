<?php
/*
Plugin Name: Nicasource Products Plugin
Plugin URI: https://nicasource.com/
Version: 1
Author: Carlos Hernandez
Description: Add Custom post Type Product and Brands
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}
 
include_once 'includes/taxonomy_product_category.php';
include_once 'includes/custom_post_brands.php';
include_once 'includes/custom_post_products.php';
include_once 'includes/ns_api_endpoint.php';
include_once 'includes/infinite_scroll.php';

register_activation_hook( __FILE__, 'nicasource_activate' );

function nicasource_activate(){
    flush_rewrite_rules();
}

function start_plugin(){
    products_post_type();
    brands_post_type();
    product_category_taxonomy();
    nicasource_api_endpoints();
}


add_action( 'init', 'start_plugin', 0 );