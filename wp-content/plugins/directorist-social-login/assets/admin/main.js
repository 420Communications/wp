(function ($) {
    $(document).ready(function () {
        // activate license and set up updated
        $('#social_login_activated input[name="social_login_activated"]').on('change', function (event) {
            event.preventDefault();
            var form_data = new FormData();
            var social_login_license = $('#social_login_license input[name="social_login_license"]').val();
            form_data.append('action', 'atbdp_social_login_license_activation');
            form_data.append('social_login_license', social_login_license);
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                url: social_js_obj.ajaxurl,
                data: form_data,
                success: function (response) {
                    if (response.status === true) {
                        $('#success_msg').remove();
                        $('#social_login_activated').after('<p id="success_msg">' + response.msg + '</p>');
                        location.reload();
                    } else {
                        $('#error_msg').remove();
                        $('#social_login_activated').after('<p id="error_msg">' + response.msg + '</p>');
                    }
                },
                error: function (error) {
                    // console.log(error);
                }
            });
        });
        // deactivate license
        $('#social_login_deactivated input[name="social_login_deactivated"]').on('change', function (event) {
            event.preventDefault();
            var form_data = new FormData();
            var social_login_license = $('#social_login_license input[name="social_login_license"]').val();
            form_data.append('action', 'atbdp_social_login_license_deactivation');
            form_data.append('social_login_license', social_login_license);
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                url: social_js_obj.ajaxurl,
                data: form_data,
                success: function (response) {
                    if (response.status === true) {
                        $('#success_msg').remove();
                        $('#social_login_deactivated').after('<p id="success_msg">' + response.msg + '</p>');
                        location.reload();
                    } else {
                        $('#error_msg').remove();
                        $('#social_login_deactivated').after('<p id="error_msg">' + response.msg + '</p>');
                    }
                },
                error: function (error) {
                    // console.log(error);
                }
            });
        });
    })

})(jQuery);

