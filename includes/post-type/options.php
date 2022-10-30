<?php

global $uix_usercenter_typeshow_val;
// This global variable will be used to match the key `uix_usercenter_typeshow` of 
// the meta field for items list in the admin panel.

$uix_usercenter_typeshow_val = array(
	'submission'          => '<i class="dashicons dashicons-format-aside"></i> '.esc_html__( 'Submission', 'uix-usercenter' ),
	'resource'     => '<i class="dashicons dashicons-cart"></i> '.esc_html__( 'Resource', 'uix-usercenter' ),
 );




/*
 * Custom Metaboxes and Fields
 *
 */


if ( class_exists( 'Uix_UserCenter_Custom_Metaboxes' ) ) { 
	

	$custom_metaboxes_uix_usercenter_vars = array(


		array(
			'config' => array( 
				'id'         =>  'uix-usercenter-meta-typeshow', 
				'title'      =>  esc_html__( 'Type', 'uix-usercenter' ),
				'screen'     =>  'uix_usercenter', 
				'context'    =>  'side',
				'priority'   =>  'high',
				'fields' => array( 

					array(
						'id'          =>  'uix_usercenter_typeshow',
						'type'        =>  'radio',
						'title'       =>  '',
						'default'     =>  'submission',
						'options'     =>  array( 
											'br'          => true,
											'radio_type'  => 'normal',
											'value'       => $uix_usercenter_typeshow_val
										  )

					),




				)
			)

		),


        //-- Item Options
		array(
			'config' => array( 
				'id'         =>  'uix-usercenter-meta-item-settings', 
				'title'      =>  esc_html__( 'Item Info', 'uix-usercenter' ),
				'screen'     =>  'uix_usercenter', 
				'context'    =>  'side',
				'priority'   =>  'high',
				'fields' => array( 

					array(
						'id'             =>  'uix_usercenter_item_status',
						'type'           =>  'number',
						'title'          =>  esc_html__( 'Status', 'uix-usercenter' ),
					), 
  

				)
			)

		),


        //-- User Options
		array(
			'config' => array( 
				'id'         =>  'uix-usercenter-meta-user-settings', 
				'title'      =>  esc_html__( 'User Info', 'uix-usercenter' ),
				'screen'     =>  'uix_usercenter', 
				'context'    =>  'side',
				'priority'   =>  'high',
				'fields' => array( 


					array(
						'id'             =>  'uix_usercenter_user_name',
						'type'           =>  'text',
						'title'          =>  esc_html__( 'Name', 'uix-usercenter' ),
					),

					array(
						'id'             =>  'uix_usercenter_user_id',
						'type'           =>  'number',
						'title'          =>  esc_html__( 'ID', 'uix-usercenter' ),
					),

					array(
						'id'             =>  'uix_usercenter_user_email',
						'type'           =>  'text',
						'title'          =>  esc_html__( 'Email', 'uix-usercenter' ),
					),   

					array(
						'id'            =>  'uix_usercenter_user_submitdate',
						'type'          =>  'date',
						'title'         =>  esc_html__( 'Submit Date', 'uix-usercenter' ),
                        'default'       => date("F d, Y"),
                        'options'       =>  array( 
                                            'format'  => 'MM dd, yy',
                                            )


					),
				)
			)

		),



        //-- Submission Options
		array(
			'config' => array( 
				'id'         =>  'uix-usercenter-meta-submission-settings', 
				'title'      =>  esc_html__( 'Submission Settings', 'uix-usercenter' ),
				'screen'     =>  'uix_usercenter', 
				'context'    =>  'normal',
				'priority'   =>  'high',
				'fields' => array( 


					array(
						'id'             =>  'uix_usercenter_submission_project_url',
						'type'           =>  'url',
						'title'          =>  esc_html__( 'Project URL', 'uix-usercenter' ),
						'placeholder'    =>  esc_html__( 'http://', 'uix-usercenter' ),
						'desc_primary'   =>  esc_html__( __( 'Enter destination URL of this project.', 'uix-usercenter' ) ),
					),

                  

				)
			)

		),


		//-- Resource Options
		array(
			'config' => array( 
				'id'         =>  'uix-usercenter-meta-resource-settings', 
				'title'      =>  esc_html__( 'Resource Settings', 'uix-usercenter' ),
				'screen'     =>  'uix_usercenter', 
				'context'    =>  'normal',
				'priority'   =>  'high',
				'fields' => array( 

					array(
						'id'             =>  'uix_usercenter_resource_name',
						'type'           =>  'text',
						'title'          =>  esc_html__( 'Item Name', 'uix-usercenter' ),
						'desc_primary'   =>  esc_html__( __( 'E.g. Uixplant, 15 characters maximum.', 'uix-usercenter' ) ),
					),

					array(
						'id'             =>  'uix_usercenter_resource_fileURL',
						'type'           =>  'url',
						'title'          =>  esc_html__( 'Purchase/Download Link', 'uix-usercenter' ),
						'desc_primary'   =>  esc_html__( __( 'Direct link to the purchase or download page.', 'uix-usercenter' ) ),
					),

					array(
						'id'             =>  'uix_usercenter_resource_filepass',
						'type'           =>  'text',
						'title'          =>  esc_html__( 'Access Password', 'uix-usercenter' ),
                        'desc_primary'   =>  esc_html__( __( 'Some resources may require a password to access.', 'uix-usercenter' ) ),
					),
                                    
                    
				)
			)

		),
       
        //---


	);

	$custom_metaboxes_uix_usercenter = new Uix_UserCenter_Custom_Metaboxes( $custom_metaboxes_uix_usercenter_vars );

	
}
