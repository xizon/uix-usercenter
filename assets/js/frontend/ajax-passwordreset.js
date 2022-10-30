/*! 
 * ************************************************
 * Password Reset
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {


        var $form = document.querySelector('form#uix-usercenter-site-passwordreset');
        if ( $form === null ) return;


        var btnStep1 = $form.querySelector('#uix-usercenter-site-passwordreset__step-1-submit');
        var btnStep2 = $form.querySelector('#uix-usercenter-site-passwordreset__step-2-submit');
        var wrapperStep1 = $form.querySelector('#uix-usercenter-site-passwordreset__step-1');
        var wrapperStep2 = $form.querySelector('#uix-usercenter-site-passwordreset__step-2');
        var $stat = $form.querySelector('.status');

        /*! 
         * ########################################## (Step 1)
         */

         function _curFun(e) {
            e.preventDefault();
            
            $stat.style.display = 'block';
            $stat.innerHTML = ajax_object.i18n.loadingmessage;

            //button status
            $form.querySelector('.status').removeAttribute('style');
            btnStep1.disabled = true;
           

            var formData = new FormData();
            formData.append('action', 'passwordreset_action'); //calls wp_ajax_nopriv_???????
            formData.append('email', $form.querySelector('#email').value);
            formData.append('security', $form.querySelector('#uix-usercenter-site-passwordreset-security').value);
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

                    wrapperStep1.style.display = 'none';
                    wrapperStep2.style.display = 'block';
                    $form.querySelector('#uix-usercenter-site-passwordreset__step-2-q1').innerHTML = ajax_object.i18n['security_questions'][jsonData.security_question_1];
                    $form.querySelector('#uix-usercenter-site-passwordreset__step-2-q2').innerHTML = ajax_object.i18n['security_questions'][jsonData.security_question_2];

                } else {
                    //button status
                    btnStep1.disabled = false;

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
                btnStep1.disabled = false;
                
                //refresh checkcode
                UixCreateCaptcha(document.getElementById('uix-usercenter-refresh-session-captcha'));


            });
         }


         if ( btnStep1 !== null ) {
            btnStep1.removeEventListener('click', _curFun);
            btnStep1.addEventListener('click', _curFun);
         }

  

        /*! 
         * ########################################## (Step 2)
         */
         function _curFun2(e) {
            e.preventDefault();
            
            $stat.style.display = 'block';
            $stat.innerHTML = ajax_object.i18n.loadingmessage;

            //button status
            $form.querySelector('.status').removeAttribute('style');
            btnStep2.disabled = true;
                        

            var formData = new FormData();
            formData.append('action', 'passwordreset_verify_action'); //calls wp_ajax_nopriv_???????
            formData.append('email', $form.querySelector('#email').value);
            formData.append('security_answer_1', $form.querySelector('#security_answer_1').value);
            formData.append('security_answer_2', $form.querySelector('#security_answer_2').value);
            formData.append('security', $form.querySelector('#uix-usercenter-site-passwordreset-security').value);
            
            axios.post(ajax_object.ajaxUrl, formData, {}).then(function (response) {
                var jsonData = response.data;
                
                $stat.innerHTML = ajax_object.i18n[jsonData.message];
                $stat.style.color = 'red';

                if (jsonData.status == true) {
                    $stat.style.color = 'green';
                    $stat.innerHTML = ajax_object.i18n[jsonData.message] + '<strong>'+jsonData.newpass+'</strong>';
                    
                    btnStep2.style.display = 'none';
                } else {
                    //button status
                    btnStep2.disabled = false;
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
                btnStep2.disabled = false;

            });

        }

         if ( btnStep2 !== null ) {
            btnStep2.removeEventListener('click', _curFun2);
            btnStep2.addEventListener('click', _curFun2);  
         }


  
        
    });
})();


