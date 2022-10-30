<?php
/*
 * Enqueuing Scripts and Styles
 * 
 */
function uix_usercenter_scripts() {

	//Check if screen ID
	$currentScreen = get_current_screen();

	if ( UixUserCenter::inc_str( $currentScreen->base, '_page_' ) &&
		 ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == UixUserCenter::HELPER )	
	 ) 
	{
	    wp_enqueue_style( UixUserCenter::PREFIX . '-helper', UixUserCenter::plug_directory() .'helper/helper.css', true, UixUserCenter::ver(), 'all' );
		wp_enqueue_script( UixUserCenter::PREFIX . '-helper', UixUserCenter::plug_directory() .'helper/helper.js', array( 'jquery' ), UixUserCenter::ver(), true );	

	} 
	
	
}
add_action( 'admin_enqueue_scripts', 'uix_usercenter_scripts' );


/*
 * Add an operator panel in admin panel
 * 
 */
function uix_usercenter_options_page(){
	
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.', 'uix-usercenter') );
    }

  
?>

<div class="wrap uix-bg-custom-wrapper">
    
    <h2><?php _e( 'Uix UserCenter Helper', 'uix-usercenter' ); ?></h2>
    <?php
	
	if( !isset( $_GET[ 'tab' ] ) ) {
		$active_tab = 'about';
	}
	
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) );
	} 
	
	$tabs = array();
	$tabs[] = array(
	    'tab'     =>  'about', 
		'title'   =>  __( 'About', 'uix-usercenter' )
	);
    
	$tabs[] = array(
	    'tab'     =>  'usage', 
		'title'   =>  __( 'How to use?', 'uix-usercenter' )
	);
	
	$tabs[] = array(
	    'tab'     =>  'temp', 
		'title'   =>  __( 'Template Files', 'uix-usercenter' )
	);

	$tabs[] = array(
	    'tab'     =>  'general-settings', 
		'title'   =>  __( '<i class="dashicons dashicons-admin-generic"></i> General Settings', 'uix-usercenter' )
	);
	
	?>
    <h2 class="nav-tab-wrapper">
        <?php foreach ( $tabs as $key ) :  ?>
            <?php $url = 'admin.php?page=' . rawurlencode( UixUserCenter::HELPER ) . '&tab=' . rawurlencode( $key[ 'tab' ] ); ?>
            <a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ) ?>"
               class="nav-tab<?php echo $active_tab === $key[ 'tab' ] ? esc_attr( ' nav-tab-active' ) : '' ?>">
                <?php echo wp_kses_post( $key[ 'title' ] ); ?>
            </a>
        <?php endforeach ?>
    </h2>

    <?php 
		foreach ( glob( UIX_USERCENTER_PLUGIN_DIR. "helper/tabs/*.php") as $file ) {
			include $file;
		}	
	?>
        
    
    
</div>
 
    <?php
     
}