<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}



/**
 * Password Reset via AJAX (Step 1)
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_passwordreset' ) ) {

    add_action('wp_ajax_passwordreset_action', 'uix_usercenter_ajax_passwordreset'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_passwordreset_action', 'uix_usercenter_ajax_passwordreset');  // for non-logged in users
    function uix_usercenter_ajax_passwordreset(){

        // First check the nonce, if it fails the function will break
        check_ajax_referer( 'ajax-passwordreset-nonce', 'security' );

        
        // Nonce is checked, get the POST data
        $email = sanitize_text_field($_POST['email']);


        /* Check Captcha */
        $captcha_check = uix_usercenter_captcha_check($_POST[ 'captcha' ]);
        if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ) {
            $captcha_check['status'] = true; // disable captcha
        }
        if ( $captcha_check['status'] === true ) {
            /* Check User */
            if ( !empty( $email ) ){
                if ( !is_email( $email ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_invalid'
                    ));
                } elseif( !email_exists( $email ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_no_registered'
                    ));
                }
            }
    

            $user_query = get_user_by( 'email', $email );
            if ( ! empty( $user_query ) ) {

                wp_send_json(array(
                    'status' => true, 
                    'message'  => 'mail_correct',
                    'security_question_1' => $user_query->security_question_1,
                    'security_question_2' => $user_query->security_question_2,
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




/**
 * Password Reset via AJAX (Step 2)
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_passwordreset_verify' ) ) {

	add_action('wp_ajax_nopriv_passwordreset_verify_action', 'uix_usercenter_ajax_passwordreset_verify');  // for non-logged in users
    function uix_usercenter_ajax_passwordreset_verify(){

        // First check the nonce, if it fails the function will break
        check_ajax_referer( 'ajax-passwordreset-nonce', 'security' );

        
        // Nonce is checked, get the POST data
        $email = sanitize_text_field($_POST['email']);
        $security_answer_1 = sanitize_text_field($_POST['security_answer_1']);
        $security_answer_2 = sanitize_text_field($_POST['security_answer_2']);


        // Answer Result
        $user_id = false;	
        $old_security_answer_1 = false;	
        $old_security_answer_2 = false;

        $user_query = get_user_by( 'email', $email );
        if ( ! empty( $user_query ) ) {
            $user_id = $user_query->ID;
            $old_security_answer_1 = $user_query->security_answer_1;
            $old_security_answer_2 = $user_query->security_answer_2;
        }

        //
        function update($user_id) {
            $random_password = wp_generate_password( 12, false );

            // Update user password
            $update_user = wp_update_user( array (
                    'ID' => $user_id, 
                    'user_pass' => $random_password
                )
            );

            wp_send_json(array(
                'status' => true, 
                'message'  => 'pass_display',
                'newpass' => $random_password,
            ));    
        }

        if ( empty($old_security_answer_1) && empty($old_security_answer_2) ) {
            update($user_id);
        } else {

            if ( 
                ($security_answer_1 == $old_security_answer_1 && !empty($old_security_answer_1) ) ||
                ($security_answer_2 == $old_security_answer_2  && !empty($old_security_answer_2) )
            ) {
                update($user_id);
            } else {
                wp_send_json(array(
                    'status' => false, 
                    'message'  => 'send_failure'
                ));
            }

        }



 
        die();
    }
}
