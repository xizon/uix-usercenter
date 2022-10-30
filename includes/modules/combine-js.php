<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/*
 * Reset a transient when user login
 *
 */
if( !function_exists( 'uix_usercenter_ajax_usercenter_track_user_logins' ) ) {
	
	add_action( 'wp_login', 'uix_usercenter_ajax_usercenter_track_user_logins', 10, 2 );
	function uix_usercenter_ajax_usercenter_track_user_logins( $user_login, $user ){
		delete_transient( UixUserCenter::get_slug() . '-combined_uercenter_script-onetime_check' );
	}
	
}



/*
 * Combine Javascript files
 *
 */

if( !function_exists( 'uix_usercenter_combine_all_ajax_usercenter_script' ) ) {

    if ( get_option( 'uix_usercenter_opt_mergescripts' ) == 'on' ) {
        add_action('wp_print_scripts', 'uix_usercenter_combine_all_ajax_usercenter_script', 9999);
    }
    
    function uix_usercenter_combine_all_ajax_usercenter_script() {
        
        // Get list of all registered scripts in WP
        global $wp_scripts;
        
        //
        global $wp_filesystem;


        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        // New file location
        $usercenter_js_handle = UixUserCenter::frontend_js_handle();
        $all_js_file_path = 'assets/js/combine/scripts.js';
        $combined_js_file_location = UixUserCenter::plug_filepath() . $all_js_file_path;
        

        /* ===================================================================== */
        /* ============================== Print ================================ */
        /* ===================================================================== */

        $combined_uercenter_script = '';


        //Reorder the handles based on dependency
        $wp_scripts -> all_deps($wp_scripts -> queue);

        // Get our site url, for example: https://uiux.cc/
        $home_url = home_url();



        // Determines whether the current request is for an administrative interface page.
        // Determine if user is a site admin.
        // The JS generated under the login status can ensure that third-party plug-ins (Such as: "Easy Digital Downloads") can run correctly
        if ( ! is_admin() && is_super_admin() ) {



            // Loop through js files and save it
            //-----------------------------------------
            foreach($wp_scripts -> to_do as $handle) {

                if (in_array($handle, $usercenter_js_handle)) {


                    $src = strtok($wp_scripts -> registered[$handle] -> src, '?');


                    // If src is url http / https 
                    if (strpos($src, 'http') !== false) {
                        if (strpos($src, $home_url) !== false) {
                            $js_file_path = str_replace($home_url, '', $src);
                        } else {
                            $js_file_path = $src;
                        }



                        $js_file_path = ltrim($js_file_path, '/');

                    } else {

                        $js_file_path = ltrim($src, '/');

                    }

                    // Check to see if the file exists then combine
                    if (file_exists($js_file_path)) {
                        // Check for wp_localize_script
                        $localize = '';
                        $inline_script = '';

                        $obj = $wp_scripts -> registered[$handle];

                        //echo '--------- '. $js_file_path . "\n";
                        //var_dump( $obj );

                        // External script files for checkout pages of "Easy Digital Downloads"
                        if ( !empty( $obj -> extra['after'] ) && is_array( $obj -> extra['after'] ) ) {
                            foreach( $obj -> extra['after'] as $value ) {
                                $inline_script .= $value;
                            }   
                        }

                        if ( !empty( $obj -> extra['data'] ) ) {
                            $localize = $obj -> extra['data'];
                        }


                        $file_map = '/*---- file: ' . $js_file_path . ' ----*/' . PHP_EOL;
                        $file_content = $wp_filesystem->get_contents( $js_file_path );
                        
                        if ( $file_content !== false ) {
                            $combined_uercenter_script .= $file_map . $localize . $inline_script . $file_content . PHP_EOL;
                        }

                    }//endif file_exists($js_file_path)

                }//endif $usercenter_js_handle

            }

            //$generation_time =  ( $combined_uercenter_script != '' ) ? '/* ' . current_time( 'mysql' ) . ' */' . PHP_EOL : '';
            $generation_time = '';
            $combined_uercenter_script = $generation_time . $combined_uercenter_script;


            // write the combined script into current theme directory
            //-----------------------------------------
            //Only execute one-time scripts
            $transient = UixUserCenter::get_slug() . '-combined_uercenter_script-onetime_check';
            if ( !get_transient( $transient ) ) {

                set_transient( $transient, 'locked', 1800 ); // lock function for 30 Minutes

                if ( $combined_uercenter_script != '' ) {
                    if(!$wp_filesystem->put_contents( $combined_js_file_location, $combined_uercenter_script, FS_CHMOD_FILE) ) {
                        //Failed to create js file
                        return false;
                    }   
                }

            }//endif get_transient( $transient )     
            


        }    

        
        
        /* ===================================================================== */
        /* ============================== Enqueue ============================== */
        /* ===================================================================== */
        if ( ! is_admin() && 
		   !isset( $_GET['pb_preview'] )  //Compatible with Uix Page Builder editing of plugin in admin panel.
		   ) {
            
            
            // Deregister handles
            //-----------------------------------------
            foreach($wp_scripts -> to_do as $handle) {
                if (in_array($handle, $usercenter_js_handle)) {
                    wp_deregister_script($handle);
                }//endif $usercenter_js_handle
                
            }
   
            // Load the URL of combined files
            //-----------------------------------------
            if ( get_option( 'uix_usercenter_opt_unenqueuejs' ) == 'on' || !get_option( 'uix_usercenter_opt_unenqueuejs' ) ) {
                
                wp_enqueue_script( UixUserCenter::get_slug() . '-all-usercenter-scripts', UixUserCenter::plug_directory() . $all_js_file_path, false, UixUserCenter::ver(), 'all' );
 
            }
   
            
          
        }

        
    }  
}



