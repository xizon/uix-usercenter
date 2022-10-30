<?php
/**
 * This template is used to generate captcha image
 */
header( 'Content-type: image/png' );

// Style
$theme = isset($_REQUEST['theme']) ? sanitize_title( $_REQUEST['theme'] ) : 'light';

//
$sessionvar  = isset($_REQUEST['id']) ? sanitize_title( $_REQUEST['id'] ) : get_option( 'uix_usercenter_opt_captchastr' );
$imgwidth    = 100; 
$imgheight   = 40; 
$codelen     = 4;
$fontsize    = 20;
$charset     = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
$font        = UIX_USERCENTER_PLUGIN_DIR . 'includes/plugins/checkcode/Elephant.ttf';

//PHP extension "gd" must be loaded
$testGD = get_extension_funcs("gd"); // Grab function list 
if ( ! $testGD ){
	exit;
}


$im = @imagecreatetruecolor( $imgwidth, $imgheight )
      or die('Cannot Initialize new GD image stream');
 

//background
switch($theme) {
    case 'light':
        $bg = imagecolorallocate( $im, 255, 255, 255 );
        break;

    case 'dark':
        $bg = imagecolorallocate( $im, 24, 20, 22 );
        break;      

    case 'gray':
        $bg = imagecolorallocate( $im, 209, 209, 209 );
        break;            
}


imagefill( $im, 0, 0, $bg ); //Fill image
 
//Get string
$authstr = '';
$_len  = strlen( $charset ) - 1;
for ( $i = 0; $i < $codelen; $i++ ) {
	$authstr .= $charset[mt_rand( 0, $_len )];
}

// custom string
if ( isset($_REQUEST['authstr']) ) {
    $authstr = sanitize_title( $_REQUEST['authstr'] );
}
 

//save the session
//All converted to lowercase, mainly to be case insensitive
if( ! isset( $_SESSION ) ) session_start();
$_SESSION[$sessionvar] = strtolower( $authstr ); 



//Randomly painted, has been changed to star
switch($theme) {
    case 'light':
        for ( $i = 0;$i < $imgwidth; $i++ ){
            $randcolor = imagecolorallocate( $im, mt_rand( 200, 255 ), mt_rand( 200, 255 ), mt_rand( 200, 255 ) );
         imagestring( $im, mt_rand( 1, 5 ),  mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ),  '*', $randcolor );
            //imagesetpixel( $im, mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ), $randcolor );
        }
        break;

    case 'dark':
        for ( $i = 0;$i < $imgwidth; $i++ ){
            $randcolor = imagecolorallocate( $im, mt_rand( 1, 45 ), mt_rand( 1, 45 ), mt_rand( 1, 45 ) );
         imagestring( $im, mt_rand( 1, 5 ),  mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ),  '*', $randcolor );
            //imagesetpixel( $im, mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ), $randcolor );
        }
        break;      

    case 'gray':
        for ( $i = 0;$i < $imgwidth; $i++ ){
            $randcolor = imagecolorallocate( $im, mt_rand( 199, 209 ), mt_rand( 199, 209 ), mt_rand( 199, 209 ) );
         imagestring( $im, mt_rand( 1, 5 ),  mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ),  '*', $randcolor );
            //imagesetpixel( $im, mt_rand( 0, $imgwidth ), mt_rand( 0, $imgheight ), $randcolor );
        }
        break;            
}
 

//Randomly draw lines, the number of lines is equal to the number of characters
switch($theme) {
    case 'light':
        for( $i = 0;$i < $codelen;$i++ ) 
        {  
        $randcolor = imagecolorallocate( $im, mt_rand( 0, 255 ), mt_rand( 0, 255 ), mt_rand( 0, 255 ) );
        imageline( $im, 0, mt_rand( 0, $imgheight ), $imgwidth, mt_rand( 0, $imgheight ), $randcolor ); 
        } 
        break;

    case 'dark':
        for( $i = 0;$i < $codelen;$i++ ) 
        {  
        $randcolor = imagecolorallocate( $im, mt_rand( 10, 50 ), mt_rand( 10, 50 ), mt_rand( 10, 50 ) );
        imageline( $im, 0, mt_rand( 0, $imgheight ), $imgwidth, mt_rand( 0, $imgheight ), $randcolor ); 
        } 
        break;      

    case 'gray':
        for( $i = 0;$i < $codelen;$i++ ) 
        {  
        $randcolor = imagecolorallocate( $im, mt_rand( 200, 255 ), mt_rand( 200, 255 ), mt_rand( 200, 255 ) );
        imageline( $im, 0, mt_rand( 0, $imgheight ), $imgwidth, mt_rand( 0, $imgheight ), $randcolor ); 
        } 
        break;            
}
 


 
$_x = intval( $imgwidth/$codelen ); //Calculate character distance
$_y = intval( $imgheight*0.7 ); //Characters are displayed in 70% of the image
for( $i = 0; $i < strlen( $authstr ); $i++ ){
 

//text color
switch($theme) {
    case 'light':
        $randcolor = imagecolorallocate( $im, mt_rand( 0, 150 ), mt_rand( 0, 150 ), mt_rand( 0, 150 ) );
        break;

    case 'dark':
        $randcolor = imagecolorallocate( $im, mt_rand( 116, 145 ), mt_rand( 116, 145 ), mt_rand( 116, 145 ) );
        break;      

    case 'gray':
        $randcolor = imagecolorallocate( $im, mt_rand( 240, 255 ), mt_rand( 240, 255 ), mt_rand( 240, 255 ) );
        break;            
}
    
    


 // imagestring( $im, 5, $j, 5, $imgstr[$i], $color3 );
 // imagettftext (  resource $image ,  float $size ,  float $angle ,  int $x ,  int $y ,  int $color ,  string $fontfile ,  string $text  )
 imagettftext( $im, $fontsize, mt_rand( -30, 30 ), $i*$_x+3, $_y, $randcolor, $font, $authstr[$i] );
 
}
 
//Generate image
imagepng( $im );
imagedestroy( $im );
?> 