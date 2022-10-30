<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}



/**
 * Registering a New Route
 * The URL to a REST API endpoint is: `/wp-json/<namespace>/<route>`
 *
 */
if( !function_exists( 'uix_usercenter_get_usercenter_data' ) ) {

	add_action('rest_api_init', function() {


        /*
         * This function will be called, every time you make a GET request to:  
         * all posts          =>  `/wp-json/usercenter/v1/all/display/{DISPLAY_NUMBER}/page/{PAGE_NUMBER}/rand/{RAND}/statistics/{ENABLE_STAT}`
         * posts by category  =>  `/wp-json/usercenter/v1/{CAT_SLUG}/display/{DISPLAY_NUMBER}/page/{PAGE_NUMBER}/rand/{RAND}/statistics/{ENABLE_STAT}`
         * 
         * All categories use the `all` keyword
        */

		register_rest_route('usercenter/v1', '/(?P<cat_slug>.+)/display/(?P<display_number>\d+)/page/(?P<page_number>\d+)/rand/(?P<orderby_rand>\d+)/statistics/(?P<enable_stat>\d+)', array(
			'methods' => 'POST',
			'callback' => 'uix_usercenter_get_usercenter_data',
			'permission_callback' => '__return_true',
		));
	});

	
	// Grab some data
	//------------
	function uix_usercenter_get_usercenter_data($request) {

        $data_query = uix_usercenter_token_auth();
        if ( isset( $data_query -> data ) ) {
        
            $per_page = !isset($request['display_number']) ? 3 : ($request['display_number'] > 100 ? 100 : $request['display_number']);
            $paged = !isset($request['page_number']) ? 1 : $request['page_number'];
            $cat_slug = !isset($request['cat_slug']) ? '' : ($request['cat_slug'] == 'all' ? '' : sanitize_title($request['cat_slug']) );
            $enable_stat = !isset($request['enable_stat']) ? 0 : $request['enable_stat'];
            $orderby_rand = !isset($request['orderby_rand']) ? 0 : $request['orderby_rand'];

            //
            $user_query = $data_query;
            $user_id = $user_query->ID;
            
            //
            $meta_query = $cat_slug == '' ? array(
                array(
                    'key'     => 'uix_usercenter_user_id',
                    'value'   => $user_id,
                    'compare' => 'IN',
            ),
            ) : array(
                array(
                        'key'     => 'uix_usercenter_typeshow',
                        'value'   => $cat_slug,
                        'compare' => 'IN',
                ),
                array(
                    'key'     => 'uix_usercenter_user_id',
                    'value'   => $user_id,
                    'compare' => 'IN',
            ),
            );



            //++++++++++++++++++++++++++++++++++++++++++++++++
            // output statistics
            if ( (int)$enable_stat === 1 ) {
                $args = array(
                    'orderby'             => 'date',
                    'order'               => 'DESC',	
                    'post_type'           => 'uix_usercenter',
                    'posts_per_page'      => -1,
                    'no_found_rows'       => true,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true,
                    'meta_query'          => $meta_query,
                );	
                
                $stat_query = new WP_Query($args); 
                $total_num = 0;
                if ( $stat_query->have_posts() ) {    
                    while ( $stat_query->have_posts() ) :
                        $stat_query->the_post();
                        $total_num++;
                    endwhile;
                    wp_reset_postdata();
                }

                $res = array(
                    "code" => 200,
                    "message" => __( 'OK', 'uix-usercenter' ),
                    "data" => array(
                        "total" => $total_num
                    ),
                );   

            }//endif $enable_stat

            //++++++++++++++++++++++++++++++++++++++++++++++++
            if ( (int)$enable_stat === 0 ) {

                $args = array(
                    'orderby'             => (int)$orderby_rand === 1 ? 'rand' : 'date',
                    'order'               => 'DESC',	
                    'post_type'           => 'uix_usercenter',
                    'posts_per_page'      => $per_page,
                    'paged'               => $paged,
                    'no_found_rows'       => true,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true,
                    'meta_query'          => $meta_query,
                );	

                
                $items = new WP_Query( $args );

                $posts = array();

                if ( $items->have_posts() ) {    
                    while ( $items->have_posts() ) :
                        $items->the_post();

                    
                        // Thumbnail size
                        if ( $layout == 'masonry' ) { 
                            $thumbnail_size        = 'uix-usercenter-autoheight-entry';
                            $thumbnail_retina_size = 'uix-usercenter-autoheight-retina-entry';
                            $thumbnail_large_size  = 'uix-usercenter-gallery-post';

                        } else {
                            $thumbnail_size        = 'uix-usercenter-entry';
                            $thumbnail_retina_size = 'uix-usercenter-retina-entry';
                            $thumbnail_large_size  = 'uix-usercenter-gallery-post';
                        }

                        //type
                        $project_type          = get_post_meta( get_the_ID(), 'uix_usercenter_typeshow', true );

                        // Item
                        $item_status           = get_post_meta( get_the_ID(), 'uix_usercenter_item_status', true );

                        

                        // User
                        $user_name             = get_post_meta( get_the_ID(), 'uix_usercenter_user_name', true );
                        $user_id               = get_post_meta( get_the_ID(), 'uix_usercenter_user_id', true );
                        $user_email            = get_post_meta( get_the_ID(), 'uix_usercenter_user_email', true );
                        $user_submitdate       = get_post_meta( get_the_ID(), 'uix_usercenter_user_submitdate', true );

                        // Submission
                        $submission_project_url = get_post_meta( get_the_ID(), 'uix_usercenter_submission_project_url', true );

                        // Resource
                        $resource_name          = get_post_meta( get_the_ID(), 'uix_usercenter_resource_name', true );
                        $resource_fileURL       = get_post_meta( get_the_ID(), 'uix_usercenter_resource_fileURL', true );
                        $resource_filepass      = get_post_meta( get_the_ID(), 'uix_usercenter_resource_filepass', true );

                                                        
                        //date
                        $date = new DateTime(($user_submitdate == '' ? '0000-00-00 00:00:00' : $user_submitdate));
                        
                        array_push($posts, array(
                            'ID' => get_the_ID(),

                            //content
                            'title' => get_the_title(),
                            'excerpt' => get_the_excerpt(), 

                            //featured image
                            'thumbnail' => has_post_thumbnail() ? wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $thumbnail_size ) : [],
                            'thumbnail_retina' => has_post_thumbnail() ? wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $thumbnail_retina_size ) : [],
                
                    
                            //path slug
                            'slug' => pathinfo( parse_url( get_permalink(), PHP_URL_PATH ), PATHINFO_BASENAME ),  

                            //type
                            'project_type' => $project_type, 

                            //date
                            'date_day' => $date->format('d'),
                            'date_month' => $date->format('m'),
                            'date_month_e' => $date->format('F'),
                            'date_year' => $date->format('Y'),
                            'date_weekday' => $date->format('w'),


                            // Item
                            'status' => empty($item_status) ? 0 : $item_status,

                            // User
                            'user_name' => $user_name,
                            'user_id' => $user_id,
                            'user_email' => $user_email,
                            'user_submitdate' => $user_submitdate,

                            // Submission
                            'submission_project_url' => $submission_project_url,

                            // Resource
                            'resource_name' => $resource_name,
                            'resource_fileURL' => $resource_fileURL,
                            'resource_filepass' => $resource_filepass,
                                
                        ));

            
                    endwhile;
                    wp_reset_postdata();
                

                    $res = array(
                        "code" => 200,
                        "message" => __( 'OK', 'uix-usercenter' ),
                        "data" => $posts,
                    ); 


                } else {

                    $res = array(
                        "error" => __( 'No content', 'uix-usercenter' ),
                        "code" => 404,
                    );
                    
                }

                
            }//endif $enable_stat

            return $res;   
            
        } else {
            return $data_query;
        }


	}

}

