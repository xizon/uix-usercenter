<?php
if( ! isset( $_SESSION ) ) session_start();

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Create captcha via AJAX
 *
 */
if ( !function_exists( 'uix_usercenter_ajax_createcaptcha' ) ) {

    add_action('wp_ajax_createcaptcha_action', 'uix_usercenter_ajax_createcaptcha'); // Solve the problem that multiple sites share the same token across sites
	add_action('wp_ajax_nopriv_createcaptcha_action', 'uix_usercenter_ajax_createcaptcha');  // for non-logged in users
    function uix_usercenter_ajax_createcaptcha(){

        // only POST requests
        if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) die();

        // Get Captcha
         // ( --> It could be used for different domain request)
         $rand_str = UixUserCenter::generate_random_str();
         $_SESSION[get_option( 'uix_usercenter_opt_captchastr' )] = strtolower( $rand_str );

       
        //
        $testGD = get_extension_funcs("gd");
        if ( !$testGD ) {
            $captcha_res = array(
                'type' => 'string',
                'origin' => $rand_str,
                'value' => $rand_str
            );
        } else {

            
            if ( get_option( 'uix_usercenter_opt_captchastyle' ) == 'light' || !get_option( 'uix_usercenter_opt_captchastyle' ) ) {
                $theme = 'light';
            } else {
                $theme = get_option( 'uix_usercenter_opt_captchastyle' );
            }

            $img = file_get_contents(esc_url( get_site_url() . '/?uix-usercenter=1' ).'&checkcode=1&theme='.$theme.'&authstr='.$rand_str);
            $data = base64_encode($img);

            $captcha_res = array(
                'type' => 'image',
                'origin' => $rand_str,
                'value' => 'data:image/png;base64,' . $data
            );
        }

        wp_send_json(array(
            'captcha' => $captcha_res,
        ));

        die();
    }
}

