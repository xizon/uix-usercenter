


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