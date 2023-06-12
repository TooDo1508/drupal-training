
console.log(23344);

(function ($, Drupal, once) {
    Drupal.behaviors.eventRetrict = {
        attach: function (context, settings) {
            $(document).ready(function () {
                $('.alk-authentication-form').css('display', "none");
                $('.login-button > a').click(function (e) {
                    console.log(123);
                    $('.login-content').css('display', "none");
                    $('.alk-authentication-form').css('display', "block");
                });
            });
        }
    };
})(jQuery, Drupal, once);