<?php
if (!defined('ABSPATH')) {
    die('-1');
}

//Get custom global variables
global $uix_usercenter_global_pages;

// variables for the field and option names 
$hidden_field_name = 'submit_hidden_uix_usercenter_generalsettings';



// If they did, this hidden field will be set to 'Y'
if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {

    // Just security thingy that wordpress offers us
    check_admin_referer('uix_usercenter_generalsettings');

    // Only if administrator
    if (current_user_can('administrator')) {


        $uix_usercenter_opt_captchastyle      = sanitize_text_field($_POST['uix_usercenter_opt_captchastyle']);
        $uix_usercenter_opt_captchastr        = sanitize_text_field($_POST['uix_usercenter_opt_captchastr']);
        $uix_usercenter_opt_corsdomains       = sanitize_text_field($_POST['uix_usercenter_opt_corsdomains']);
        $uix_usercenter_opt_redirectloginurl  = sanitize_text_field($_POST['uix_usercenter_opt_redirectloginurl']);
        $uix_usercenter_opt_redirectlogouturl = sanitize_text_field($_POST['uix_usercenter_opt_redirectlogouturl']);
        $uix_usercenter_opt_captchadetect     = sanitize_text_field($_POST['uix_usercenter_opt_captchadetect']);
        $uix_usercenter_opt_autog             = sanitize_text_field($_POST['uix_usercenter_opt_autog']);
        $uix_usercenter_opt_mergescripts      = sanitize_text_field($_POST['uix_usercenter_opt_mergescripts']);
        $uix_usercenter_opt_unenqueuejs       = sanitize_text_field($_POST['uix_usercenter_opt_unenqueuejs']);
        $uix_usercenter_opt_jumploginpage     = sanitize_text_field($_POST['uix_usercenter_opt_jumploginpage']);
        $uix_usercenter_opt_removetoolbar     = sanitize_text_field($_POST['uix_usercenter_opt_removetoolbar']);
        $uix_usercenter_opt_adminpageredirect = sanitize_text_field($_POST['uix_usercenter_opt_adminpageredirect']);


        //Prevent the login page from using a template without a script
        if ($uix_usercenter_opt_unenqueuejs == 'off') {
            $uix_usercenter_opt_jumploginpage = 'off';
        }

        // Save the posted value in the database
        update_option('uix_usercenter_opt_captchastyle', $uix_usercenter_opt_captchastyle);
        update_option('uix_usercenter_opt_captchastr', $uix_usercenter_opt_captchastr);
        update_option('uix_usercenter_opt_corsdomains', $uix_usercenter_opt_corsdomains);
        update_option('uix_usercenter_opt_redirectloginurl', $uix_usercenter_opt_redirectloginurl);
        update_option('uix_usercenter_opt_redirectlogouturl', $uix_usercenter_opt_redirectlogouturl);
        update_option('uix_usercenter_opt_captchadetect', $uix_usercenter_opt_captchadetect);
        update_option('uix_usercenter_opt_autog', $uix_usercenter_opt_autog);
        update_option('uix_usercenter_opt_mergescripts', $uix_usercenter_opt_mergescripts);
        update_option('uix_usercenter_opt_unenqueuejs', $uix_usercenter_opt_unenqueuejs);
        update_option('uix_usercenter_opt_jumploginpage', $uix_usercenter_opt_jumploginpage);
        update_option('uix_usercenter_opt_removetoolbar', $uix_usercenter_opt_removetoolbar);
        update_option('uix_usercenter_opt_adminpageredirect', $uix_usercenter_opt_adminpageredirect);


        // Put a "settings saved" message on the screen
        echo '<div class="updated"><p><strong>' . __('Settings saved.', 'uix-usercenter') . '</strong></p></div>';
    }
}


if (isset($_GET['tab']) && $_GET['tab'] == 'general-settings') {


?>

    <form method="post" action="">

        <input type="hidden" name="<?php echo esc_attr( $hidden_field_name ); ?>" value="Y">
        <?php wp_nonce_field('uix_usercenter_generalsettings'); ?>


        <table class="form-table">

            <caption style="background: #e2e2e2;padding: 15px;border-bottom: 3px dotted #cecece;border-radius: 3px;font-size:1.2em;font-weight:bold;text-align:left;">
                <?php _e('Remote Requests', 'uix-usercenter'); ?>
            </caption>

            <tr>
                <th scope="row">
                    <?php _e('Allow CORS Domains', 'uix-usercenter'); ?>
                </th>
                <td>
                    <p>
                        <input type="text" class="regular-text" name="uix_usercenter_opt_corsdomains" value="<?php echo esc_attr(get_option('uix_usercenter_opt_corsdomains')); ?>">
                    </p>

                    <p class="description"><?php _e('Separate with english comma like <code>http://localhost:8888,https://example.com</code>, do not end the domain name with a slash <code>/</code>.', 'uix-usercenter'); ?></p>

                </td>

            </tr>



        </table>



        <table class="form-table">

            <caption style="background: #e2e2e2;padding: 15px;border-bottom: 3px dotted #cecece;border-radius: 3px;font-size:1.2em;font-weight:bold;text-align:left;">
                <?php _e('Captcha', 'uix-usercenter'); ?>
            </caption>


            <tr>
                <th scope="row">
                    <?php _e('Captcha Detection', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_captchadetect" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_captchadetect') ? get_option('uix_usercenter_opt_captchadetect') : 'on', 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>

                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_captchadetect" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_captchadetect'), 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>

                    </p>

                    <p class="description"><?php _e('If disabled, registration, login, publishing and other plugin pages will not require verification codes.', 'uix-usercenter'); ?></p>
                </td>

            </tr>

            <tr>
                <th scope="row">
                    <?php _e('Captcha Identifier', 'uix-usercenter'); ?>
                </th>
                <td>
                    <p>
                        <input type="text" class="regular-text" name="uix_usercenter_opt_captchastr" value="<?php echo esc_attr(get_option('uix_usercenter_opt_captchastr')); ?>">
                    </p>

                    <p class="description"><?php _e('The captcha is stored in session, and you can set its matching identifier.', 'uix-usercenter'); ?></p>

                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Captcha Style', 'uix-usercenter'); ?>
                </th>

                <td>
                
                    <p>
                        <label>
                            <input name="uix_usercenter_opt_captchastyle" type="radio" value="light" class="tog" <?php checked( get_option('uix_usercenter_opt_captchastyle') ? get_option('uix_usercenter_opt_captchastyle') : 'light', 'light' ); ?> />
                            <img src="data:image/png;base64,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">
                        </label>

                        &nbsp;&nbsp;
                        <label>
                        
                            <input name="uix_usercenter_opt_captchastyle" type="radio" value="gray" class="tog" <?php checked( get_option('uix_usercenter_opt_captchastyle'), 'gray' ); ?> />
                            <img src="data:image/png;base64,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">
                        </label>


                        &nbsp;&nbsp;
                        <label>
                        
                            <input name="uix_usercenter_opt_captchastyle" type="radio" value="dark" class="tog" <?php checked( get_option('uix_usercenter_opt_captchastyle'), 'dark' ); ?> />
                            <img src="data:image/png;base64,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">
                        </label>

                    </p>

                </td>

            </tr>



        </table>



        <table class="form-table">

            <caption style="background: #e2e2e2;padding: 15px;border-bottom: 3px dotted #cecece;border-radius: 3px;font-size:1.2em;font-weight:bold;text-align:left;">
                <?php _e('Miscellaneous', 'uix-usercenter'); ?>
            </caption>

            <tr>
                <th scope="row">
                    <?php _e('Jump URL After Login', 'uix-usercenter'); ?>
                </th>
                <td>
                    <p>
                        <input type="text" class="regular-text" name="uix_usercenter_opt_redirectloginurl" value="<?php echo esc_url(get_option('uix_usercenter_opt_redirectloginurl', (!empty($uix_usercenter_global_pages['ajax_submission_url']) ? $uix_usercenter_global_pages['ajax_submission_url'] : home_url()))); ?>">
                    </p>

                </td>

            </tr>

            <tr>
                <th scope="row">
                    <?php _e('Jump URL After Logout', 'uix-usercenter'); ?>
                </th>
                <td>
                    <p>
                        <input type="text" class="regular-text" name="uix_usercenter_opt_redirectlogouturl" value="<?php echo esc_url(get_option('uix_usercenter_opt_redirectlogouturl', home_url())); ?>">
                    </p>

                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Automatically Generate Pages', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_autog" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_autog') ? get_option('uix_usercenter_opt_autog') : 'on', 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>

                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_autog" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_autog'), 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>

                    </p>

                    <p class="description"><?php _e('Automatically generate template pages so that you can quickly find the corresponding URL. <br> You could click on Pages in your Dashboard and then click <strong>Add New Page</strong>.', 'uix-usercenter'); ?></p>
                </td>

            </tr>




            <tr>
                <th scope="row">
                    <?php _e('Merge Front-end JS Files', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_mergescripts" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_mergescripts'), 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>
                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_mergescripts" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_mergescripts') ? get_option('uix_usercenter_opt_mergescripts') : 'off', 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>

                    </p>

                    <p class="description">
                        <?php _e('Combine all front-end JS into one file.', 'uix-usercenter'); ?><br>
                        <strong><?php _e('Note: it will have a 30-minute cache, and the cache can be cleared immediately when the user login.', 'uix-usercenter'); ?></strong>
                    </p>

                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Unenqueue This Plugin\'s Script', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_unenqueuejs" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_unenqueuejs') ? get_option('uix_usercenter_opt_unenqueuejs') : 'on', 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>

                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_unenqueuejs" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_unenqueuejs'), 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>
                    </p>

                    <p class="description"><?php printf(__('After canceling the queue, you can create a new JS file for use.<br> The script refers to <a href="%s" target="_blank">%s</a>', 'uix-usercenter'), UixUserCenter::plug_directory() . 'assets/js/combine/scripts.js', UixUserCenter::plug_directory() . 'assets/js/combine/scripts.js'); ?></p>
                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Automatically Jump To Custom Login Page', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_jumploginpage" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_jumploginpage'), 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>
                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_jumploginpage" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_jumploginpage') ? get_option('uix_usercenter_opt_jumploginpage') : 'off', 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>
                    </p>



                    <p class="description"><?php _e('Check if we\'re on the login page. If so, jump to the login page template created by the plugin.', 'uix-usercenter'); ?></p>
                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Remove Toolbar for Register Members', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_removetoolbar" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_removetoolbar'), 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>
                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_removetoolbar" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_removetoolbar') ? get_option('uix_usercenter_opt_removetoolbar') : 'off', 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>
                    </p>

                </td>

            </tr>



            <tr>
                <th scope="row">
                    <?php _e('Admin Page Redirect for Member', 'uix-usercenter'); ?>
                </th>

                <td>

                    <p>
                        <label>
                            <input name="uix_usercenter_opt_adminpageredirect" type="radio" value="on" class="tog" <?php checked( get_option('uix_usercenter_opt_adminpageredirect'), 'on' ); ?> />
                            <?php _e('Enable', 'uix-usercenter'); ?>
                        </label>
                        &nbsp;&nbsp;
                        <label>
                            <input name="uix_usercenter_opt_adminpageredirect" type="radio" value="off" class="tog" <?php checked( get_option('uix_usercenter_opt_adminpageredirect') ? get_option('uix_usercenter_opt_adminpageredirect') : 'off', 'off' ); ?> />
                            <?php _e('Disable', 'uix-usercenter'); ?>
                        </label>
                    </p>

                </td>

            </tr>




        </table>


        <?php submit_button(); ?>


    </form>



<?php } ?>