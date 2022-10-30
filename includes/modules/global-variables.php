<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}



/**
 * Add global variables
 *
 */

if( !function_exists( 'uix_usercenter_ajax_global_variables' ) ) {
	
	add_action( 'init', 'uix_usercenter_ajax_global_variables' );
	function uix_usercenter_ajax_global_variables(){
        

        //###################### Page URL ##################################

		// Custom AJAX login page & page ID
		$ajax_login_url = '';
		$ajax_login_pageid = 0;
		foreach( UixUserCenter::get_pageid_from_template( 'tmpl-usercenter_login.php' ) as $id ){
			$ajax_login_url = esc_url( get_permalink( $id ) );
			$ajax_login_pageid = $id;
		}


		// Custom AJAX register page & page ID
		$ajax_register_url = '';
		$ajax_register_pageid = 0;
		foreach( UixUserCenter::get_pageid_from_template( 'tmpl-usercenter_register.php' ) as $id ){
			$ajax_register_url = esc_url( get_permalink( $id ) );
			$ajax_register_pageid = $id;
		}

		// Custom AJAX password reset page & page ID
		$ajax_pw_reset_url = '';
		$ajax_pw_reset_pageid = 0;
		foreach( UixUserCenter::get_pageid_from_template( 'tmpl-usercenter_password_reset.php' ) as $id ){
			$ajax_pw_reset_url = esc_url( get_permalink( $id ) );
			$ajax_pw_reset_pageid = $id;
		}


		// Custom AJAX profile page & page ID
		$ajax_profile_url = '';
		$ajax_profile_pageid = 0;
		foreach( UixUserCenter::get_pageid_from_template( 'tmpl-usercenter_profile.php' ) as $id ){
			$ajax_profile_url = esc_url( get_permalink( $id ) );
			$ajax_profile_pageid = $id;
		}


		// Custom AJAX submission page & page ID
		$ajax_submission_url = '';
		$ajax_submission_pageid = 0;
		foreach( UixUserCenter::get_pageid_from_template( 'tmpl-usercenter_submission.php' ) as $id ){
			$ajax_submission_url = esc_url( get_permalink( $id ) );
			$ajax_submission_pageid = $id;
		}
   
        //
        global $uix_usercenter_global_pages;
        $uix_usercenter_global_pages = array(
            'ajax_login_url' => $ajax_login_url,
            'ajax_login_pageid' => $ajax_login_pageid,
            'ajax_register_url' => $ajax_register_url,
            'ajax_register_pageid' => $ajax_register_pageid,
            'ajax_pw_reset_url' => $ajax_pw_reset_url,
            'ajax_pw_reset_pageid' => $ajax_pw_reset_pageid,
            'ajax_profile_url' => $ajax_profile_url,
            'ajax_profile_pageid' => $ajax_profile_pageid,
            'ajax_submission_url' => $ajax_submission_url,
            'ajax_submission_pageid' => $ajax_submission_pageid,
        );

        //###################### /Page URL ##################################




        //###################### AJAX Object ##################################
        //
        global $uix_usercenter_global_ajax_object;
        $uix_usercenter_global_ajax_object = array( 
            'ajaxUrl'                   => esc_url( admin_url( 'admin-ajax.php' ) ),
            'redirecturl_login'         => get_option( 'uix_usercenter_opt_redirectloginurl', ( !empty($ajax_submission_url) ? $ajax_submission_url : home_url() ) ),
            'redirecturl_logout'        => get_option( 'uix_usercenter_opt_redirectlogouturl', home_url() ),
            'captcha_detect'            => get_option( 'uix_usercenter_opt_captchadetect' ) == 'off' ? 'off' : 'on',
            'captcha_id'                => get_option( 'uix_usercenter_opt_captchastr' ),
            'i18n'                      => array(
                'captcha_getstr'            => esc_attr__('Get Captcha', 'uix-usercenter'),
                'security_questions'        => array(
                    '1' => esc_html__('What is the first name of your best friend in high school?', 'uix-usercenter'),
                    '2' => esc_html__('What was the first film you saw in the theatre?', 'uix-usercenter'),
                    '3' => esc_html__('City where you met your other half?', 'uix-usercenter'),
                    '4' => esc_html__('What is the first name of the person you first kissed?', 'uix-usercenter'),
                    '5' => esc_html__('What was the name of your elementary / primary school?', 'uix-usercenter'),
                    '6' => esc_html__('In what city or town does your nearest sibling live?', 'uix-usercenter'),
                    '7' => esc_html__('What time of the day were you born? (hh:mm)', 'uix-usercenter'),
                    '8' => esc_html__('What is your pet\'s name?', 'uix-usercenter'),
                    '9' => esc_html__('What is the name of your favourite band or singer?', 'uix-usercenter'),
                    '10' => esc_html__('In what year was your mother born?', 'uix-usercenter'),
                ),
                'loadingmessage'            => esc_html__( 'Sending, please wait...', 'uix-usercenter' ),
                'review'                    => esc_html__( 'reviewing', 'uix-usercenter' ),
                'defaultcontent'            => esc_html__( 'loading...', 'uix-usercenter' ),
                'none'                      => esc_html__( 'You have no content.', 'uix-usercenter' ),
                'profile_updated'           => esc_html__( 'Profile successfully updated.', 'uix-usercenter' ),
                'mail_invalid'              => esc_html__( 'Invalid e-mail address.', 'uix-usercenter' ),
                'mail_exist'                => esc_html__( 'Email already exists.', 'uix-usercenter' ),
                'mail_no_registered'        => esc_html__( 'There is no user registered with that email address.', 'uix-usercenter' ),
                'mail_correct'              => esc_html__( 'Email verification is correct.', 'uix-usercenter' ),
                'pass_notmatch'             => esc_html__( 'The passwords entered twice do not match.', 'uix-usercenter' ),
                'pass_display_callback'     => function($arg) { return sprintf( __( 'Your new password is: %s', 'uix-usercenter' ), $arg ); },
                'pass_display'              => esc_html__( 'Your new password is: ', 'uix-usercenter' ),
                'url_invalid'               => esc_html__( 'It is NOT valid URL.', 'uix-usercenter' ),
                'title_empty'               => esc_html__( 'The title can not be blank.', 'uix-usercenter' ),
                'captcha_invalid'           => esc_html__( 'Please enter correct captcha.', 'uix-usercenter' ),
                'captcha_empty'             => esc_html__( 'Please enter the captcha.', 'uix-usercenter' ),
                'send_ok'                   => esc_html__( 'Your submission was successful. Thank you!', 'uix-usercenter' ),
                'send_failure'              => esc_html__( 'Data update failure.', 'uix-usercenter' ),
                'login_ok'                  => esc_html__( 'Login successful, redirecting...', 'uix-usercenter' ),
                'logout_ok'                 => esc_html__( 'Logout successful, redirecting...', 'uix-usercenter' ),
                'name_mail_pass_invalid'    => esc_html__( 'Wrong username, e-mail address or password.', 'uix-usercenter' ),
                'name_pass_empty'           => esc_html__( 'Username and password cannot be empty.', 'uix-usercenter' ),
                'name_exist'                => esc_html__( 'Username already exists.', 'uix-usercenter' ),
                'unauthorized'              => esc_html__( 'Unauthorized', 'uix-usercenter' ),   
                'security_tip'              => esc_html__( 'Please modify your Password & Security Questions to login next time.', 'uix-usercenter' ),   
            )
        );

        //###################### /AJAX Object ##################################


		
	}
}

