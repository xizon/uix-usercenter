
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

