<?php

/**
 * Template Name: UserCenter Profile
 *
 *
 */

/**
 * Get custom global variables
 */
global $uix_usercenter_global_pages;
global $uix_usercenter_global_ajax_object;


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


<!-- Profile
	====================================================== -->
<section>
    <div class="uix-usercenter-container">
        <div class="uix-usercenter-row">
            <div class="uix-usercenter-col-12">


            <?php get_template_part('partials', 'usercenter_tmpl_nav'); ?>


                <?php
                if (have_posts()) {

                    while (have_posts()) : the_post();
                ?>


                        <?php $current_user = wp_get_current_user(); ?>

                        <!-- Form
                            ========================= -->
                        <form id="uix-usercenter-site-updateuser" method="post">


                            <div class="uix-usercenter-tabs"">
                                <div class=" uix-usercenter-tabs__nav">
                                    <ul role="tablist">
                                        <li role="presentation" class="is-active"><a href="javascript:void(0);"><?php esc_html_e('Personal info', 'uix-usercenter'); ?></a></li>
                                        <li role="presentation"><a href="javascript:void(0);"><?php esc_html_e('Security Questions', 'uix-usercenter'); ?></a></li>
                                        <li role="presentation"><a href="javascript:void(0);"><?php esc_html_e('Change password', 'uix-usercenter'); ?></a></li>
                                    </ul>
                                </div><!-- /.uix-usercenter-tabs__nav -->


                                <div role="tabpanel" class="uix-usercenter-tabs__content is-active">

                                    <dl class="uix-usercenter-formside">

                                        <dt><?php esc_html_e('Username', 'uix-usercenter'); ?></dt>
                                        <dd>

                                        <input type="text" size="50" name="user_login" id="user_login" value="<?php the_author_meta('user_login', $current_user->ID); ?>" disabled>
                                        </dd>



                                        <dt><?php esc_html_e('E-mail', 'uix-usercenter'); ?><i>*</i></dt>
                                        <dd>
                                            <input type="email" size="50" name="user_email" id="user_email" value="<?php the_author_meta('user_email', $current_user->ID); ?>" required>
                                        </dd>



                                        <dt><?php esc_html_e('First Name', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="text" size="50" name="first_name" id="first_name" value="<?php the_author_meta('first_name', $current_user->ID); ?>">
                                        </dd>

                                        <dt><?php esc_html_e('Last Name', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="text" size="50" name="last_name" id="last_name" value="<?php the_author_meta('last_name', $current_user->ID); ?>">
                                        </dd>



                                        <dt><?php esc_html_e('Display Name', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="text" size="50" name="display_name" id="display_name" value="<?php the_author_meta('display_name', $current_user->ID); ?>">
                                        </dd>

                                        <dt><?php esc_html_e('Website', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="url" size="50" name="user_url" id="user_url" value="<?php the_author_meta('user_url', $current_user->ID); ?>">
                                        </dd>



                                        <dt><?php esc_html_e('Description', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <textarea rows="4" cols="70" name="description" id="description"><?php the_author_meta('description', $current_user->ID); ?></textarea>
                                        </dd>

                                    </dl>



                                </div>
                                <div role="tabpanel" class="uix-usercenter-tabs__content">


                                    <p><?php esc_html_e('Resetting your password by authenticating via Security question(s) & answer method.', 'uix-usercenter'); ?></p>

                                    <?php

                                    $security_question_1 = get_the_author_meta('security_question_1', $current_user->ID);
                                    $options_1 = '';
                                    foreach ($uix_usercenter_global_ajax_object['i18n']['security_questions'] as $key => $value) {
                                        $options_1 .= "<option value=\"$key\" " . ($security_question_1 == $key ? 'selected' : '') . ">$value</option>" . PHP_EOL;
                                    }

                                    $security_question_2 = get_the_author_meta('security_question_2', $current_user->ID);
                                    $options_2 = '';
                                    foreach ($uix_usercenter_global_ajax_object['i18n']['security_questions'] as $key => $value) {
                                        $options_2 .= "<option value=\"$key\" " . ($security_question_2 == $key ? 'selected' : '') . ">$value</option>" . PHP_EOL;
                                    }


                                    ?>

                                    <dl class="uix-usercenter-formside">


                                        <dt><?php esc_html_e('Question 1', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <select name="security_question_1" id="security_question_1">
                                                <?php
                                                $security_question_1 = get_the_author_meta('security_question_1', $current_user->ID);
                                                foreach ($uix_usercenter_global_ajax_object['i18n']['security_questions'] as $key => $value) {
                                                ?>
                                                    <?php if ( $security_question_1 == $key ) { ?>
                                                        <option value="<?php echo esc_attr($key); ?>" selected><?php echo esc_html($value); ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                                                    <?php } ?>

                                                <?php } ?>
                                            </select>
                                        </dd>


                                        <dt><?php esc_html_e('Answer', 'uix-usercenter'); ?></dt>
                                        <dd>
                                        <input type="text" size="70" name="security_answer_1" id="security_answer_1" value="<?php the_author_meta('security_answer_1', $current_user->ID); ?>">
                                        </dd>


                                        <dt><?php esc_html_e('Question 2', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <select name="security_question_2" id="security_question_2">
                                                <?php
                                                $security_question_2 = get_the_author_meta('security_question_2', $current_user->ID);
                                                foreach ($uix_usercenter_global_ajax_object['i18n']['security_questions'] as $key => $value) {
                                                ?>
                                                    <?php if ( $security_question_2 == $key ) { ?>
                                                        <option value="<?php echo esc_attr($key); ?>" selected><?php echo esc_html($value); ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                                                    <?php } ?>

                                                <?php } ?>
                                            
                                            </select>
                                        </dd>


                                        <dt><?php esc_html_e('Answer', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="text" size="70" name="security_answer_2" id="security_answer_2" value="<?php the_author_meta('security_answer_2', $current_user->ID); ?>">
                                        </dd>


                                    </dl>


                                </div>
                                <div role="tabpanel" class="uix-usercenter-tabs__content">


                                    <p><?php esc_html_e('When both password fields are left empty, your password will not change', 'uix-usercenter'); ?></p>

                                    <dl class="uix-usercenter-formside">


                                        <dt><?php esc_html_e('Password', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="password" size="50" name="pass1" id="pass1">
                                        </dd>


                                        <dt><?php esc_html_e('Confirm Password', 'uix-usercenter'); ?></dt>
                                        <dd>
                                            <input type="password" size="50" name="pass2" id="pass2">
                                        </dd>

                                    </dl>

                                </div>


                            </div>
                            <!-- .uix-usercenter-tabs end -->


                            <dl class="uix-usercenter-formside">

                                <dt>&nbsp;</dt>
                                <dd>

                                    <input  type="submit" value="<?php esc_html_e( 'Update', 'uix-usercenter' ); ?>">
                                    
                                    <p class="status"></p>
                                </dd>

                            </dl>

                            
                            <input type="hidden" name="user_id"  id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>">

                            <!-- Create nonce -->
                            <input type="hidden" name="uix-usercenter-site-updateuser-security" id="uix-usercenter-site-updateuser-security" value="">
                     
                            

                        </form>


                <?php
                    endwhile;
                    wp_reset_postdata();
                }
                ?>

        </div>
        <!-- .uix-usercenter-col-12 end -->
    </div>
    <!-- .row end -->


    </div>
    <!-- .container end -->




</section>


<?php get_footer(); ?>