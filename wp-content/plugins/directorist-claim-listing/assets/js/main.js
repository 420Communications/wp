/* eslint-disable */
(function ($) {

        var set_plan = $('#pricing_plans');
        var claimed = $('#claimed_by_admin');
        var claim_charge = $('input[name="claim_charge"]');

        claim_charge.hide();
        if($("#clain_with_fee").is(":checked")){
            claim_charge.show();
        }
        $('input[name="claim_fee"]').on("change", function () {
            if($("#clain_with_fee").is(":checked")){
                claim_charge.show();
            }else{
                claim_charge.hide();
            }
        });

        if(claimed.is(":checked")){
            set_plan.hide();
        }
        claimed.on('click', function () {
            if($(this).is(":checked")){
                set_plan.hide();
            }else{
                set_plan.show();
            }
        });

        //show plan allowance
        $('#directorist__plan-allowances').hide();
        $('body').on('change', '#directorist-claimer_plan', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var data = {
                'action': 'dcl_plan_allowances',
                'author_id': $('#directorist__plan-allowances').data('author_id'),
                'plan_id': $(this).val()
            };
            $.post(dcl_main.ajaxurl, data, function (response) {
                if (response.html){
                    $('#directorist__plan-allowances').show();
                    $('#directorist__plan-allowances').html(response.html);
                    $('.dcl_pricing_plan_name').removeClass('dcl-loading');
                }else{
                $('#directorist__plan-allowances').html(' ');
                $('.dcl_pricing_plan_name').removeClass('dcl-loading');
            }

            });
        });
        
        $('#directorist-claimer__form').on('submit', function (e) {
            
            e.preventDefault();

            var listing_type = $( 'input[type="radio"][name=listing_type]:checked').val();
            var plan_id = $( '#directorist-claimer__plan option:selected').val();
            var active_elm = $( '.dpp-order-select-dropdown ul' ).find( '.active' );

			var formData = new FormData();
			
			formData.append('action', 'dcl_submit_claim');

			if( $('#directorist__post-id').val() ) {
				formData.append('post_id', $('#directorist__post-id').val() );
			}
			
			if( $('#directorist-claimer__name').val() ) {
				formData.append('claimer_name', $('#directorist-claimer__name').val() );
			}
			
			if( $('#directorist-claimer__phone').val() ) {
				formData.append('claimer_phone', $('#directorist-claimer__phone').val() );
			}
			
			if( $('#directorist-claimer__details').val() ) {
				formData.append('claimer_details', $('#directorist-claimer__details').val() );
			}
			
			if( plan_id ) {
				formData.append('plan_id', plan_id );
			}
			
			if( dcl_main.nonce ) {
				formData.append('nonce', dcl_main.nonce );
			}
			
			if( listing_type ) {
				formData.append('type', listing_type );
			}
			
			if( active_elm.length ) {
				formData.append('order_id', active_elm.attr('data-value') );
			}
			
			$.ajax({
            method: 'POST',
            processData: false,
            contentType: false,
            url: dcl_main.ajaxurl,
            data: formData,
            success(response) {
				if ( response.take_payment ) {
                    window.location.href = response.checkout_url;
                } else {
                    $('#directorist-claimer__name').val('');
                    $('#directorist-claimer__phone').val('');
                    $('#directorist-claimer__details').val('');
                    $('#directorist-claimer__submit-notification').addClass('text-success').html(response.message);
                    setTimeout(() => {
                        $('#directorist-claimer__submit-notification').html("");
                    }, 5000);
                }
                if ( response.error_msg ){
                    $('#directorist-claimer__warning-notification').addClass('text-warning').html(response.error_msg);
                    setTimeout(() => {
                        $('#directorist-claimer__warning-notification').html("");
                    }, 5000);
                }
			},
				
			error( error ) {
				
			},
				
			});
        });

        //calim listng settings panel - set claim fee
        var claim_price = $("#claim_listing_price");
        claim_price.hide();

        $('select[name="claim_charge_by"]').on("change", function () {
            if($(this).val() == "static_fee"){
                claim_price.show();
            }else{
                claim_price.hide();
            }
        });
        if($('select[name="claim_charge_by"] option:selected').val() == "static_fee"){

            claim_price.show();
        }

    var dcln = $('.directorist-claim-listing__login-notice');
    dcln.hide();
    $('.directorist-claim-listing__login-alert ').on('click', function (e) {
        e.preventDefault();
        dcln.slideDown();
    });

})(jQuery);

