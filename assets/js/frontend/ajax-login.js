

/*! 
 * ************************************************
 * Login via AJAX
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var $form = document.querySelector('form#uix-usercenter-site-login');
        if ( $form === null ) return;

        var $stat = $form.querySelector('.status');
        var $btn = $form.querySelector('[type="submit"]');

        $form.addEventListener('submit', function(e) {

            e.preventDefault();

            $stat.style.display = 'block';
            $stat.innerHTML = ajax_object.i18n.loadingmessage;
        
            //button status
            $btn.disabled = true;

            //rememberme
            var remembermeVal = $form.querySelector('#rememberme') !== null ? $form.querySelector('#rememberme').checked : false;
    
            var formData = new FormData();
            formData.append('action', 'login_action'); //calls wp_ajax_nopriv_???????
            formData.append('user', $form.querySelector('#user').value);
            formData.append('password', $form.querySelector('#password').value);
            formData.append('security', $form.querySelector('#uix-usercenter-site-login-security').value);
            formData.append('rememberme', remembermeVal);
            formData.append('captcha', $form.querySelector('#captcha') === null ? '' : $form.querySelector('#captcha').value);

            // Get the default captcha in `localStorage()` 
            // ( --> It could be used for different domain request)
            formData.append(ajax_object.captcha_id, localStorage.getItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id));


            axios.post(ajax_object.ajaxUrl, formData, {}).then(function (response) {
                var jsonData = response.data;

                $stat.innerHTML = ajax_object.i18n[jsonData.message];
                $stat.style.color = 'red';

                if (jsonData.status == true) {
                    $stat.style.color = 'green';

                    // save Authentication info
                    localStorage.setItem('UIX_USERCENTER_DATA_SITE_LOGIN',JSON.stringify({
                        root: jsonData.wpApiRoot,
                        token: jsonData.token
                    }));

                    // Expiration time for sync with WordPress
                    // ( --> It could be used for different domain request)
                    if ( remembermeVal == true ) {
                        UixSetCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE', jsonData.token, '/', 14);
                    } else {
                        UixSetCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE', jsonData.token, '/', 'Session');
                    }
                    

                    
                    // 
                    document.location.href = ajax_object.redirecturl_login;

                } else {
                    //button status
                    $btn.disabled = false;

                    //refresh checkcode
                    UixCreateCaptcha(document.getElementById('uix-usercenter-refresh-session-captcha'));
                }


            }).catch(function (error) {
                if (error.response) {
                    $stat.innerHTML = error.response.status;
                    $stat.style.color = 'red';
                } else if (error.request) {
                    $stat.innerHTML = error.request;
                    $stat.style.color = 'red';
                } else {
                    $stat.innerHTML = error.message;
                    $stat.style.color = 'red'; 
                }
                //button status
                $btn.disabled = false;

                //refresh checkcode
                UixCreateCaptcha(document.getElementById('uix-usercenter-refresh-session-captcha'));

            });

            
        });


    });
})();

