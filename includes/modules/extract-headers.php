<?php
/**
 * Extracts headers from a PHP-style $_SERVER array.
 *
 * @param array $server Associative array similar to `$_SERVER`.
 * @return array Headers extracted from the input.
 */
if (!function_exists('uix_usercenter_get_headers')) {
    function uix_usercenter_get_headers( $server ) {
        $headers = array();

        // CONTENT_* headers are not prefixed with HTTP_.
        $additional = array( 'CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true );

        foreach ( $server as $key => $value ) {
            if ( strpos( $key, 'HTTP_' ) === 0 ) {
                $headers[ substr( $key, 5 ) ] = $value;
            } elseif ( isset( $additional[ $key ] ) ) {
                $headers[ $key ] = $value;
            }
        }

        return $headers;
    }
}

/**
 * Get access token from header
 * 
 */
if (!function_exists('uix_usercenter_get_bearer_token')) {
    function uix_usercenter_get_bearer_token($headers) {
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }


}  

