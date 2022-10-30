


/*! 
 * ************************************************
 * Authentication & Get User Info
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');

        if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);
            
            axios.post(`${_data.root}usercenter/v1/auth`, {}, {
                headers: { "Authorization": 'Bearer ' + _data.token }
            }).then(function (response) {
                console.log( response.data );
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

    });
})();
