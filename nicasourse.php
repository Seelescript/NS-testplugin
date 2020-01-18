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

register_activation_hook( __FILE__, 'nicasource_activate' );

function nicasource_activate(){
    flush_rewrite_rules();
}

function start_plugin(){
}


add_action( 'init', 'start_plugin', 0 );