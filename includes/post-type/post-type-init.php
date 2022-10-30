<?php


/**
 * Registers the "UserCenter" custom post type
 *
 * @link	http://codex.wordpress.org/Function_Reference/register_post_type
 */
if ( !function_exists( 'uix_usercenter_taxonomy_register' ) ) {
    // hook into the init action and call create_book_taxonomies when it fires
    add_action( 'init', 'uix_usercenter_taxonomy_register', 0 );
    function uix_usercenter_taxonomy_register() {

        // Define post type args
        $args = array(
            'labels'			    => array(
                'name'                  => __( 'Uix UserCenter', 'uix-usercenter' ),
                'singular_name'         => __( 'UserCenter Item', 'uix-usercenter' ),
                'add_new'               => __( 'Add New Item', 'uix-usercenter' ),
                'add_new_item'          => __( 'Add New UserCenter Item', 'uix-usercenter' ),
                'edit_item'             => __( 'Edit Item', 'uix-usercenter' ),
                'new_item'              => __( 'Add New Item', 'uix-usercenter' ),
                'view_item'             => __( 'View Item', 'uix-usercenter' ),
                'search_items'          => __( 'Search Items', 'uix-usercenter' ),
                'not_found'             => __( 'No Items Found', 'uix-usercenter' ),
                'not_found_in_trash'    => __( 'No Items Found In Trash', 'uix-usercenter' ),
            ),
            'public'            => true,  
            'show_ui'           => true,  
            'supports'			=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
            'capability_type'	=> 'post',
            'rewrite'			=> array(
                /*
                 *
                 * Get the single page's permalink from the ID using "http://yoursite.com/usercenter-item/*"
                 *
                 */
                'slug'       => 'usercenter-item'

            ),



            /*
             *
             * Post type archive page working
             *
             */
            'has_archive'		=> true,
            'menu_icon'			=> 'dashicons-groups',
        );

        // Apply filters for child theming
        $args = apply_filters( 'uix_usercenter_args', $args);

        // Register the post type
        register_post_type( 'uix_usercenter', $args );


       
    }
  
}




/**
 * Adds columns in the admin view for thumbnail and taxonomies
 *
 *
 *
 */
if ( !function_exists( 'uix_usercenter_taxonomy_edit_cols' ) ) {
    add_filter( 'manage_edit-uix_usercenter_columns', 'uix_usercenter_taxonomy_edit_cols' );
    function uix_usercenter_taxonomy_edit_cols( $columns ) {

        $columns = array(
            'cb' 		                   => $columns['cb'], 
            'uix-usercenter-thumbnail'     => __( 'Thumbnail', 'uix-usercenter' ),
            'title'                  	   => $columns['title'], 
            'uix-usercenter-type'          => __( 'Type', 'uix-usercenter' ),
            'uix-usercenter-userinfo'      => __( 'User Info', 'uix-usercenter' ),
            'uix-usercenter-submitdate'    => __( 'Submit Date', 'uix-usercenter' ),
            'uix-usercenter-itemstatus'   => __( 'Status', 'uix-usercenter' ),
            'author' 	                   => __('Author', 'uix-usercenter'),
            'date'                         => $columns['date']

        );

        return $columns;
    }
 
}



/**
 * Adds columns in the admin view for thumbnail and taxonomies
 *
 * Display the meta_boxes in the column view
 */
if ( !function_exists( 'uix_usercenter_taxonomy_cols_display' ) ) {
    add_action( 'manage_uix_usercenter_posts_custom_column', 'uix_usercenter_taxonomy_cols_display', 10, 2 );
    function uix_usercenter_taxonomy_cols_display( $columns, $post_id ) {

		//get Custom product types for metaboxes
		global $uix_usercenter_typeshow_val;

        switch ( $columns ) {
                
            

            //------
            case "uix-usercenter-thumbnail":

                // Get post thumbnail ID
                $thumbnail_id = get_post_thumbnail_id();

                if ( $thumbnail_id ) {
                    $thumb = wp_get_attachment_image( $thumbnail_id, array( '50', '50' ), true );
                }
                if ( isset( $thumb ) ) {
                    echo wp_kses_post( $thumb );
                } else {
                    echo esc_html( '&mdash;' );
                }

            break;	



            //------
            case "uix-usercenter-type":

                $type = get_post_meta( get_the_ID(), 'uix_usercenter_typeshow', true );
				
                if ( !empty( $type ) ) {
                
                    $params = array( 
                            'post_type' => 'uix_usercenter',
                            'uix_usercenter_typeshow' => $type
                        );

                    //push new param "uix_usercenter_category"
                    $temp_var_typeshow = 'uix_usercenter_category';
                    if ( isset( $_GET[ $temp_var_typeshow ] ) && !empty( $_GET[ $temp_var_typeshow ] ) ) {
                        array_push( $params, array( 
                            $temp_var_typeshow => sanitize_text_field( wp_unslash( $_GET[ $temp_var_typeshow ] ) )
                        ));
                    }


                    //push new param "order"
                    if ( isset( $_GET[ 'order' ] ) && !empty( $_GET[ 'order' ] ) ) {
                        array_push( $params, array( 
                            'order' => sanitize_text_field( wp_unslash( $_GET[ 'order' ] ) )
                        ));
                    }      
                    
                    $_url = esc_url( add_query_arg( $params, admin_url( 'edit.php' ) ) ); 
                    
             
					//check product type
					if ( is_array( $uix_usercenter_typeshow_val ) ) {
						
						$cat_not_match_str = '&mdash;';
							
						foreach ($uix_usercenter_typeshow_val as $key=>$value) {
							if ( $key == $type ) {
								echo wp_kses_post( '<a href="'.$_url.'">'.$value.'</a>' );
								$cat_not_match_str = '';
								break;
							}
						}	
						echo esc_html( $cat_not_match_str );
					} else {
						echo esc_html( '&mdash;' );
					}

                    
                } else {
                    echo esc_html( '&mdash;' );
                }         
                

            break;			




            //------
            case "uix-usercenter-userinfo":

                

                $_user_name = get_post_meta( get_the_ID(), 'uix_usercenter_user_name', true );
                $_user_id = get_post_meta( get_the_ID(), 'uix_usercenter_user_id', true );
                $_user_email = get_post_meta( get_the_ID(), 'uix_usercenter_user_email', true );

                echo wp_kses_post( '
                    <strong>'.esc_html__( 'Name', 'uix-usercenter' ).'</strong>: '.(empty($_user_name) ? '&mdash;' : $_user_name).'<br>
                    <strong>'.esc_html__( 'ID', 'uix-usercenter' ).'</strong>: '.(empty($_user_id) ? '&mdash;' : $_user_id).'<br>
                    <strong>'.esc_html__( 'Email', 'uix-usercenter' ).'</strong>: '.(empty($_user_email) ? '&mdash;' : $_user_email).'
                ' );


            break;		
            

            //------
            case "uix-usercenter-submitdate":

                $_user_submitdate = get_post_meta( get_the_ID(), 'uix_usercenter_user_submitdate', true );
       
                echo empty($_user_submitdate) ? esc_html( '&mdash;' ) : esc_html( $_user_submitdate );


            break;		   

            //------
            case "uix-usercenter-itemstatus":

                $_user_itemstatus = get_post_meta( get_the_ID(), 'uix_usercenter_item_status', true );
       
                echo empty($_user_itemstatus) ? 0 : esc_html( $_user_itemstatus );


            break;		   



            //------


        }
    }


   
}



/**
 * Custom column sorting and filtering for custom post type
 *
 */
if ( !function_exists( 'uix_usercenter_admin_posts_filter' ) ) {
    add_filter( 'parse_query', 'uix_usercenter_admin_posts_filter' );
    function uix_usercenter_admin_posts_filter($query) {
        global $pagenow;

        $qv = &$query->query_vars;
        

        if ( is_admin() && $pagenow == 'edit.php' && (isset( $_GET[ 'post_type' ] ) && $_GET['post_type'] == 'uix_usercenter') ) {

    
            // Update query with filter
            if ( isset( $_GET[ 'uix_usercenter_typeshow' ] ) && !empty( $_GET[ 'uix_usercenter_typeshow' ] ) ) {
                $args = array(

                    'meta_query'  => array(
                                        array(
                                                'key'     => 'uix_usercenter_typeshow',
                                                'value'   => sanitize_text_field( wp_unslash( $_GET[ 'uix_usercenter_typeshow' ] ) ),
                                                'compare' => 'IN',
                                           )
                                     )
                );


                $query -> set( 'meta_query', $args );

                return $query;    

            } 

        }
    }

}


    


/**
 * Add Sortable Custom Columns in WordPress Dashboard  
 *
 */
if ( !function_exists( 'uix_usercenter_register_post_column_views_sortable' ) ) {
    add_filter( 'manage_edit-uix_usercenter_sortable_columns', 'uix_usercenter_register_post_column_views_sortable' );
    function uix_usercenter_register_post_column_views_sortable( $newcolumn ) {
        $newcolumn['uix-usercenter-type'] = 'uix-usercenter-type';
        return $newcolumn;
    }

}



/**
 * Set default title for Wordpress Custom Post Types
 *
 */
if ( !function_exists( 'uix_usercenter_mask_empty_post_title' ) ) {
	add_filter('pre_post_title', 'uix_usercenter_mask_empty_post_title');
	function uix_usercenter_mask_empty_post_title($value) {

		if( get_post_type() === 'uix_usercenter' ) {
			if ( empty($value) ) {
				return __( '(no title)' );
			}	
		}

		return $value;
	}
}

