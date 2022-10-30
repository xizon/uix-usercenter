<?php

/**
 * Template Name: UserCenter Submission
 *
 *
 */

 
/**
 * Get custom global variables
 */
global $uix_usercenter_global_pages;

/**
 * Redirects users to the login page if the user is not logged in
 * Using location replace, this will replace the current history of the page, 
 * means that it is not possible to use the back button to go back to the original page.
 */
if (!is_user_logged_in()) {

    if (!wp_doing_ajax()) {
        wp_safe_redirect($uix_usercenter_global_pages['ajax_login_url']);
    } else {
        echo '<script>window.location.replace( "' . esc_url($uix_usercenter_global_pages['ajax_login_url']) . '" );</script>';
    }
    exit;
}


get_header(); ?>


<script src="<?php echo esc_url(UixUserCenter::plug_directory() .'assets/js/frontend/jwt-auth.js'); ?>"></script>


<!-- Submission
	====================================================== -->
<section>
    <div class="uix-usercenter-container">
        <div class="uix-usercenter-row">
            <div class="uix-usercenter-col-12">


                <?php get_template_part('partials', 'usercenter_tmpl_nav'); ?>


                    <div class="uix-usercenter-tabs"">
                        <div class=" uix-usercenter-tabs__nav">
                            <ul role="tablist">
                                <li role="presentation" class="is-active"><a href="javascript:void(0);"><?php esc_html_e('Submission List', 'uix-usercenter'); ?></a></li>
                                <li role="presentation"><a href="javascript:void(0);"><?php esc_html_e('Submit a website', 'uix-usercenter'); ?></a></li>
                            </ul>
                        </div><!-- /.uix-usercenter-tabs__nav -->

                        <div role="tabpanel" class="uix-usercenter-tabs__content is-active">
                            <script src="<?php echo esc_url(UixUserCenter::plug_directory() .'assets/js/frontend/get-list.js'); ?>"></script>
                            <ol id="uix-usercenter-submission-list"></ol>
                        </div>
                        
                        <div role="tabpanel" class="uix-usercenter-tabs__content">
                        

                            <?php
                            if (have_posts()) {

                                while (have_posts()) : the_post();
                            ?>

                            <?php $current_user = wp_get_current_user(); ?>


                            <form id="uix-usercenter-site-usersubmission" method="post">

                                <p><?php esc_html_e('If your submitted site meets the quality requirements, it will be reviewed and approved within 7 days, thank you for your recommendation.', 'uix-usercenter'); ?></p>


                                <dl class="uix-usercenter-formside">

                                    <dt><?php esc_html_e('Title', 'uix-usercenter'); ?></dt>
                                    <dd>
                                        <input autocomplete="off" type="text" size="50" name="user_submission_title" id="user_submission_title" value="">
                                    </dd>

                                    <dt><?php esc_html_e('Project URL', 'uix-usercenter'); ?></dt>
                                    <dd>
                                        <input autocomplete="off" type="url" size="50" name="user_submission_project_url" id="user_submission_project_url" value="">
                                    </dd>


                                    <?php if ( get_option( 'uix_usercenter_opt_captchadetect' ) == 'on' || !get_option( 'uix_usercenter_opt_captchadetect' ) ) { ?>
                    
                                    <dt class="uix-usercenter-captcha-section"><?php esc_html_e('Captcha', 'uix-usercenter'); ?></dt>
                                    <dd class="uix-usercenter-captcha-section">
                                        <input autocomplete="off" id="captcha" type="text" size="50" value="" name="captcha">
                  
                                        <br />

                                        <span id="uix-usercenter-refresh-session-captcha"></span>


                                    </dd>
                                        
                                    <?php } ?>


                                </dl>

                                <dl class="uix-usercenter-formside">

                                    <dt>&nbsp;</dt>
                                    <dd>

                                        <input  type="submit" value="<?php esc_html_e( 'Submit', 'uix-usercenter' ); ?>">
                                        
                                        <p class="status"></p>
                                    </dd>

                                </dl>



                                <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>">
                                <input type="hidden" name="user_login" id="user_login" value="<?php the_author_meta('user_login', $current_user->ID); ?>">
                                <input type="hidden" name="user_email" id="user_email" value="<?php the_author_meta('user_email', $current_user->ID); ?>">


                                <!-- Create nonce -->
                                <input type="hidden" name="uix-usercenter-site-usersubmission-security" id="uix-usercenter-site-usersubmission-security" value="">

                            </form>

                            <?php
                                endwhile;
                                wp_reset_postdata();
                            }
                            ?>



                        </div>


                    </div>
                    <!-- .uix-usercenter-tabs end -->




            </div>
            <!-- .uix-usercenter-col-12 end -->
        </div>
        <!-- .row end -->


    </div>
    <!-- .container end -->




</section>


<?php get_footer(); ?>