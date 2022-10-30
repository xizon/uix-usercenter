<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Update user via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_updateuser' ) ) {

    add_action('wp_ajax_updateuser_action', 'uix_usercenter_ajax_updateuser'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_updateuser_action', 'uix_usercenter_ajax_updateuser');  // for non-logged in users

    function uix_usercenter_ajax_updateuser(){

        $data_query = uix_usercenter_token_auth();
        if ( isset( $data_query -> data ) ) {
            
            // First check the nonce, if it fails the function will break
            check_ajax_referer( 'ajax-updateuser-nonce', 'security' );

            // Nonce is checked, get the POST data
            $user_id = sanitize_text_field($_POST['user_id']);
            $user_email = sanitize_email($_POST['user_email']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $display_name = sanitize_text_field($_POST['display_name']);
            $security_question_1 = sanitize_text_field($_POST['security_question_1']);
            $security_answer_1 = sanitize_text_field($_POST['security_answer_1']);
            $security_question_2 = sanitize_text_field($_POST['security_question_2']);
            $security_answer_2 = sanitize_text_field($_POST['security_answer_2']);
            $pass1 = sanitize_text_field($_POST['pass1']);
            $pass2 = sanitize_text_field($_POST['pass2']);
            $description = sanitize_textarea_field(wp_unslash($_POST['description']));
            $user_url = sanitize_url($_POST['user_url']);



            if ( !empty( $user_email ) ){
                if ( !is_email( $user_email ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_invalid'
                    ));
                } elseif( email_exists( $user_email ) && ( email_exists( $user_email ) != $user_id ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'mail_exist'
                    ));
                } else{
                    //update email
                    wp_update_user( array (
                        'ID' => $user_id, 
                        'user_email' => $user_email 
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
                } else {
                    //update password
                    wp_update_user( array (
                        'ID' => $user_id, 
                        'user_pass' => $pass1
                    ));
                }
            }

            
            if ( !empty( $display_name ) ) {
                //update display_name
                wp_update_user( array (
                    'ID' => $user_id, 
                    'display_name' => $display_name 
                ));
                //update nickname
                update_user_meta( $user_id, 'nickname', $display_name );
            }
            
            //update url
            wp_update_user( array (
                'ID' => $user_id, 
                'user_url' => $user_url 
            ));
            
            //update custom fields
            update_user_meta( $user_id, 'first_name', $first_name );
            update_user_meta( $user_id, 'last_name', $last_name );
            update_user_meta( $user_id, 'description', $description );
            update_user_meta( $user_id, 'security_question_1', $security_question_1 );
            update_user_meta( $user_id, 'security_answer_1', $security_answer_1 );
            update_user_meta( $user_id, 'security_question_2', $security_question_2 );
            update_user_meta( $user_id, 'security_answer_2', $security_answer_2 );
            

            wp_send_json(array(
                'status' => true, 
                'message'  => 'profile_updated',
            ));

            
        } else {
            wp_send_json($data_query);
        }


        die();
    }
}



/**
 * Submit a website via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_usersubmission' ) ) {

    add_action('wp_ajax_usersubmission_action', 'uix_usercenter_ajax_usersubmission'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_usersubmission_action', 'uix_usercenter_ajax_usersubmission');  // for non-logged in users

    function uix_usercenter_ajax_usersubmission(){

        $data_query = uix_usercenter_token_auth();
        if ( isset( $data_query -> data ) ) {
            
            // First check the nonce, if it fails the function will break
            check_ajax_referer( 'ajax-usersubmission-nonce', 'security' );

            
            // Nonce is checked, get the POST data

            //user info
            $user_id = sanitize_text_field($_POST['user_id']);
            $user_login = sanitize_text_field($_POST['user_login']);
            $user_email = sanitize_text_field($_POST['user_email']);

            //submit info
            $user_submission_title = sanitize_text_field($_POST['user_submission_title']);
            $user_submission_project_url = sanitize_text_field($_POST['user_submission_project_url']);



            /* Check Captcha */
            $captcha_check = uix_usercenter_captcha_check($_POST[ 'captcha' ]);
            if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ) {
                $captcha_check['status'] = true; // disable captcha
            }
            if ( $captcha_check['status'] === true ) {
                if ( !wp_http_validate_url( $user_submission_project_url ) ) {
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'url_invalid'
                    ));
                }

                if ( empty( $user_submission_title ) ){
                    wp_send_json(array(
                        'status' => false, 
                        'message'  => 'title_empty'
                    ));
                }

                // Insert custom post type objects
                $post_id = wp_insert_post(array (
                    'post_type' => 'uix_usercenter',
                    'post_title' => $user_submission_title,
                    'post_content' => '',
                    'post_status' => 'publish',
                ));

                
                if ($post_id) {
                    // insert post meta
                    add_post_meta($post_id, 'uix_usercenter_submission_project_url', $user_submission_project_url);
                    add_post_meta($post_id, 'uix_usercenter_user_name', $user_login);
                    add_post_meta($post_id, 'uix_usercenter_user_id', $user_id);
                    add_post_meta($post_id, 'uix_usercenter_user_email', $user_email);
                    add_post_meta($post_id, 'uix_usercenter_user_submitdate', date("F d, Y"));
                    add_post_meta($post_id, 'uix_usercenter_typeshow', 'submission');

                }


                //
                wp_send_json(array(
                    'status' => true, 
                    'message'  => 'send_ok',
                ));

        
                // Finally use the unset session
                unset($_SESSION[$captcha_check['captcha_id']]);
            } else {
                wp_send_json($captcha_check);
            }


        } else {
            wp_send_json($data_query);
        }


        die();

    }
}


