<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Login via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_login' ) ) {

    add_action('wp_ajax_login_action', 'uix_usercenter_ajax_login'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_login_action', 'uix_usercenter_ajax_login');  // for non-logged in users
    function uix_usercenter_ajax_login(){

        
        // First check the nonce, if it fails the function will break
        check_ajax_referer( 'ajax-login-nonce', 'security' );

        
        // Nonce is checked, get the POST data
        $user = sanitize_user(wp_unslash($_POST['user']));
        $pass = sanitize_text_field($_POST['password']);
        $remember = isset( $_POST['rememberme'] ) ? sanitize_text_field( $_POST[ 'rememberme' ] ) : true;
 
        if ( gettype($remember) == 'string' && $remember !== 'false' && $remember !== 'true' ) $remember = true;

        $info = array(
            'user_login'  =>  $user,
            'user_password' => $pass,
            'remember' => filter_var($remember, FILTER_VALIDATE_BOOLEAN)
        );
        

        /* Check Captcha */
        $captcha_check = uix_usercenter_captcha_check($_POST[ 'captcha' ]);
        if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ) {
            $captcha_check['status'] = true; // disable captcha
        }
        if ( $captcha_check['status'] === true ) {
           /* Check User */

            // If `$secure_cookie` do not use true, `https` cannot directly authorize the management page under AJAX requests
            // If the user wants SSL but the session is not SSL, force a secure cookie.
            $secure_cookie   = '';
            if ( ! force_ssl_admin() ) {
                $user_query_first = get_user_by( 'login', $user );
            
                if ( ! $user_query_first && strpos( $user, '@' ) ) {
                    $user_query_first = get_user_by( 'email', $user );
                }
            
                if ( $user_query_first ) {
                    if ( get_user_option( 'use_ssl', $user_query_first->ID ) ) {
                        $secure_cookie = true;
                        force_ssl_admin( true );
                    }
                }
            }
        
            $user_signon = wp_signon( $info, $secure_cookie );
            if ( is_wp_error($user_signon) ){
                wp_send_json(array(
                    'status' => false, 
                    'message'  => 'name_mail_pass_invalid'
                ));
            } else {


                // Login successful
                $user = $info['user_login'];

                if ( is_email( $user ) ) {
                    $user_query = get_user_by( 'email', $user );
                } else {
                    $user_query = get_user_by( 'login', $user );
                }

                // Generate JWT tokens
                $apikey = md5( 'userapikey' . $user_query->ID );
                $serverKey = md5( 'userapikey-token-key' ); // Get our server-side secret key from a secure location.
                // $nbf = strtotime('2021-01-01 00:00:01');  // enable the "not before" feature.
                // $exp = strtotime('2021-01-01 00:00:01'); // enable the "expire" feature.
                $payloadArray = array();
                if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
                if (isset($exp)) {$payloadArray['exp'] = $exp;}

                $payloadArray['apikey'] = $apikey; //user API key
                $payloadArray['id'] = $user_query->ID; //user API key
                
                $token = UixUserCenter_JWT::encode($payloadArray, $serverKey);

                wp_send_json(array(
                    'status' => true, 
                    'message'  => 'login_ok',
                    'wpApiRoot' => esc_url_raw( rest_url() ),
                    'token' => $token
                ));
            } 
                
    
            // Finally use the unset session
            unset($_SESSION[$captcha_check['captcha_id']]);
        } else {
            wp_send_json($captcha_check);
        }


        die();
    }
}
