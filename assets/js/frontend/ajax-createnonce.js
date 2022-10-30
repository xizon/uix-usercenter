
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

