<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}



/**
 * Register via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_register' ) ) {

    add_action('wp_ajax_register_action', 'uix_usercenter_ajax_register');  // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_register_action', 'uix_usercenter_ajax_register');  // for non-logged in users
    function uix_usercenter_ajax_register(){

        // First check the nonce, if it fails the function will break
        check_ajax_referer( 'ajax-register-nonce', 'security' );

        
        // Nonce is checked, get the POST data
        $username = sanitize_user(wp_unslash($_POST['username']));
        $pass1 = sanitize_text_field($_POST['password']);
        $pass2 = sanitize_text_field($_POST['password_confirm']);
        $email = sanitize_text_field($_POST['email']);

        $info = array(
            'user_login' => $username,
            'user_pass' => $pass1,
            'user_email' => $email,
        );

    
        /* Check Captcha */
        $captcha_check = uix_usercenter_captcha_check($_POST[ 'captcha' ]);
        if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ) {
            $captcha_check['status'] = true; // disable captcha
        }
        if ( $captcha_check['status'] === true ) {
            /* Check User */

            if ( empty($username) || empty($pass1) ) {
                wp_send_json(array(
                    'status' => false, 
                    'message'  => 'name_pass_empty'
                ));
            }

            if ( username_exists( $username ) ) {
                wp_send_json(array(
                    'status' => false, 
                    'message'  => 'name_exist'
                ));
            }
    
            if ( !empty( $email ) ){
                if ( !is_email( $email ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_invalid'
                    ));
                } elseif( email_exists( $email ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_exist'
                    ));
                }
            }
        
            
            if ( !empty( $pass1 ) || !empty( $pass2 ) ) {
                if ( $pass1 != $pass2 ) {
                    $pass1 = '';
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'pass_notmatch'
                    ));
                }   
            }


            $user_id = wp_insert_user($info);

            if (!is_wp_error($user_id)) {

                // If `$secure_cookie` do not use true, `https` cannot directly authorize the management page under AJAX requests
                // If the user wants SSL but the session is not SSL, force a secure cookie.
                $secure_cookie   = '';
                if ( ! force_ssl_admin() ) {
                    $user_query_first = get_user_by( 'login', $username );
                
                    if ( ! $user_query_first && strpos( $username, '@' ) ) {
                        $user_query_first = get_user_by( 'email', $username );
                    }
                
                    if ( $user_query_first ) {
                        if ( get_user_option( 'use_ssl', $user_query_first->ID ) ) {
                            $secure_cookie = true;
                            force_ssl_admin( true );
                        }
                    }
                }

                $user_signon = wp_signon(array(
                    'user_login'  =>  $username,
                    'user_password' => $pass1
                ), $secure_cookie);

                if (!is_wp_error($user_signon)) {


                    // Register successful

                    // Generate JWT tokens
                    $apikey = md5( 'userapikey' . $user_id );
                    $serverKey = md5( 'userapikey-token-key' ); // Get our server-side secret key from a secure location.
                    // $nbf = strtotime('2021-01-01 00:00:01');  // enable the "not before" feature.
                    // $exp = strtotime('2021-01-01 00:00:01'); // enable the "expire" feature.
                    $payloadArray = array();
                    if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
                    if (isset($exp)) {$payloadArray['exp'] = $exp;}

                    $payloadArray['apikey'] = $apikey; //user API key
                    $payloadArray['id'] = $user_id; //user API key
                    
                    $token = UixUserCenter_JWT::encode($payloadArray, $serverKey);


                    wp_send_json(array(
                        'status' => true, 
                        'message'  => 'login_ok',
                        'wpApiRoot' => esc_url_raw( rest_url() ),
                        'token' => $token
                    ));
                } 
            } else {
                wp_send_json(array(
                    'status' => false, 
                    'message'  => __( $user_id->get_error_message() )
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

