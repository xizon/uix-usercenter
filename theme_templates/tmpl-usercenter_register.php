<?php
/**
 * Template Name: UserCenter Register
 *
 *
 */

/**
 * Get custom global variables
 */
global $uix_usercenter_global_pages;


get_header(); ?>


<!-- Register
    ====================================================== -->
<section>
    <div class="uix-usercenter-container">
        <div class="uix-usercenter-row">
            <div class="uix-usercenter-col-12">

            <?php get_template_part('partials', 'usercenter_tmpl_nav'); ?>

            <?php if (is_user_logged_in()) { ?>
                <?php esc_html_e('You are already logged in', 'uix-usercenter'); ?>, <a class="js-uix-usercenter-logout-btn" href="<?php echo wp_logout_url(home_url()); ?>"><?php esc_html_e( 'Logout', 'uix-usercenter' ); ?></a>
            <?php } else { ?>
                <form id="uix-usercenter-site-register" method="post" style="padding: 30px; width: 320px; max-width: 100%; margin: 0 auto;">
                    <h3><?php esc_html_e( 'Register New Account', 'uix-usercenter' ); ?></h3>
                    <p class="status"></p>

                    <p>
                        <label for="username"><?php esc_html_e( 'Username', 'uix-usercenter' ); ?></label><br />
                        <input id="username" type="text" name="username" style="width: 98%">
                    </p>
                    <p>
                        <label for="email"><?php esc_html_e( 'Email', 'uix-usercenter' ); ?></label><br />
                        <input id="email" type="email" name="email" style="width: 98%">
                    </p>

                    <p>
                        <label for="password"><?php esc_html_e( 'Password', 'uix-usercenter' ); ?></label><br />
                        <input id="password" type="password" name="password" style="width: 98%">
                    </p>

                    <p>
                        <label for="password_confirm"><?php esc_html_e( 'Confirm Password', 'uix-usercenter' ); ?></label>
                        <input id="password_confirm" type="password" name="password_confirm" style="width: 98%">
                    </p>


                    <?php if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'on' || !get_option( 'uix_usercenter_opt_captchadetect' ) ) { ?>
    
                        <p class="uix-usercenter-captcha-section">
                            <label for="captcha"><?php esc_html_e('Captcha', 'uix-usercenter'); ?></label><br />

                            <input id="captcha" type="text" value="" name="captcha" style="width: 98%">
                            <br />

                            <span id="uix-usercenter-refresh-session-captcha"></span>
                        </p>
                        
                    <?php } ?>


                    <p>

                        <input  type="submit" value="<?php esc_attr_e( 'Register', 'uix-usercenter' ); ?>">
                    
                        &nbsp;&nbsp;
                        <span>
                            <a href="<?php echo esc_url( $uix_usercenter_global_pages['ajax_login_url'] ); ?>"><?php esc_html_e( 'Log In', 'uix-usercenter' ); ?></a>
                        </span>
                </p>
        
                    <!-- Create nonce -->
                    <input type="hidden" name="uix-usercenter-site-register-security" id="uix-usercenter-site-register-security" value="">



                </form>
            <?php } ?>

            </div>
            <!-- .uix-usercenter-col-12 end -->
    </div>
    <!-- .row end -->

    </div>
    <!-- .container end -->
</section>


<?php get_footer(); ?>