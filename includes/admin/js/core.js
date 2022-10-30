(function ($) {
    "use strict";
    $(function () {


        // Logout 
        $( "#wp-admin-bar-logout a" ).off("click.logout").on("click.logout", function (e) {
            localStorage.removeItem("UIX_USERCENTER_DATA_SITE_LOGIN");

            //cookie
            document.cookie = "UIX_USERCENTER_DATA_SITE_LOGIN_COOKIE=0;expires="+new Date(0).toUTCString()+";path=/"
       
        });

    });
})(jQuery);