<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Logout via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_logout' ) ) {

    add_action('wp_ajax_logout_action', 'uix_usercenter_ajax_logout'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_logout_action', 'uix_usercenter_ajax_logout');  // for non-logged in users

    function uix_usercenter_ajax_logout(){

        $data_query = uix_usercenter_token_auth();
        if ( isset( $data_query -> data ) ) {
        
            wp_destroy_current_session();
            wp_clear_auth_cookie();
            wp_set_current_user( 0 );
    

            wp_send_json(array(
                'status' => true, 
                'message'  => 'logout_ok',
            ));

        } else {
            wp_send_json($data_query);
        }

        die();
    }
}

