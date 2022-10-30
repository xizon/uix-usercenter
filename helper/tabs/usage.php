<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'usage' ) {
?>

        <p>
           <?php _e( '<h4 class="uix-bg-custom-title">1. After activating your theme, you can see a prompt pointed out as absolutely critical. Go to <strong>"Appearance -> Install Plugins"</strong>.
Or, upload the plugin to wordpress, Activate it. (Access the path (/wp-content/plugins/) And upload files there.)</h4>', 'uix-usercenter' ); ?>
        </p>  
        <p>
           <?php _e( '<h4 class="uix-bg-custom-title">2. You need to create Uix UserCenter template files in your templates directory. You can create the files on the WordPress admin panel.</h4>', 'uix-usercenter' ); ?>
     
        </p>  
        <p>
           &nbsp;&nbsp;&nbsp;&nbsp;<a class="button button-primary" href="<?php echo esc_url( admin_url( "admin.php?page=".UixUserCenter::HELPER."&tab=temp" ) ); ?>"><?php _e( 'Create now!', 'uix-usercenter' ); ?></a>
     
        </p>  
         <p>
           <?php _e( '&nbsp;&nbsp;&nbsp;&nbsp;As a workaround you can use FTP, access the Uix UserCenter template files path <code>/wp-content/plugins/uix-usercenter/theme_templates/</code> and upload files to your theme templates directory <code>/wp-content/themes/{your-theme}/</code>. ', 'uix-usercenter' ); ?>
   
        </p>  
        
<?php } ?>