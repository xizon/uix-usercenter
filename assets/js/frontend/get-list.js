


/*! 
 * ************************************************
 * Get list
 ************************************************
*/
(function() {
    "use strict";
    document.addEventListener("DOMContentLoaded", function(event) {

        var loggedStoredObject =  localStorage.getItem('UIX_USERCENTER_DATA_SITE_LOGIN');
        var $listContainer = document.querySelector('#uix-usercenter-submission-list');
        if ( $listContainer === null ) return;


        if (loggedStoredObject) {
            var _data = JSON.parse(loggedStoredObject);

            var _api = `${_data.root}usercenter/v1/submission/display/{DISPLAY_NUMBER}/page/{PAGE_NUMBER}/rand/{RAND}/statistics/{ENABLE_STAT}`;
            _api = _api
                    .replace('{DISPLAY_NUMBER}', '10')
                    .replace('{PAGE_NUMBER}', '1')
                    .replace('{RAND}', '0')
                    .replace('{ENABLE_STAT}', '0');
                    
                    
            $listContainer.innerHTML = ajax_object.i18n.defaultcontent;

            axios.post(_api, {}, {
                headers: { "Authorization": 'Bearer ' + _data.token }
            }).then(function (response) {
                var jsonData = response.data;

                var res = '';
                if ( jsonData.data !== undefined ) {
                    jsonData.data.forEach( (item, i) => {
                        res += '<li class="uix-usercenter-text-start"><strong>' + item.title + '</strong> '+(item.status == 0 ? '<i style="color:red">('+ajax_object.i18n.review+')</i>' : '')+' <p>' + item.submission_project_url + '</p></li>';
                    });

                } else {
                    res = ajax_object.i18n.none;
                }
                $listContainer.innerHTML = res;

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


