<?php
/**
 * Navigation of UserCenter Custom Templats
 * 
 */

/**
 * Get custom global variables
 */
global $uix_usercenter_global_pages;
global $uix_usercenter_global_ajax_object;

?>

<style>

    /* General */
    .uix-usercenter-container {
        margin: 0 auto;
        max-width: 1200px;
        padding: 15px;
    }
    .uix-usercenter-row {
        display: flex;
    }
    .uix-usercenter-col-12 {
        width: 100%;
    }
    .uix-usercenter-col-md-8 {
        width: 66.6666666666667%;
    }
    .uix-usercenter-col-md-4 {
        width: 33.3333333333333%;
    }

    /** Tabs */
    .uix-usercenter-tabs .uix-usercenter-tabs__nav ul,
    .uix-usercenter-tabs .uix-usercenter-tabs__nav li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .uix-usercenter-tabs .uix-usercenter-tabs__nav ul {
        display: flex;
        justify-content: center;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }

    .uix-usercenter-tabs .uix-usercenter-tabs__nav li {
        flex: 1;
        border-bottom: 1px solid #ddd;
        text-align: center;
        padding: 10px 0;
    }

    .uix-usercenter-tabs .uix-usercenter-tabs__nav li.is-active {
        border-color: #333;
        font-weight: bold;
    }

    .uix-usercenter-tabs .uix-usercenter-tabs__content {
        display: none;
        ;
    }

    .uix-usercenter-tabs .uix-usercenter-tabs__content.is-active {
        display: block;
        ;
    }


    /** Forms Layout */
    dl.uix-usercenter-formside {
        width: 100%;
        /* adjust the width; make sure the total of both is 100% */
        font-size: 0.875rem;
        margin-bottom: 0;
        /* Icon List */
    }

    dl.uix-usercenter-formside input,
    dl.uix-usercenter-formside textarea,
    dl.uix-usercenter-formside select {
        max-width: 99%;
    }

    dl.uix-usercenter-formside dt {
        float: left;
        clear: left;
        width: 120px;
        padding: 15px 0;
        margin: 0;
        font-weight: 600;
        text-align: left;
    }

    dl.uix-usercenter-formside dt i {
        color: orange;
    }

    dl.uix-usercenter-formside dd {
        float: left;
        width: calc(100% - 120px);
        padding: 15px 0;
        margin: 0;
        text-align: left;
        padding-left: 1rem;
        word-break: break-all;
    }

    @media all and (max-width: 768px) {
        dl.uix-usercenter-formside dt {
            width: 100%;
            padding-bottom: 0;
        }
        dl.uix-usercenter-formside dd {
            width: 100%;
            padding-bottom: 0;
            padding-left: 0;
        }
    }

    dl.uix-usercenter-formside dd p {
        margin-bottom: 0.2rem;
    }

    dl.uix-usercenter-formside::after {
        content: "";
        display: table;
        clear: both;
    }

    dl.uix-usercenter-formside.uix-usercenter-formside--icon dt {
        width: 30px;
    }

    dl.uix-usercenter-formside.uix-usercenter-formside--icon dt i {
        font-size: 1.5rem;
    }

    dl.uix-usercenter-formside.uix-usercenter-formside--icon dd {
        width: calc(100% - 30px);
    }

    dl.uix-usercenter-formside input[type=submit] {
        background: #24272a!important;
        border: none!important;
        border-radius: 35px!important;
        color: #fff!important;
        margin: 0!important;
        padding: 10px 25px!important;
        width: auto!important;
    }


    /** Navigation */
    .uix-usercenter-nav a {
        display: inline-block;
        padding: 2px 15px;
        font-size: 0.875rem;
        border: 1px solid #ddd;
        color: #fff;
        border-radius: 25px;
        background: #101010;
        margin-bottom: 10px;
    }

    .uix-usercenter-nav .uix-usercenter-nav__logout {
        background-color: #c83434;
    }


    .uix-usercenter-nav__wrapper {
        background: #fafafa;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        padding: 15px 15px 5px 15px;
    }

</style>

<script>

function UixSimpleTabs(element) {
    const tabsContent = document.querySelector(element);
    if ( tabsContent !== null ) {
        tabsContent.querySelectorAll(".uix-usercenter-tabs__nav li").forEach((tablink, index) => tablink.addEventListener("click", e => {
            e.preventDefault();
            tabsContent.querySelectorAll(".uix-usercenter-tabs__content").forEach(el => el.classList.remove("is-active"));
            tabsContent.querySelectorAll(".uix-usercenter-tabs__content")[index].classList.add("is-active");
            tabsContent.querySelectorAll(".uix-usercenter-tabs__nav li").forEach(el => el.classList.remove("is-active"));
            tablink.classList.add("is-active");
        }));
    }
}

(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {
        UixSimpleTabs(".uix-usercenter-tabs");
    });
})();


</script>

<?php if (is_user_logged_in()) { ?>
<div class="uix-usercenter-nav__wrapper">
    <div class="uix-usercenter-row">
        <div class="uix-usercenter-col-md-8">

            <?php 
            $user = wp_get_current_user(); 
            $user_name = $user->user_login;
            $user_role = $user->roles[0];
            $user_id   = $user->ID;


            //
            $security_answer_1 = get_the_author_meta( 'security_answer_1', $user_id );
            $security_answer_2 = get_the_author_meta( 'security_answer_2', $user_id )


            ?>



            <?php printf( esc_html__( __( 'Welcome, %s !', 'uix-usercenter' ) ), $user_name ); ?>&nbsp;
            <?php printf( esc_html__( __( 'You are %s.', 'uix-usercenter' ) ), $user_role ); ?>
            &nbsp;&nbsp;
            
            <?php if ( empty( $security_answer_1 ) && empty( $security_answer_2 ) ) : ?>
                <p><i>
                <?php echo esc_html( $uix_usercenter_global_ajax_object['i18n']['security_tip'] ); ?>			  
                 </i></p>
            <?php endif; ?>  

        </div>
        <div class="uix-usercenter-col-md-4">
            
            <div class="uix-usercenter-nav">
                    <a href="<?php echo esc_url( $uix_usercenter_global_pages['ajax_submission_url'] ); ?>"><?php esc_html_e( 'My Submission', 'uix-usercenter' ); ?></a>

                    <a href="<?php echo esc_url( $uix_usercenter_global_pages['ajax_profile_url'] ); ?>"><?php esc_html_e( 'Edit Profile', 'uix-usercenter' ); ?></a>

                    <a class="uix-usercenter-nav__logout js-uix-usercenter-logout-btn--ajax" href="#"><?php esc_html_e( 'Logout', 'uix-usercenter' ); ?></a>


            </div>

        </div>

        


    </div>
</div>

<?php } ?>