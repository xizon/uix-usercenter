<?php

/**
 * Uix UserCenter
 *
 * @author UIUX Lab <uiuxlab@gmail.com>
 *
 *
 * Plugin name: Uix UserCenter
 * Plugin URI:  https://uiux.cc/
 * Description: Sign-in, registration and publishing system with AJAX, support remote API.
 * Version:     1.0.1
 * Author:      UIUX Lab
 * Author URI:  https://uiux.cc
 * License:     GPLv2 or later
 * Text Domain: uix-usercenter
 * Domain Path: /languages
 */

class UixUserCenter {




    const PREFIX = 'uix';
    const HELPER = 'uix-usercenter-helper';
    const NOTICEID = 'uix-usercenter-helper-tip';


    /**
     * Initialize
     *
     */
    public static function init() {

        self::setup_constants();
        self::includes();

        add_action( 'init', array( __CLASS__, 'captcha' ) );
        add_action( 'init', array( __CLASS__, 'register_scripts' ) );
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'actions_links'), -10);
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'backstage_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontpage_scripts' ) );
        add_action('current_screen', array(__CLASS__, 'usage_notice'));
        add_action('admin_init', array(__CLASS__, 'tc_i18n'));
        add_action('admin_init', array(__CLASS__, 'load_helper'));
        add_action('admin_init', array(__CLASS__, 'nag_ignore'));
        add_action('admin_menu', array(__CLASS__, 'options_admin_menu'));

        //fix Access-Control-Allow-Origin (CORS origin) Issue
        add_action( 'init', array( __CLASS__, 'cors_config' ) );

        //permission settings
        add_action( 'init', array( __CLASS__, 'permission_redirect_loginpage' ) );
        add_action( 'wp_head', array( __CLASS__, 'permission_remove_toolbar' ) );
        add_action( 'admin_init', array( __CLASS__, 'permission_adminpage_redirect_member' ) );

        //Make a virtual page for checkcode
        add_action( 'generate_rewrite_rules', array( __CLASS__, 'checkcodepage_rewrite_rules' ) );
        add_action( 'template_redirect', array( __CLASS__, 'checkcodepage_template_redirect' ) );
        add_action( 'query_vars', array( __CLASS__, 'checkcodepage_custom_query_vars_filter' ) );
        
    }

    /**
     * Files for the frontend function queue 
     * (you can cancel them and create your own script, 
     * or you can set up the script queue in the Settings from admin panel)
     *
     */
    public static  function frontend_js_handle() {

        return array(
            'utils' => self::get_slug() . '-ajax-utils',
            'createnonce' => self::get_slug() . '-ajax-createnonce',
            'createcaptcha' => self::get_slug() . '-ajax-createcaptcha',
            'curd' => self::get_slug() . '-ajax-curd',
            'login' => self::get_slug() . '-ajax-login',
            'logout' => self::get_slug() . '-ajax-logout',
            'register' => self::get_slug() . '-ajax-register',
            'passwordreset' => self::get_slug() . '-ajax-passwordreset',
        );
    }


    /**
     * Initialize captcha
     *
     */
    public static  function captcha() {

        if ( !get_option( 'uix_usercenter_opt_captchastr' ) || empty(get_option( 'uix_usercenter_opt_captchastr' )) ) {
            update_option( 'uix_usercenter_opt_captchastr', 'uix_usercenter_captcha_validate_session' );
        }
    }

    
    /**
     * Setup plugin constants.
     *
     */
    public static  function setup_constants() {

        // Plugin Folder Path.
        if (!defined('UIX_USERCENTER_PLUGIN_DIR')) {
            define('UIX_USERCENTER_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
        }

        // Plugin Folder URL.
        if (!defined('UIX_USERCENTER_PLUGIN_URL')) {
            define('UIX_USERCENTER_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
        }

        // Plugin Root File.
        if (!defined('UIX_USERCENTER_PLUGIN_FILE')) {
            define('UIX_USERCENTER_PLUGIN_FILE', trailingslashit(__FILE__));
        }
    }

    /*
	 * Include required files.
	 *
	 *
	 */
    public static function includes() {

        //Add custom meta boxes API. 
        //Provides a compatible solution for some personalized themes that require Uix UserCenter.
        require_once UIX_USERCENTER_PLUGIN_DIR . 'includes/admin/uix-custom-metaboxes/init.php';


        //Core
        foreach (glob(UIX_USERCENTER_PLUGIN_DIR . "includes/modules/*.php") as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }

        foreach (glob(UIX_USERCENTER_PLUGIN_DIR . "includes/API/*.php") as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }

        // custom post type
        require_once UIX_USERCENTER_PLUGIN_DIR . "includes/post-type/instance.php";


    }


    /*
    * 
    *
    */

      
    /*
    * Make a virtual page for checkcode
    *
    * Such as: 
    *         get_site_url() . '/?uix-usercenter=1&checkcode=1&theme=light'
    *         get_site_url() . '/uix-usercenter/checkcode?theme=light'
    *
    */
    public static function checkcodepage_rewrite_rules($wp_rewrite) {
        $wp_rewrite->rules = array_merge(
            ['uix-usercenter/checkcode?$' => 'index.php?uix-usercenter=1&checkcode=1'],
            $wp_rewrite->rules
        );
    }

    public static function checkcodepage_template_redirect() {
        if ( ! get_query_var( 'uix-usercenter' ) || ! get_query_var( 'checkcode' ) ) {
            return;
        }
        require_once UIX_USERCENTER_PLUGIN_DIR . "includes/plugins/checkcode/index.php";
        die();
    }

    public static function checkcodepage_custom_query_vars_filter($vars) {
        $vars[] .= 'uix-usercenter';
        $vars[] .= 'checkcode';
        return $vars;
    }







	/*
    * Fix Access-Control-Allow-Origin (CORS origin) Issue
    *
    * !!! Make AJAX requests for different domains valid
    *
    */
    public static function cors_config() {
        $origin = get_http_origin();

        // Allow CORS domains
        $_domains = get_option( 'uix_usercenter_opt_corsdomains' );
        
        function trim_value(&$value) { 
            $value = trim($value); 
        }
        
        $_domains = rtrim(trim($_domains), ',');
        $allow_domains = explode(',', $_domains);
        array_walk($allow_domains, 'trim_value');

        foreach($allow_domains as $domain) {
            if ($origin === $domain) {
                header("Access-Control-Allow-Methods: HEAD, GET, POST, DELETE, PUT, OPTIONS");
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: *');
                header('Access-Control-Allow-Credentials: true');

                if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
                    status_header(200);
                    exit();
                }
            }
        }



    }




	/*
    * Permission settings (Redirect it to a custom login page)
    *
    * !!! Important: Please do not use the `require` method, otherwise `global` variables will be invalid
    *
    */
    public static function permission_redirect_loginpage() {

        if ( get_option( 'uix_usercenter_opt_jumploginpage' ) == 'on' ) {
            
            //Get custom global variables
            global $uix_usercenter_global_pages;


            //
            if ( $uix_usercenter_global_pages['ajax_login_url'] != '' ) {
                global $pagenow;
                $action = (isset($_GET['action'])) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

                // Check if we're on the login page, and ensure the action are not 'logout', 'lostpassword', 'rp', 'resetpass'
                if( $pagenow == 'wp-login.php' && ( ! $action || ( $action && ! in_array($action, array('logout'))))) {

                    wp_redirect( $uix_usercenter_global_pages['ajax_login_url'] );
                    exit();
                }		
            }
        }

    }

    
	/*
    * Permission settings (Remove the WordPress Admin Toolbar for register members)
    *
    *
    */
    public static function permission_remove_toolbar() {

        if ( get_option( 'uix_usercenter_opt_removetoolbar' ) == 'on' ) {
            
            //The user has not the "administrator" role
            if ( ! current_user_can( 'administrator' ) ) {
                add_filter( 'show_admin_bar', '__return_false' );
            }
            
        }

    }


	/*
    * Permission settings (Admin Page Redirect for Member)
    *
    *
    */
    public static function permission_adminpage_redirect_member() {

        if ( get_option( 'uix_usercenter_opt_adminpageredirect' ) == 'on' ) {
            
            if ( ! is_user_logged_in() ) return;
	
            //Redirect custom page
            if( ! current_user_can( 'administrator' ) && is_admin() && ( ! wp_doing_ajax() ) ) {
                wp_redirect( esc_url( home_url( '/' ) ) );
                exit;
            }
            
        }

    }

	
	/*
    * Register scripts and styles.
    *
    *
    */
    public static function register_scripts() {
       
        if ( get_option( 'uix_usercenter_opt_unenqueuejs' ) == 'on' || !get_option( 'uix_usercenter_opt_unenqueuejs' ) ) {

            wp_register_script( 'axios', self::plug_directory() .'assets/js/libs/axios.min.js', false, '0.19.2', true );

            
            wp_register_script( self::frontend_js_handle()['utils'], self::plug_directory() .'assets/js/frontend/utils.js', array( 'axios' ), self::ver(), true );

            wp_register_script( self::frontend_js_handle()['createnonce'], self::plug_directory() .'assets/js/frontend/ajax-createnonce.js', array( 'axios', self::frontend_js_handle()['utils'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['createcaptcha'], self::plug_directory() .'assets/js/frontend/ajax-createcaptcha.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['login'], self::plug_directory() .'assets/js/frontend/ajax-login.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['logout'], self::plug_directory() .'assets/js/frontend/ajax-logout.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['curd'], self::plug_directory() .'assets/js/frontend/ajax-curd.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['passwordreset'], self::plug_directory() .'assets/js/frontend/ajax-passwordreset.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
            
            wp_register_script( self::frontend_js_handle()['register'], self::plug_directory() .'assets/js/frontend/ajax-register.js', array( self::frontend_js_handle()['createnonce'] ), self::ver(), true );
        }
    }
    
 
    /*
     * Enqueue scripts and styles.
     *
     *
     */
    public static function frontpage_scripts() {

        if ( get_option( 'uix_usercenter_opt_unenqueuejs' ) == 'on' || !get_option( 'uix_usercenter_opt_unenqueuejs' ) ) {
            //Get global variables
            global $uix_usercenter_global_ajax_object;

            wp_enqueue_script( 'axios' );

            wp_enqueue_script( self::frontend_js_handle()['utils'] );
            wp_localize_script( self::frontend_js_handle()['utils'], 'ajax_object', $uix_usercenter_global_ajax_object);

            wp_enqueue_script( self::frontend_js_handle()['createnonce'] );
            wp_enqueue_script( self::frontend_js_handle()['createcaptcha'] );
            wp_enqueue_script( self::frontend_js_handle()['login'] );
            wp_enqueue_script( self::frontend_js_handle()['logout'] );
            wp_enqueue_script( self::frontend_js_handle()['curd'] );
            wp_enqueue_script( self::frontend_js_handle()['passwordreset'] );
            wp_enqueue_script( self::frontend_js_handle()['register'] );
        }

    }
    
 

	
	/*
	 * Enqueue scripts and styles  in the backstage
	 *
	 *
	 */
	public static function backstage_scripts() {
        wp_enqueue_script( self::PREFIX . '-usercenter-admin', self::plug_directory() .'includes/admin/js/core.min.js', array( 'jquery' ), self::ver(), true );	 
   }
   
   


    /**
     * Internationalizing  Plugin
     *
     */
    public static function tc_i18n() {


        load_plugin_textdomain('uix-usercenter', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        //move language files to System folder "languages/plugins/yourplugin-<locale>.po"
        global $wp_filesystem;

        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $filenames = array();
        $filepath = UIX_USERCENTER_PLUGIN_DIR . 'languages/';
        $systempath = WP_CONTENT_DIR . '/languages/plugins/';

        if (!$wp_filesystem->is_dir($systempath)) {
            $wp_filesystem->mkdir($systempath, FS_CHMOD_DIR);
        } //endif is_dir( $systempath ) 

        if ($wp_filesystem->is_dir($systempath)) {

            //Only execute one-time scripts
            $transient = self::PREFIX . '-usercenter-lang_files_onetime_check';
            if (!get_transient($transient)) {

                set_transient($transient, 'locked', 1800); // lock function for 30 Minutes


                foreach (glob(dirname(__FILE__) . "/languages/*.po") as $file) {
                    $filenames[] = str_replace(dirname(__FILE__) . "/languages/", '', $file);
                }

                foreach (glob(dirname(__FILE__) . "/languages/*.mo") as $file) {
                    $filenames[] = str_replace(dirname(__FILE__) . "/languages/", '', $file);
                }

                foreach ($filenames as $filename) {

                    // Copy
                    $dir1 = $wp_filesystem->find_folder($filepath);
                    $file1 = trailingslashit($dir1) . $filename;

                    $dir2 = $wp_filesystem->find_folder($systempath);
                    $file2 = trailingslashit($dir2) . $filename;

                    $filecontent = $wp_filesystem->get_contents($file1);

                    $wp_filesystem->put_contents($file2, $filecontent, FS_CHMOD_FILE);
                }
            } //endif get_transient( $transient )


        } //endif is_dir( $systempath )  



    }

	/*
	 * The function finds the position of the first occurrence of a string inside another string.
	 *
	 * As strpos may return either FALSE (substring absent) or 0 (substring at start of string), strict versus loose equivalency operators must be used very carefully.
	 *
	 */
	public static function inc_str( $str, $incstr ) {
		
		$incstr = str_replace( '(', '\(',
				  str_replace( ')', '\)',
				  str_replace( '|', '\|',
				  str_replace( '*', '\*',
				  str_replace( '+', '\+',
			      str_replace( '.', '\.',
				  str_replace( '[', '\[',
				  str_replace( ']', '\]',
				  str_replace( '?', '\?',
				  str_replace( '/', '\/',
				  str_replace( '^', '\^',
			      str_replace( '{', '\{',
				  str_replace( '}', '\}',	
				  str_replace( '$', '\$',
			      str_replace( '\\', '\\\\',
				  $incstr 
				  )))))))))))))));
			
		if ( !empty( $incstr ) ) {
			if ( preg_match( '/'.$incstr.'/', $str ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}


	}





    /*
	 * Create customizable menu in backstage  panel
	 *
	 * Add a submenu page
	 *
	 */
    public static function options_admin_menu() {

        //Add sub links
        add_submenu_page(
            'edit.php?post_type=uix_usercenter',
            __('How to use?', 'uix-usercenter'),
            __('How to use?', 'uix-usercenter'),
            'manage_options',
            'admin.php?page=' . self::HELPER . '&tab=usage'
        );

        add_submenu_page(
            'edit.php?post_type=uix_usercenter',
            __('Template Files', 'uix-usercenter'),
            __('Template Files', 'uix-usercenter'),
            'manage_options',
            'admin.php?page=' . self::HELPER . '&tab=temp'
        );

        add_submenu_page(
            'edit.php?post_type=uix_usercenter',
            __('Settings', 'uix-usercenter'),
            __('Settings', 'uix-usercenter'),
            'manage_options',
            'admin.php?page=' . self::HELPER . '&tab=general-settings'
        );

        add_submenu_page(
            'edit.php?post_type=uix_usercenter',
            __('Helper', 'uix-usercenter'),
            __('About', 'uix-usercenter'),
            'manage_options',
            self::HELPER,
            'uix_usercenter_options_page'
        );
    }
             


    /*
	 * Load helper
	 *
	 */
    public static function load_helper() {

        require_once UIX_USERCENTER_PLUGIN_DIR . 'helper/settings.php';
    }


    /**
     * Add plugin actions links
     */
    public static function actions_links($links) {
        $links[] = '<a href="' . admin_url("admin.php?page=" . self::HELPER . "&tab=usage") . '">' . __('How to use?', 'uix-usercenter') . '</a>';
        return $links;
    }


    /*
	 * Get plugin slug
	 *
	 *
	 */
    public static function get_slug() {

        return dirname(plugin_basename(__FILE__));
    }



    /*
	 *  Add admin one-time notifications
	 *
	 *
	 */
    public static function usage_notice() {


        //Check if screen’s ID, base, post type, and taxonomy, among other data points
        $currentScreen = get_current_screen();

        if (
            (self::inc_str($currentScreen->id, 'uix_usercenter') || self::inc_str($currentScreen->id, 'uix-usercenter')) &&
            !self::inc_str($currentScreen->id, '_page_')
        ) {
            add_action('admin_notices', array(__CLASS__, 'usage_notice_app'));
            add_action('admin_notices', array(__CLASS__, 'template_notice_required'));
        }
    }

    public static function usage_notice_app() {

        global $current_user;
        $user_id = $current_user->ID;

        /* Check that the user hasn't already clicked to ignore the message */
        if (!get_user_meta($user_id, self::NOTICEID)) {
            echo '<div class="updated"><p>
              ' . __('Do you want to create a usercenter website with WordPress?  Learn how to add usercenter to your themes.', 'uix-usercenter') . '
              <a href="' . admin_url("admin.php?page=" . self::HELPER . "&tab=usage") . '">' . __('How to use?', 'uix-usercenter') . '</a>
               | 
          ';
            printf(__('<a href="%1$s">Hide Notice</a>'), '?post_type=uix_usercenter&' . self::NOTICEID . '=0');

            echo "</p></div>";
        }
    }

    public static function template_notice_required() {

        if (!self::tempfile_exists()) {
            echo '
              <div class="notice notice-warning">
                  <p>' . __('<strong>You could create Uix UserCenter template files in your templates directory. You can create the files on the WordPress admin panel.</strong>', 'uix-usercenter') . ' <a class="button button-primary" href="' . admin_url("admin.php?page=" . self::HELPER . "&tab=temp") . '">' . __('Create now!', 'uix-usercenter') . '</a><br>' . __('As a workaround you can use FTP, access the Uix UserCenter template files path <code>/wp-content/plugins/uix-usercenter/theme_templates/</code> and upload files to your theme templates directory <code>/wp-content/themes/{your-theme}/</code>. ', 'uix-usercenter') . '</p>
              </div>
          ';
        }
    }


    public static function nag_ignore() {
        global $current_user;
        $user_id = $current_user->ID;

        /* If user clicks to ignore the notice, add that to their user meta */
        if (isset($_GET[self::NOTICEID]) && '0' == $_GET[self::NOTICEID]) {
            add_user_meta($user_id, self::NOTICEID, 'true', true);

            if (wp_get_referer()) {
                /* Redirects user to where they were before */
                wp_safe_redirect(wp_get_referer());
            } else {
                /* This will never happen, I can almost gurantee it, but we should still have it just in case*/
                wp_safe_redirect(home_url());
            }
        }
    }

    /*
   * Checks whether a template file or directory exists
   *
   *
   */
    public static function tempfile_exists() {

        if( !file_exists( get_stylesheet_directory() . '/partials-usercenter_tmpl_nav.php' ) ) {
            return false;
        } else {
            return true;
        }
    }


    /*
   * Callback the plugin directory URL
   *
   *
   */
    public static function plug_directory() {

        return UIX_USERCENTER_PLUGIN_URL;
    }

    /*
   * Callback the plugin directory
   *
   *
   */
    public static function plug_filepath() {

        return UIX_USERCENTER_PLUGIN_DIR;
    }



    /*
   * Returns template files directory
   *
   *
   */
    public static function list_templates_name($show = 'plug') {


        $filenames = array();
        $filepath = UIX_USERCENTER_PLUGIN_DIR . 'theme_templates/';
        $themepath = get_stylesheet_directory() . '/';

        foreach (glob(dirname(__FILE__) . "/theme_templates/*") as $file) {
            $filenames[] = str_replace(dirname(__FILE__) . "/theme_templates/", '', $file);
        }

        echo '<ul>';

        foreach ($filenames as $filename) {
            $file1 = trailingslashit($filepath) . $filename;

            $file2 = trailingslashit($themepath) . $filename;

            if ($show == 'plug') {
                echo wp_kses_post('<li>' . trailingslashit($filepath) . $filename . '</li>');
            } else {
                echo wp_kses_post('<li>' . trailingslashit($themepath) . $filename . ' &nbsp;&nbsp;' . sprintf(__('<a target="_blank" href="%1$s"><i class="dashicons dashicons-welcome-write-blog"></i> Edit this template</a>', 'uix-usercenter'), admin_url('theme-editor.php?file=' . $filename)) . '</li>');
            }
        }

        echo '</ul>';
    }




    /*
   * Copy/Remove template files to your theme directory
   *
   *
   */

    public static function templates($nonceaction, $nonce, $remove = false, $ajax = false) {

        global $wp_filesystem;

        $filenames = array();
        $filepath  = UIX_USERCENTER_PLUGIN_DIR . 'theme_templates/';
        $themepath = get_stylesheet_directory() . '/';

        foreach (glob(dirname(__FILE__) . "/theme_templates/*.php") as $file) {
            $filenames[] = str_replace(dirname(__FILE__) . "/theme_templates/", '', $file);
        }


        /*
          * To perform the requested action, WordPress needs to access your web server. 
          * Please enter your FTP credentials to proceed. If you do not remember your credentials, 
          * you should contact your web host.
          *
          */
        if ($ajax) {
            ob_start();

            self::wpfilesystem_read_file($nonceaction, $nonce, self::get_theme_template_dir_name() . '/', 'tmpl-uix_usercenter.php', 'plugin');
            $out = ob_get_contents();
            ob_end_clean();

            if (!empty($out)) {
                return 0;
                exit();
            }
        }

        /*
          * File batch operation
          *
          */
        $url = wp_nonce_url($nonce, $nonceaction);

        $contentdir = $filepath;

        if (self::wpfilesystem_connect_fs($url, '', $contentdir, '')) {

            foreach ($filenames as $filename) {

                // Copy
                if (!file_exists($themepath . $filename)) {

                    $dir1 = $wp_filesystem->find_folder($filepath);
                    $file1 = trailingslashit($dir1) . $filename;

                    $dir2 = $wp_filesystem->find_folder($themepath);
                    $file2 = trailingslashit($dir2) . $filename;

                    $filecontent = $wp_filesystem->get_contents($file1);

                    $wp_filesystem->put_contents($file2, $filecontent, FS_CHMOD_FILE);
                }

                // Remove
                if ($remove) {
                    if (file_exists($themepath . $filename)) {

                        $dir = $wp_filesystem->find_folder($themepath);
                        $file = trailingslashit($dir) . $filename;

                        $wp_filesystem->delete($file, false, FS_CHMOD_FILE);
                    }
                }
            }
        }



        /*
          * Returns the system information.
          *
          */
        $div_notice_info_before    = '<p class="uix-bg-custom-info-msg"><strong><i class="dashicons dashicons-warning"></i> ';
        $div_notice_success_before = '<p class="uix-bg-custom-success-msg"><strong><i class="dashicons dashicons-yes"></i> ';
        $div_notice_error_before   = '<p class="uix-bg-custom-error-msg"><strong><i class="dashicons dashicons-no"></i> ';
        $div_notice_after  = '</strong></p>';
        $notice                    = '';
        $echo_ok_status            = '<span data-ok="1"></span>';

        if ($ajax) {
            $div_notice_info_before      = '';
            $div_notice_success_before   = '';
            $div_notice_error_before     = '';
            $div_notice_after            = '';
        }

        if (!$remove) {
            if (self::tempfile_exists()) {
                $info   = $echo_ok_status . __('Operation successfully completed!', 'uix-usercenter');
                $notice = $div_notice_success_before . $info . $div_notice_after;
                echo '<script type="text/javascript">
                             setTimeout( function(){
                                 window.location = "' . admin_url('admin.php?page=' . self::HELPER . '&tab=temp') . '";
                             }, 1000 );

                        </script>';
            } else {
                $info   = __('There was a problem copying your template files:</strong> Please check your server settings. You can upload files to theme templates directory using FTP.', 'uix-usercenter');
                $notice = $div_notice_error_before . $info . $div_notice_after;
            }
        } else {
            if (self::tempfile_exists()) {
                $info   = __('There was a problem removing your template files:</strong> Please check your server settings. You can upload files to theme templates directory using FTP.', 'uix-usercenter');
                $notice = $div_notice_error_before . $info . $div_notice_after;
            } else {
                $info   = $echo_ok_status . __('Remove successful!', 'uix-usercenter');
                $notice = $div_notice_success_before . $info . $div_notice_after;
                echo '<script type="text/javascript">
                             setTimeout( function(){
                                 window.location = "' . admin_url('admin.php?page=' . self::HELPER . '&tab=temp') . '";
                             }, 1000 );

                        </script>';
            }
        }


        return $notice;
    }



    /**
     * Initialize the WP_Filesystem
     * 
     * Example:
          
          $output = "";
          
          if ( !empty( $_POST ) && check_admin_referer( 'custom_action_nonce') ) {
              
              
                $output = UixUserCenter::wpfilesystem_write_file( 'custom_action_nonce', 'admin.php?page='.UixUserCenter::HELPER.'&tab=???', UIX_USERCENTER_PLUGIN_DIR.'helper/', 'debug.txt', 'This is test.', 'plugin' );
                echo wp_kses_post( $output );
          
          } else {
              
              wp_nonce_field( 'custom_action_nonce' );
              echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__( 'Click This Button to Copy Files', 'uix-usercenter' ).'"  /></p>';
              
          }
     *
     */
    public static function wpfilesystem_connect_fs($url, $method, $context, $fields = null) {
        global $wp_filesystem;

        if (false === ($credentials = request_filesystem_credentials($url, $method, false, $context, $fields))) {
            return false;
        }

        //check if credentials are correct or not.
        if (!WP_Filesystem($credentials)) {
            request_filesystem_credentials($url, $method, true, $context);
            return false;
        }

        return true;
    }

    public static function wpfilesystem_write_file($nonceaction, $nonce, $path, $pathname, $text, $type = 'plugin') {
        global $wp_filesystem;


        $url = wp_nonce_url($nonce, $nonceaction);

        if ($type == 'plugin') {
            $contentdir = UIX_USERCENTER_PLUGIN_DIR . $path;
        }
        if ($type == 'theme') {
            $contentdir = trailingslashit(get_stylesheet_directory()) . $path;
        }

        if (self::wpfilesystem_connect_fs($url, '', $contentdir)) {

            $dir = $wp_filesystem->find_folder($contentdir);
            $file = trailingslashit($dir) . $pathname;
            $wp_filesystem->put_contents($file, $text, FS_CHMOD_FILE);

            return true;
        } else {
            return false;
        }
    }


    public static function wpfilesystem_read_file($nonceaction, $nonce, $path, $pathname, $type = 'plugin') {
        global $wp_filesystem;

        $url = wp_nonce_url($nonce, $nonceaction);

        if ($type == 'plugin') {
            $contentdir = UIX_USERCENTER_PLUGIN_DIR . $path;
        }
        if ($type == 'theme') {
            $contentdir = trailingslashit(get_stylesheet_directory()) . $path;
        }


        if (self::wpfilesystem_connect_fs($url, '', $contentdir)) {

            $dir = $wp_filesystem->find_folder($contentdir);
            $file = trailingslashit($dir) . $pathname;


            if ($wp_filesystem->exists($file)) {

                return $wp_filesystem->get_contents($file);
            } else {
                return false;
            }
        }
    }


    public static function wpfilesystem_del_file($nonceaction, $nonce, $path, $pathname, $type = 'plugin') {
        global $wp_filesystem;

        $url = wp_nonce_url($nonce, $nonceaction);

        if ($type == 'plugin') {
            $contentdir = UIX_USERCENTER_PLUGIN_DIR . $path;
        }
        if ($type == 'theme') {
            $contentdir = trailingslashit(get_stylesheet_directory()) . $path;
        }


        if (self::wpfilesystem_connect_fs($url, '', $contentdir)) {

            $dir = $wp_filesystem->find_folder($contentdir);
            $file = trailingslashit($dir) . $pathname;


            if ($wp_filesystem->exists($file)) {

                $wp_filesystem->delete($file, false, FS_CHMOD_FILE);
                return true;
            } else {
                return false;
            }
        }
    }




    /*
   * Returns current plugin version.
   *
   *
   */
    public static function ver() {

        if (!function_exists('get_plugins')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_folder = get_plugins('/' . self::get_slug());
        $plugin_file = basename((__FILE__));
        return $plugin_folder[$plugin_file]['Version'];
    }


    /*
        * Generate a random string
        *
        * @param  {Number} $length   - Length of random characters.
        * @return {String}           - A new string.
        */
    public static function generate_random_str( $length = 4 ) {
        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $rand_str = '';
        for ($i = 0; $i < $length; $i++ ) {
            $rand_str .= $characters[rand(0, $charactersLength - 1)];
        }

        return $rand_str;

    }	
    

	/**
	 * Filters content and keeps only allowable HTML elements.
	 *
	 */
	public static function kses( $html ){
		
		return wp_kses( $html, wp_kses_allowed_html( 'post' ) );

	}

    /*
        * Get latest page ID from template
        *
        * @param  {String} $template   - Custom php template file name in the theme folder.
        * @return {Array}              - Find matching template IDs from pages in Admin Panel that have already been created.
        * @Usage
        
        $temp_ids = UixUserCenter::get_pageid_from_template( 'tmpl-???.php' );
        foreach( $temp_ids as $id ){
            echo esc_url( get_permalink( $id ) );
        }

        *
        */
    public static function get_pageid_from_template( $template = '' ) {
        
        $page_id = array();
        $pages = get_pages( array(
            'sort_order' => 'DESC',
            'meta_value' => $template
        ) );
        
        foreach( $pages as $page ){
            $page_id[] = $page->ID;
        }

        return $page_id;

    }	
    



}


add_action( 'plugins_loaded', array( 'UixUserCenter', 'init' ) );

