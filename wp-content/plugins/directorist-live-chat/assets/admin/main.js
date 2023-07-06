(function ($) {
    $(document).ready(function () {
        // activate license and set up updated
        $('#live_chat_activated input[name="live_chat_activated"]').on('change', function (event) {
            event.preventDefault();
            var form_data = new FormData();
            var live_chat_license = $('#live_chat_license input[name="live_chat_license"]').val();
            form_data.append('action', 'atbdp_live_chat_license_activation');
            form_data.append('live_chat_license', live_chat_license);
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                url: dlc_main__admin_js.ajaxurl,
                data: form_data,
                success: function (response) {
                    if (response.status === true) {
                        $('#success_msg').remove();
                        $('#live_chat_activated').after('<p id="success_msg">' + response.msg + '</p>');
                        location.reload();
                    } else {
                        $('#error_msg').remove();
                        $('#live_chat_activated').after('<p id="error_msg">' + response.msg + '</p>');
                    }
                },
                error: function (error) {
                    // console.log(error);
                }
            });
        });
        // deactivate license
        $('#live_chat_deactivated input[name="live_chat_deactivated"]').on('change', function (event) {
            event.preventDefault();
            var form_data = new FormData();
            var live_chat_license = $('#live_chat_license input[name="live_chat_license"]').val();
            form_data.append('action', 'atbdp_live_chat_license_deactivation');
            form_data.append('live_chat_license', live_chat_license);
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                url: dlc_main__admin_js.ajaxurl,
                data: form_data,
                success: function (response) {
                    if (response.status === true) {
                        $('#success_msg').remove();
                        $('#live_chat_deactivated').after('<p id="success_msg">' + response.msg + '</p>');
                        location.reload();
                    } else {
                        $('#error_msg').remove();
                        $('#live_chat_deactivated').after('<p id="error_msg">' + response.msg + '</p>');
                    }
                },
                error: function (error) {
                    // console.log(error);
                }
            });
        });

    });
})(jQuery);