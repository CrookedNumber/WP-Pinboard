<?php
/*
Plugin Name: Pinboard
Plugin URI: TBD
Version: 1.0
Author: David Moore
Author URI: http://CrookedNumber.com
License: GPLv2
*/

require_once(plugin_dir_path( __FILE__ ) . '/classes/pinboard_most_recent.class.php');
require_once(plugin_dir_path( __FILE__ ) . '/classes/pinboard_tags.class.php');

add_action( 'widgets_init', 'pinboard_register_widgets' );

function pinboard_register_widgets() {
    register_widget( 'pinboard_most_recent_widget' );
    register_widget( 'pinboard_tags_widget' );
}

function pinboard_api_call($method, $params) {
    $params['format'] = 'json';
    $url = 'https://api.pinboard.in/v1/' . $method . '?' . http_build_query($params);

    $args = array(
        'headers' => array( 'Content-type' => 'application/json' )
    );
 
    $response = wp_remote_get( $url, $args );
    $body = wp_remote_retrieve_body( $response );
    return json_decode($body);
}

function pinboard_extract_user($token) {
    $pieces = explode(':', $token);
    return array_shift($pieces);
}