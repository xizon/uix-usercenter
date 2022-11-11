<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Create nonce via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_createnonce' ) ) {

    add_action('wp_ajax_createnonce_action', 'uix_usercenter_ajax_createnonce'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_createnonce_action', 'uix_usercenter_ajax_createnonce');  // for non-logged in users
    function uix_usercenter_ajax_createnonce(){

        // only POST requests
        if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) die();

        $nonce_name = sanitize_text_field($_POST['nonce_name']);

        wp_send_json(array(
            'form_nonce' => wp_create_nonce( $nonce_name )
        ));

        die();
    }
}

