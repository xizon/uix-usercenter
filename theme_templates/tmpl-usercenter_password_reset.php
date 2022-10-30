<?php
/**
 * Template Name: UserCenter Password Reset via Security Question(s)
 *
 *
 */

/**
 * Get custom global variables
 */
global $uix_usercenter_global_pages;


get_header(); ?>


<!-- Password Reset
    ====================================================== -->
<section>
    <div class="uix-usercenter-container">
        <div class="uix-usercenter-row">
            <div class="uix-usercenter-col-12">

            <?php get_template_part('partials', 'usercenter_tmpl_nav'); ?>

            <?php if (is_user_logged_in()) { ?>
                <?php esc_html_e('You are already logged in', 'uix-usercenter'); ?>, <a class="js-uix-usercenter-logout-btn" href="<?php echo esc_url( wp_logout_url(home_url()) ); ?>"><?php esc_html_e( 'Logout', 'uix-usercenter' ); ?></a>
            <?php } else { ?>
                <form id="uix-usercenter-site-passwordreset" method="post" style="padding: 30px; width: 320px; max-width: 100%; margin: 0 auto;">

                    <h3><?php esc_html_e( 'Password Reset', 'uix-usercenter' ); ?></h3>
                    <p class="status"></p>

                    <div id="uix-usercenter-site-passwordreset__step-1">
                        <!-- ######################################## -->
                        
                        <p>
                            <?php esc_html_e( 'Please enter your email address to continue to the next step.', 'uix-usercenter' ); ?>

                        </p> 

                        <p>
                            <label for="email"><?php esc_html_e( 'Email', 'uix-usercenter' ); ?></label><br />
                            <input id="email" type="email" name="email" style="width: 98%">
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
                            <input id="uix-usercenter-site-passwordreset__step-1-submit" type="button" value="<?php esc_html_e( 'Go on', 'uix-usercenter' ); ?>">

                        </p>

                        <!-- ######################################## -->
                    </div><!-- #uix-usercenter-site-passwordreset__step-1 -->


                    <div id="uix-usercenter-site-passwordreset__step-2" style="display: none;">
                        <!-- ######################################## -->

                        <p>
                            <?php esc_html_e( 'You will be prompted with the security question(s) provided by you. Answer those to authenticate the password reset process. Password can be reset if any one is correct.', 'uix-usercenter' ); ?>
                        </p>

                        <p>
                            <strong><?php esc_html_e( 'Question 1', 'uix-usercenter' ); ?></strong>
                            <span id="uix-usercenter-site-passwordreset__step-2-q1"></span>
                        </p>   
                        <p>
                            <label for="security_answer_1"><?php esc_html_e( 'Answer', 'uix-usercenter' ); ?></label><br />
                            <input id="security_answer_1" type="text" name="security_answer_1" style="width: 98%">
                        </p>   
                        
                        <p>
                            <strong><?php esc_html_e( 'Question 2', 'uix-usercenter' ); ?></strong>
                            <span id="uix-usercenter-site-passwordreset__step-2-q2"></span>
                        </p>                   
                        <p>
                            <label for="security_answer_2"><?php esc_html_e( 'Answer', 'uix-usercenter' ); ?></label><br />
                            <input id="security_answer_2" type="text" name="security_answer_2" style="width: 98%">
                        </p>   

                        <p>
                            <input id="uix-usercenter-site-passwordreset__step-2-submit" type="button" value="<?php esc_html_e( 'Verify', 'uix-usercenter' ); ?>">

                        </p>

                        <!-- ######################################## -->
                    </div><!-- #uix-usercenter-site-passwordreset__step-2 -->


                    <!-- Create nonce -->
                    <input type="hidden" name="uix-usercenter-site-passwordreset-security" id="uix-usercenter-site-passwordreset-security" value="">

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