/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/utils.js ----*/
var ajax_object = {"ajaxUrl":"http:\/\/localhost:8888\/1\/wp-admin\/admin-ajax.php","redirecturl_login":"http:\/\/localhost:8888\/1\/custom-submission\/","redirecturl_logout":"http:\/\/localhost:8888\/1","captcha_detect":"on","captcha_id":"uix_usercenter_captcha_validate_session","i18n":{"captcha_getstr":"Get Captcha","security_questions":{"1":"What is the first name of your best friend in high school?","2":"What was the first film you saw in the theatre?","3":"City where you met your other half?","4":"What is the first name of the person you first kissed?","5":"What was the name of your elementary \/ primary school?","6":"In what city or town does your nearest sibling live?","7":"What time of the day were you born? (hh:mm)","8":"What is your pet&#039;s name?","9":"What is the name of your favourite band or singer?","10":"In what year was your mother born?"},"loadingmessage":"Sending, please wait...","review":"reviewing","defaultcontent":"loading...","none":"You have no content.","profile_updated":"Profile successfully updated.","mail_invalid":"Invalid e-mail address.","mail_exist":"Email already exists.","mail_no_registered":"There is no user registered with that email address.","mail_correct":"Email verification is correct.","pass_notmatch":"The passwords entered twice do not match.","pass_display_callback":{},"pass_display":"Your new password is: ","url_invalid":"It is NOT valid URL.","title_empty":"The title can not be blank.","captcha_invalid":"Please enter correct captcha.","captcha_empty":"Please enter the captcha.","send_ok":"Your submission was successful. Thank you!","send_failure":"Data update failure.","login_ok":"Login successful, redirecting...","logout_ok":"Logout successful, redirecting...","name_mail_pass_invalid":"Wrong username, e-mail address or password.","name_pass_empty":"Username and password cannot be empty.","name_exist":"Username already exists.","unauthorized":"Unauthorized","security_tip":"Please modify your Password &amp; Security Questions to login next time."}};


/*! 
* ************************************************
* Set a global variable, exposing it for easy access
************************************************
*/
window['uix-usercenter-listener-func'] = {};


/*! 
* ************************************************
* Expiration time for sync with WordPress
* ( --> It could be used for different domain request)
************************************************
*/
(function() {
    "use strict";

    var expiration = UixGetCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE');
    if ( expiration == null || expiration == 0 ) {
        localStorage.removeItem('UIX_USERCENTER_DATA_SITE_LOGIN');
        localStorage.removeItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id);
    }

})();


/*! 
* ************************************************
* Determine whether the request is for a different domain name
************************************************
*/
function UixCrossDomain() {
    return new URL(ajax_object.ajaxUrl).host != new URL(window.location.href).host;
}



/*! 
* ************************************************
* Cookie
************************************************
*/
function UixSetCookie(name, value) {
    var path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "/";
    var days = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);

    document.cookie = ""
        .concat(name, "=")
        .concat(value, ";expires=")
        .concat(exp.toGMTString(), ";path=")
        .concat(path);


}



function UixGetCookie(name) {
    let arr;
    const reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg)) {
        return arr[2];
    } else {
        return null;
    }

}

function UixDelCookie(name) {
    var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "/";
    var cval = UixGetCookie(name);
    if (cval != null) {
      document.cookie = ""
        .concat(name, "=")
        .concat(cval, ";expires=")
        .concat(new Date(0).toUTCString(), ";path=")
        .concat(path);
    }
  }
/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-createnonce.js ----*/

/*! 
* ************************************************
* Create nonce via AJAX
************************************************
*/
function UixCreateNonceToInput(input, name) {

    if (input === null) return;
    
    var formData = new FormData();
    formData.append('action', 'createnonce_action');
    formData.append('nonce_name', name);

    axios.post(ajax_object.ajaxUrl, formData, {
        headers: { 'Content-Type':  'multipart/form-data' }
    }).then(function (response) {
        var jsonData = response.data;

        //console.log(name, '-->', jsonData);
        input.value = jsonData.form_nonce;


    }).catch(function (error) {
        if (error.response) {
            console.log(error.response.status);
        } else if (error.request) {
            console.log(error.request);
        } else {
            console.log(error.message);
        }
    });
}


(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {
        UixCreateNonceToInput(document.getElementById('uix-usercenter-site-login-security'), 'ajax-login-nonce');
        UixCreateNonceToInput(document.getElementById('uix-usercenter-site-updateuser-security'), 'ajax-updateuser-nonce');
        UixCreateNonceToInput(document.getElementById('uix-usercenter-site-register-security'), 'ajax-register-nonce');
        UixCreateNonceToInput(document.getElementById('uix-usercenter-site-usersubmission-security'), 'ajax-usersubmission-nonce');
        UixCreateNonceToInput(document.getElementById('uix-usercenter-site-passwordreset-security'), 'ajax-passwordreset-nonce');
    });
})();


/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-createcaptcha.js ----*/

/*! 
* ************************************************
* Create captcha via AJAX
************************************************
*/

function UixCreateCaptcha(captchaObj) {
    if (captchaObj === null) return;

    var formData = new FormData();
    formData.append('action', 'createcaptcha_action');

    axios.post(ajax_object.ajaxUrl, formData, {
        headers: { 'Content-Type':  'multipart/form-data' }
    }).then(function (response) {
        var jsonData = response.data;

        // Store the default captcha in `localStorage()` 
        // ( --> It could be used for different domain request)
        //------------
        localStorage.setItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id, jsonData.captcha.origin.toLowerCase());

        
        // Display captcha
        //------------
        if ( jsonData.captcha.type == 'string' ) {
            captchaObj.style.cssText = "background-color:#F2F2F2;font-size:20px;text-align:center;padding:.7rem 1.3rem;display:inline-block;margin-top:.2rem;pointer-events:none;user-select:none;font-family: monospace;";
            captchaObj.innerHTML = jsonData.captcha.value;
        } else {
            captchaObj.innerHTML = '<span style="position:relative;display:inline-block"><img id="uix-usercenter-refresh-session-captcha" alt="" src="'+jsonData.captcha.value+'"></span>';
        }

        // Disable captcha
        //------------
        if ( ajax_object.captcha_detect == 'off' ) {
            Array.prototype.slice.call(document.querySelectorAll('.uix-usercenter-captcha-section')).forEach(function (el, i) {
               el.classList.add('disabled');
           });
        }


    }).catch(function (error) {
        if (error.response) {
            console.log(error.response.status);
        } else if (error.request) {
            console.log(error.request);
        } else {
            console.log(error.message);
        }
    });
}

(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {
        UixCreateCaptcha(document.getElementById('uix-usercenter-refresh-session-captcha'));
    });
})();


/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-login.js ----*/


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


/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-logout.js ----*/


/*! 
 * ************************************************
 * Logout & Logout via AJAX
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');


        /*! 
         * ########################################## Normal
         */
         function _curFunNormal() {
            localStorage.removeItem('UIX_USERCENTER_DATA_SITE_LOGIN');
            localStorage.removeItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id);
            UixDelCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE', '/');
         }

         //push to global variables
         window['uix-usercenter-listener-func'].adminLogoutBtn = _curFunNormal;

         var adminLogoutBtn = document.querySelector('#wp-admin-bar-logout a');
         if ( adminLogoutBtn !== null ) {
            adminLogoutBtn.removeEventListener('click', _curFunNormal);
            adminLogoutBtn.addEventListener('click', _curFunNormal);
         }

         //
         var pageLogoutBtn = document.querySelectorAll('.js-uix-usercenter-logout-btn');
         Array.prototype.slice.call(pageLogoutBtn).forEach(function (el, i) {
            el.removeEventListener('click', _curFunNormal);
            el.addEventListener('click', _curFunNormal);
        });


        /*! 
         * ########################################## AJAX
         */
        if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);

           
            function _curFunAjax(e) {
                e.preventDefault();

                var formData = new FormData();
                formData.append('action', 'logout_action'); //calls wp_ajax_???????

                axios.post(ajax_object.ajaxUrl, formData, {
                    headers: { "Authorization": 'Bearer ' + _data.token }
                }).then(function (response) {
                    var jsonData = response.data;

                    if (jsonData.status == true) {
                        localStorage.removeItem('UIX_USERCENTER_DATA_SITE_LOGIN');
                        localStorage.removeItem('UIX_USERCENTER_DATA__DEFAULT__' + ajax_object.captcha_id);
                        UixDelCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE', '/');
                        
                        document.location.href = ajax_object.redirecturl_logout;
                    }

                }).catch(function (error) {
                    if (error.response) {
                        console.log(error.response.status);
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log(error.message);
                    }
                });
            }

            //push to global variables
            window['uix-usercenter-listener-func'].pageLogoutBtnAjax = _curFunAjax;

            //
            var pageLogoutBtnAjax = document.querySelectorAll('.js-uix-usercenter-logout-btn--ajax');
            Array.prototype.slice.call(pageLogoutBtnAjax).forEach(function (el, i) {
                el.removeEventListener('click', _curFunAjax);
                el.addEventListener('click', _curFunAjax);
            });


        }



    });
})();


/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-curd.js ----*/

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



/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-passwordreset.js ----*/
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



/*---- file: wp-content/plugins/uix-usercenter/assets/js/frontend/ajax-register.js ----*/


/*! 
 * ************************************************
 * Register via AJAX
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var $form = document.querySelector('form#uix-usercenter-site-register');
        if ( $form === null ) return;


        var $stat = $form.querySelector('.status');
        var $btn = $form.querySelector('[type="submit"]');
        
        $form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            $stat.style.display = 'block';
            $stat.innerHTML = ajax_object.i18n.loadingmessage;
            

            //button status
            $btn.disabled = true;
                        

            var formData = new FormData();
            formData.append('action', 'register_action'); //calls wp_ajax_nopriv_???????
            formData.append('username', $form.querySelector('#username').value);
            formData.append('email', $form.querySelector('#email').value);
            formData.append('password', $form.querySelector('#password').value);
            formData.append('password_confirm', $form.querySelector('#password_confirm').value);
            formData.append('security', $form.querySelector('#uix-usercenter-site-register-security').value);
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
                    UixSetCookie('UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE', jsonData.token, '/', 'Session');
                    

                    
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


