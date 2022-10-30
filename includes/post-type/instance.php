<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/**
 * Uix UserCenter Post Type
 *
 */

if ( !class_exists( 'UixUserCenter_PostType' ) ) {

    class UixUserCenter_PostType {
        
        
        /**
         * Initialize
         *
         */
        public static function init() {
        
            self::includes();
            
            add_filter( 'body_class', array( __CLASS__, 'new_class' ) );
            add_filter( 'post_thumbnail_html', array( __CLASS__, 'remove_thumbnail_dimensions' ), 10, 4 );
            add_action( 'after_setup_theme', array( __CLASS__, 'add_featured_image_support' ), 11 );

        }
        
        
        /*
        * Include required files.
        *
        *
        */
        public static function includes() {
            
            //Custom post type function initialization
            require_once UIX_USERCENTER_PLUGIN_DIR . 'includes/post-type/post-type-init.php';
            
            //Options for custom meta boxes
            require_once UIX_USERCENTER_PLUGIN_DIR . 'includes/post-type/options.php';
            
        }

        
        /*
        * Extend the default WordPress body classes.
        *
        *
        */
        public static function new_class( $classes ) {
        
            global $uix_usercenter_temp;
            if ( $uix_usercenter_temp === true ) { 
                $classes[] = 'uix-usercenter-body';
            }
            
            return $classes;

        }
        
        
        
        /*
        * Featured Image
        * Add support for a custom default image
        */
        public static function custom_featured_image_column_image( $image ) {
            if ( !has_post_thumbnail() ) {
                return self::plug_directory() .'assets/images/featured-image.png';
            }
        }
        
        
        public static function custom_featured_image_column_init() {
            add_filter( 'featured_image_column_post_types', array( __CLASS__, 'custom_featured_image_column_remove_post_types' ), 11 ); // Remove
        }
        
        
        public static function custom_featured_image_column_remove_post_types( $post_types ) {
            foreach( $post_types as $key => $post_type ) {
                if ( 'page' === $post_type ) // Post type you'd like removed. Ex: 'post' or 'page'
                    unset( $post_types[$key] );
            }
            return $post_types;
        }
        
        
        public static function add_featured_image_support() {
            
            
            $supportedTypes = get_theme_support( 'post-thumbnails' );
            $thePostType = 'uix_usercenter';
            
            if( $supportedTypes === false ) {
                add_theme_support( 'post-thumbnails', array( $thePostType ) ); 
            } elseif ( is_array( $supportedTypes ) ) {
                $supportedTypes[0][] = $thePostType;
                add_theme_support( 'post-thumbnails', $supportedTypes[0] );
            }
        
        
            //---
            $uix_usercenter_opt_cover_width         = get_option( 'uix_usercenter_opt_cover_width', 400 );
            $uix_usercenter_opt_cover_height        = get_option( 'uix_usercenter_opt_cover_height', 300 );
            $uix_usercenter_opt_cover_single_width  = get_option( 'uix_usercenter_opt_cover_single_width', 1920 );	
            $uix_usercenter_opt_cover_single_height = get_option( 'uix_usercenter_opt_cover_single_height', 15000 );	
            
            
            add_image_size( 'uix-usercenter-entry', $uix_usercenter_opt_cover_width, $uix_usercenter_opt_cover_height, true );
            add_image_size( 'uix-usercenter-autoheight-entry', $uix_usercenter_opt_cover_width, 15000, false );
            add_image_size( 'uix-usercenter-gallery-post', $uix_usercenter_opt_cover_single_width, $uix_usercenter_opt_cover_single_height, false );
            
            //--- Add image sizes for retina
            add_image_size( 'uix-usercenter-retina-entry', $uix_usercenter_opt_cover_width*2, $uix_usercenter_opt_cover_height*2, true );
            add_image_size( 'uix-usercenter-autoheight-retina-entry', $uix_usercenter_opt_cover_width*2, 15000, false );
        
        }
        


        
        /*
        * Filter to remove image dimension attributes 
        *
        * 
        */
        public static function remove_thumbnail_dimensions( $html, $post_id, $post_image_id, $post_thumbnail ) {
        
            if ( $post_thumbnail == 'uix-usercenter-entry' || $post_thumbnail == 'uix-usercenter-retina-entry' ){
                $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
            }
            return $html;

        }
        

        
    }

}

UixUserCenter_PostType::init();

