jQuery(document).ready(function ($) {

    // show allowancess on plan selection

    // const plan_id = $('select[name="admin_plan"]').val();
    // if ( plan_id ) {
    //     get_allowances( plan_id );
    // }
    $('body').on('change', 'select[name="admin_plan"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#directorist-claim-warning-notification').html(' ');
        $('.directorist_loader').show();
        get_allowances( $( this ).val() );
    });

    function get_allowances( plan_id ){
        var data = {
            'action': 'dynamic_admin_listing_form_and_allowances',
            'plan_id': plan_id,
            'post_id': $("input[name='post_ID']").val(),
            'user_id': $('select[name=post_author_override]').val(),
            'directory_type': $('select[name="directory_type"]').val(),
        };
        $.post(validator_admin_js.ajaxurl, data, function (response) {
            if( response.data ){
                $('.directorist_loader').hide();
                $('#directorist-allowances').show();
                $('#directorist-allowances').html(response.data['allowances']);
            }else{
                $('#directorist-allowances').hide();
                $('.directorist_loader').hide();
            }
        });
    }

    $('select[name=post_author_override]').on('change', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".admin_plan_container").html(' ');
        var data = {
            'action': 'plan_allowances_on_user_selection',
            'user_id': $(this).val(),
            'post_id': $("input[name='post_ID']").val(),
            'directory_type': $('select[name="directory_type"]').val(),
        };
        $.post(validator_admin_js.ajaxurl, data, function (response) {
            if(response) {
                $('.directorist-admin-form-plan-container')
                                .empty()
                                .html( response );
            }
        });
    });

    $('body').on('change','.select_active_order', function(){

        var form_data       = new FormData();
        var order_id        = $( this ).val();
        var general_label   = $(this).closest('.dpp-order-select-dropdown').attr('data-general_label');
        var featured_label  = $(this).closest('.dpp-order-select-dropdown').attr('data-featured_label');
        var label           = $(this).closest('.dpp-order-select-dropdown').attr('data-label');
        var user_id         = $('select[name=post_author_override]').val();
        var plan_id         = $('select[name=admin_plan]').val();
        form_data.append('action', 'select_active_order');
        form_data.append('order_id', order_id);
        form_data.append('user_id', user_id);
        form_data.append('plan_id', plan_id);
        form_data.append('general_label', general_label);
        form_data.append('featured_label', featured_label);
        form_data.append('label', label);
        
        $.ajax({
            method: 'POST',
            processData: false,
            contentType: false,
            url: validator_admin_js.ajaxurl,
            data: form_data,
            success: function (response) {
                
                let content_area = $( '.select_active_order' );

                $( '.directorist-listing-type' ).remove();

                $( content_area ).after( response );
            },
            error: function (error) {
                console.log(error);
            },
        });
    });

    $('body').on('click', '#confirm_plan', function(e){
        e.preventDefault();
        $(this).addClass('dpp-loading');
        $("input#publish").attr('disabled', 'disabled');
        $("input#publish").css('pointer-events', 'none');
        // Post via AJAX
        var data = {
            'action': 'atpp_gifting_plan',
            'order_id': $("select[name='activated_order']").val(),
            'post_id': $("input[name='post_ID']").val(),
            'plan_id' : $('select[name=admin_plan]').val(),
            'user_id' : $('select[name=post_author_override]').val(),
            'listing_type' : $("input[name='listing_type']:checked").val(),
        };
        $.post(validator_admin_js.ajaxurl, data, function (response) {
            // console.log( response );
            // return;
            $("#confirm_plan").removeClass('dpp-loading');
            $("input#publish").removeAttr('disabled');
            $("input#publish").css('pointer-events', 'all');
            if( response.validation_error ){
                $('#directorist-claim-submit-notification').html( '' );
                $('#directorist-claim-warning-notification').html( response.validation_error );
            }else{
                $('#directorist-claim-warning-notification').html( '' );
                $('#directorist-claim-submit-notification').addClass('text-success').html(response.message);
            }
        });
    });

});