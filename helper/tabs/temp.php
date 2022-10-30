<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// variables for the field and option names 
$hidden_field_name = 'submit_hidden_uix_usercenter_temp';

	
// If they did, this hidden field will be set to 'Y'
if( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'temp' &&
    ( ( isset( $_GET[ 'tempfiles' ] ) && $_GET[ 'tempfiles' ] == 'ok' ) || ( isset( $_GET[ '_wpnonce' ] ) && !empty( $_GET[ '_wpnonce' ] ) ) ) 
  ) {

	// Only if administrator
	if( current_user_can( 'administrator' ) ) {
		
		$status_echo = "";
		
		if( UixUserCenter::tempfile_exists() ) {
			// Template files removed
			$status_echo = UixUserCenter::templates( 'uix_usercenter_tempfiles', 'admin.php?page='.UixUserCenter::HELPER.'&tab=temp', true );
			echo wp_kses_post( $status_echo );
	
		} else {
			// Template files copied
			$status_echo = UixUserCenter::templates( 'uix_usercenter_tempfiles', 'admin.php?page='.UixUserCenter::HELPER.'&tab=temp' );
			echo wp_kses_post( $status_echo );
		
		}
	
	}
	
 }


if( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'temp' ) { ?>

	<?php if ( !isset( $_GET[ 'tempfiles' ] ) && !isset( $_GET[ '_wpnonce' ] ) ) { ?>
   
		<?php if( UixUserCenter::tempfile_exists() ) { ?>

			 <h3><?php _e( 'Uix UserCenter template files already exists. Remove Uix UserCenter template files in your templates directory:', 'uix-usercenter' ); ?></h3>
			 <p>
			   <?php _e( 'As a workaround you can use FTP, access path <code>/wp-content/themes/{your-theme}/</code> and remove Uix UserCenter template files.', 'uix-usercenter' ); ?>
			 </p>   

			 <div class="uix-plug-note">
				<h4><?php _e( 'Template files list:', 'uix-usercenter' ); ?></h4>
				<?php UixUserCenter::list_templates_name( 'theme' ); ?>
			 </div>
			<p>
				<a class="button button-remove" href="<?php echo esc_url( 'admin.php?page='.UixUserCenter::HELPER.'&tab=temp&tempfiles=ok' ); ?>" onClick="return confirm('<?php echo esc_attr__( 'Are you sure?\nIt is possible based on your theme of the plugin templates. When you create them again, the default plugin template will be used.', 'uix-usercenter' ); ?>');"><?php echo esc_html__( 'Remove Uix UserCenter Template Files', 'uix-usercenter' ); ?></a>
			</p>


		<?php } else { ?>

			 <h3><?php _e( 'Copy Uix UserCenter template files in your templates directory:', 'uix-usercenter' ); ?></h3>
			 <p>
			   <?php _e( 'As a workaround you can use FTP, access the Uix UserCenter template files path <code>/wp-content/plugins/uix-usercenter/theme_templates/</code> and upload files to your theme templates directory <code>/wp-content/themes/{your-theme}/</code>. ', 'uix-usercenter' ); ?>

			 </p>   

			 <p>
			 <strong><?php _e( 'Hi, there! It’s just a custom template file in the theme folder. Of course you doesn’t need to create it, you can use of the default page template or your own custom template file directly.', 'uix-usercenter' ); ?></strong>

			</p> 

			 <div class="uix-plug-note">
				<h4><?php _e( 'Template files list:', 'uix-usercenter' ); ?></h4>
				<?php UixUserCenter::list_templates_name( 'plug' ); ?>
			 </div>


			<p>
				<a class="button button-primary" href="<?php echo esc_url( 'admin.php?page='.UixUserCenter::HELPER.'&tab=temp&tempfiles=ok' ); ?>"><?php echo esc_html__( 'Click This Button to Copy Uix UserCenter Files', 'uix-usercenter' ); ?></a>
			</p> 



		<?php } ?>

	
	<?php } ?>
 
	
    
<?php } ?>