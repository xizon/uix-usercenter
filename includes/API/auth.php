<?php

if (!defined('ABSPATH')) {
    exit;
}



/**
 * Registering a New Route
 * The URL to a REST API endpoint is: `/wp-json/<namespace>/<route>`
 *
 */
if (!function_exists('uix_usercenter_get_auth_data')) {

    add_action('rest_api_init', function () {


        /*
         * This function will be called, every time you make a GET request to:  
         * `/wp-json/usercenter/v1/auth`
        */
        /*
        Usage: 

            var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');
            if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);

            axios.post(`/wp-json/usercenter/v1/auth`, {}, {
                headers: { "Authorization": 'Bearer ' + _data.token }
            }).then(function (response) {
                console.log( response.data );
            }).catch(function (error) {
                if (error.response) {
                    console.log(error.response.status);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log(error.message);
                }
            });
        
        */

        register_rest_route('usercenter/v1', '/auth', array(
            'methods' => 'POST',
            'callback' => 'uix_usercenter_get_auth_data',
            'permission_callback' => '__return_true'
        ));
        
    });



    // Grab some data
    //------------
    function uix_usercenter_get_auth_data($request) {

        $data_query = uix_usercenter_token_auth();
        if ( isset( $data_query -> data ) ) {
            
            $user_query = $data_query;
            $res = array(
                "code" => 200,
                "message" => __('OK', 'uix-usercenter'),
                "data" => array(
                    //user info
                    'apikey' => $user_query->apikey,
                    'id' => $user_query->ID,
                    'username' => $user_query->user_login,
                    'email' => $user_query->user_email,
                    'avatar' => get_avatar_url($user_query->ID, ['size' => '51']),
                    'nicename' => $user_query->user_nicename,
                    'display_name' => $user_query->display_name,
                    'registered' => $user_query->user_registered,
                    'status' => $user_query->user_status,
                    'roles' => $user_query->roles,
                    'url' => $user_query->user_url,
                    'description' => $user_query->description,
                    'first_name' => $user_query->first_name,
                    'last_name' => $user_query->last_name,           

                    //custon fields
                    //...   
                    'security_question_1' => $user_query->security_question_1,
                    'security_answer_1' => $user_query->security_answer_1,
                    'security_question_2' => $user_query->security_question_2,
                    'security_answer_2' => $user_query->security_answer_2,
                    
                    
                    
                    
                )
            );

            return $res;
            
        } else {
            return $data_query;
        }

    }
}
