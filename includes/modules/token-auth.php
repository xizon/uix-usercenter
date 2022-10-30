<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


/*
 * Token authentication
 *
 */
/*

Usage:

    $data_query = uix_usercenter_token_auth();
    if ( isset( $data_query -> data ) ) {

        $user_query = $data_query;
        
        $_user_apikey = $user_query->apikey;
        $_user_id = $user_query->ID;
        $_user_name = $user_query->user_login;
        
        ...
    } else {
        wp_send_json($data_query);
    }

*/
if( !function_exists( 'uix_usercenter_token_auth' ) ) {
	
	function uix_usercenter_token_auth() {

        $err_unauthorized = array(
            "error" => __('Unauthorized', 'uix-usercenter'),
            "code" => 401,
        );

        //
        $headerInfo = uix_usercenter_get_headers(wp_unslash( $_SERVER ));
        $token = uix_usercenter_get_bearer_token($headerInfo['AUTHORIZATION']);
        $serverKey = md5( 'userapikey-token-key' ); // Get our server-side secret key from a secure location.

        try {

            $payload = UixUserCenter_JWT::decode($token, $serverKey, array('HS256'));

            
            //
            $exp = isset($payload->exp) ? date(DateTime::ISO8601, $payload->exp) : false;
            $user_id = $payload->id;
            $user_apikey = $payload->apikey;

            $orgin_apikey = md5( 'userapikey' . $user_id );

            if ( $orgin_apikey != $user_apikey) {
                return $err_unauthorized;
            }

            $user_query = get_user_by( 'id', $user_id );
            if ( ! empty( $user_query ) ) {
                //###############################################################################
                $user_query->apikey = $user_apikey;

                return $user_query;
                //###############################################################################
            } else {
                return $err_unauthorized;
            }

            
        } catch(Exception $e) {
            return array(
                    "error" => $e->getMessage(),
                    "code" => 400,
                );

        }



	}
	
}
