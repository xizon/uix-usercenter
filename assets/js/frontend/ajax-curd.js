
/*! 
 * ************************************************
 * Update User
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');

        if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);
           

            var $form = document.querySelector('form#uix-usercenter-site-updateuser');
            if ( $form === null ) return;
    
            var $stat = $form.querySelector('.status');
            var $btn = $form.querySelector('[type="submit"]');
    
            $form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                $stat.style.display = 'block';
                $stat.innerHTML = ajax_object.i18n.loadingmessage;
    
                //button status
                $form.querySelector('.status').removeAttribute('style');
                $btn.disabled = true;



                var formData = new FormData();
                formData.append('action', 'updateuser_action'); //calls wp_ajax_???????
                formData.append('user_id', $form.querySelector('#user_id').value);
                formData.append('user_login', $form.querySelector('#user_login').value);
                formData.append('user_email', $form.querySelector('#user_email').value);
                formData.append('first_name', $form.querySelector('#first_name').value);
                formData.append('last_name', $form.querySelector('#last_name').value);
                formData.append('display_name', $form.querySelector('#display_name').value);
                formData.append('user_url', $form.querySelector('#user_url').value);
                formData.append('description', $form.querySelector('#description').value);
                formData.append('security_question_1', $form.querySelector('#security_question_1').value);
                formData.append('security_answer_1', $form.querySelector('#security_answer_1').value);
                formData.append('security_question_2', $form.querySelector('#security_question_2').value);
                formData.append('security_answer_2', $form.querySelector('#security_answer_2').value);
                formData.append('pass1', $form.querySelector('#pass1').value);
                formData.append('pass2', $form.querySelector('#pass2').value);
                formData.append('security', $form.querySelector('#uix-usercenter-site-updateuser-security').value);
                
                
                axios.post(ajax_object.ajaxUrl, formData, {
                    headers: { "Authorization": 'Bearer ' + _data.token }
                }).then(function (response) {
                    var jsonData = response.data;

                    $stat.innerHTML = ajax_object.i18n[jsonData.message];
                    $stat.style.color = 'red';
                    
                    if (jsonData.status == true) {
                        $stat.style.color = 'green';
                    }

                    //button status
                    $btn.disabled = false;    

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

                });
                        
                
            });

        } // end loggedStoredObject


    });
})();




/*! 
 * ************************************************
 * Submit a website
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');

        if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);

            var $form = document.querySelector('form#uix-usercenter-site-usersubmission');
            if ( $form === null ) return;

            var $stat = $form.querySelector('.status');
            var $btn = $form.querySelector('[type="submit"]');

            $form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                $stat.style.display = 'block';
                $stat.innerHTML = ajax_object.i18n.loadingmessage;

                //button status
                $form.querySelector('.status').removeAttribute('style');
                $btn.disabled = true;


                var formData = new FormData();
                formData.append('action', 'usersubmission_action'); //calls wp_ajax_???????
                formData.append('user_id', $form.querySelector('#user_id').value);
                formData.append('user_login', $form.querySelector('#user_login').value);
                formData.append('user_email', $form.querySelector('#user_email').value);
                formData.append('user_submission_title', $form.querySelector('#user_submission_title').value);
                formData.append('user_submission_project_url', $form.querySelector('#user_submission_project_url').value);
                formData.append('security', $form.querySelector('#uix-usercenter-site-usersubmission-security').value);
                formData.append('captcha', $form.querySelector('#captcha') === null ? '' : $form.querySelector('#captcha').value);
                
         
                // Get the default captcha in `localStorage()` 
                // ( --> It could be used for different domain request)
                formData.append(ajax_object.captcha_id, localStorage.getItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id));
              


                axios.post(ajax_object.ajaxUrl, formData, {
                    headers: { "Authorization": 'Bearer ' + _data.token }
                }).then(function (response) {
                    var jsonData = response.data;

                    $stat.innerHTML = ajax_object.i18n[jsonData.message];
                    $stat.style.color = 'red';

                    if (jsonData.status == true) {
                        $stat.style.color = 'green';

                        //empty inputs
                        $form.querySelector('#user_submission_title').value = '';
                        $form.querySelector('#user_submission_project_url').value = '';
                        $form.querySelector('#captcha') === null ? '' : $form.querySelector('#captcha').value = '';

                    }

                    //button status
                    $btn.disabled = false;    

                    //refresh checkcode
                    UixCreateCaptcha(document.getElementById('uix-usercenter-refresh-session-captcha'));

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

            
        } // end loggedStoredObject


    });
})();


