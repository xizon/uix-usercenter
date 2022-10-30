<?php
/* @@{note: "wordpress.org is not allowed", remove: "optional"} */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/*
 * Create a new page and automatically assign the page template
 * 
 */
if ( !function_exists( 'uix_usercenter_create_pages' ) ) {
    if ( get_option( 'uix_usercenter_opt_autog' ) == 'on' || !get_option( 'uix_usercenter_opt_autog' ) ) {
        add_action( 'after_setup_theme', 'uix_usercenter_create_pages' );
    }
    function uix_usercenter_create_pages() {
        

        $new_pages = array(
            'tmpl-usercenter_login.php' => esc_html__( 'Custom Login', 'uix-usercenter' ),
            'tmpl-usercenter_register.php' => esc_html__( 'Custom Register', 'uix-usercenter' ),
            'tmpl-tmpl-usercenter_profile.php' => esc_html__( 'Custom Profile', 'uix-usercenter' ),
            'tmpl-usercenter_password_reset.php' => esc_html__( 'Custom Password Reset', 'uix-usercenter' ),
            'tmpl-tmpl-usercenter_submission.php' => esc_html__( 'Custom Submission', 'uix-usercenter' ),
        );  


        foreach ( $new_pages as $page => $post_title ) {

            if ( get_page_by_title( $post_title, 'OBJECT', 'page' ) == NULL ) {
                $new_page_id = wp_insert_post( array(
                    'post_title'   => $post_title, 
                    'post_content' => '', 
                    'post_status'  => 'publish', 
                    'ignore_sticky_posts' => true,
                    'post_type'    => 'page',
                ) );

                // Assign page template
                update_post_meta( $new_page_id, '_wp_page_template', $page );
            }
        }

        
    }	
}
