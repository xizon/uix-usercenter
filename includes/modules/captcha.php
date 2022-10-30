<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/*
* Generate CAPTCHA
*
*/

if ( !function_exists( 'uix_usercenter_captcha_generate' ) ) {
	
	function uix_usercenter_captcha_generate( $theme = 'light' ) { // possible values: light, gray, dark
		
        $testGD = get_extension_funcs("gd");
        if ( !$testGD ) {
            $rand_str = UixUserCenter::generate_random_str();
            $_SESSION[get_option( 'uix_usercenter_opt_captchastr' )] = strtolower( $rand_str );

            return '<span id="uix-usercenter-refresh-session-captcha" style="background-color:#F2F2F2;font-size:20px;text-align:center;padding:.7rem 1.3rem;display:inline-block;margin-top:.2rem;pointer-events:none;user-select:none;font-family: monospace;">'.esc_html( $rand_str ).'</span>';
        } else {
            
            if ( get_option( 'uix_usercenter_opt_captchastyle' ) == 'light' || !get_option( 'uix_usercenter_opt_captchastyle' ) ) {
                $theme = 'light';
            } else {
                $theme = get_option( 'uix_usercenter_opt_captchastyle' );
            }


            return '
            <span class="uix-usercenter-captcha-code-img">
                <span style="position: relative;display:inline-block;">
                        <em style="position: absolute;width:100%;height:100%;background: rgba(255,255,255,.9);left:0;top:0;color:#333;font-family: serif;font-weight: bold;line-height: 2.5;padding-left: .5rem;cursor:pointer;min-width:150px;" onclick="this.style.display=\'none\';document.getElementById(\'uix-usercenter-refresh-session-captcha\').src=\''.esc_url( get_site_url() . '/?uix-usercenter=1' ).'&checkcode=1&theme='.$theme.'&rnd=\'+Math.random();">'.esc_attr__( 'Get Captcha', 'uix-usercenter' ).'</em>
                        <img id="uix-usercenter-refresh-session-captcha" alt="'.esc_attr__( 'Refresh', 'uix-usercenter' ).'" src="'.esc_url( get_site_url() . '/?uix-usercenter=1' ).'&checkcode=1&theme='.$theme.'" onclick="this.src=\''.esc_url( get_site_url() . '/?uix-usercenter=1' ).'&checkcode=1&theme='.$theme.'&rnd=\'+Math.random();"></img>
                </span>
            </span>';
        }
		
	}
}


/*
 * CAPTCHA check
 *
 */
/*

Usage:

    $captcha_check = uix_usercenter_captcha_check($_POST[ 'captcha' ]);
    if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ) {
        $captcha_check['status'] = true; // disable captcha
    }
    if ( $captcha_check['status'] === true ) {
        ...

        // Finally use the unset session
        unset($_SESSION[$captcha_check['captcha_id']]);
    } else {
        switch ($captcha_check['message']) {
            case 'captcha_empty':
                echo 'Please enter the captcha.';
                break;
            case 'captcha_invalid':
                echo 'Please enter correct captcha.';
                break;
        }
    }

*/

if( !function_exists( 'uix_usercenter_captcha_check' ) ) {
	
	function uix_usercenter_captcha_check($field) {

        $captcha_id = get_option( 'uix_usercenter_opt_captchastr' );

        /* Check Captcha */
        $captcha_session_value = $_SESSION[$captcha_id];
        $captcha_value = apply_filters( 'uix_usercenter_custom_captcha_session_value', $captcha_session_value );
        $captcha 	   = isset( $field ) ? strtolower( sanitize_text_field( $field ) ) : '';
        

        // Different domain request will not get this session, 
        // the local storage of the `createnonce_action` action is used as the 
        // remote captcha, and the form value passed by the front-end is used for comparison
        // ( --> It could be used for different domain request)
        if (null === $captcha_value) {
            $captcha_null_session_value = sanitize_text_field($_POST[$captcha_id]);
            $captcha_value = apply_filters( 'uix_usercenter_custom_null_captcha_session_value', $captcha_null_session_value );
        }



        if ( empty( $captcha ) ) {

            return array(
                'status' => false, 
                'message'  => 'captcha_empty',
                'captcha_id'  => $captcha_id
            );

        } else {

            if( $captcha == $captcha_value ) {

                //###############################################################################
                return array(
                    'status' => true, 
                    'captcha_id'  => $captcha_id
                );
                //###############################################################################

            } else {
                return array(
                    'status' => false, 
                    'message'  => 'captcha_invalid',
                    'captcha_id'  => $captcha_id
                );

            }//endif $captcha == $$captcha_value

        }//endif empty( $captcha )


	}
	
}
