

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

