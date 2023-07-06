(function ($) {
    "use strict";
    $(document).ready(function () {

        // to update view with booking
        var bookingsOffset = 0;

        // here we can set how many bookings per page
        var bookingsLimit = 5;
        // function when checking user booking by widget
        function bdb_user_bookings_manage(page = 1) {

            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_user_bookings_manage',
                'listing_id': $('#listing_id').val(),
                'listing_status': $('.listing_status').val(),
                'dashboard_type': $('#dashboard_type').val(),
                'limit': bookingsLimit,
                'offset': bookingsOffset,
                'page': page,
                //'nonce': nonce
            };
            // display loader class
            $(".dashboard-list-box").addClass('loading');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {
                    // display loader class
                    $(".dashboard-list-box").removeClass('loading');
                    if (data.data.html) {
                        $('.no-bookings-information-user').hide();
                        $("#booking-requests-user").html(data.data.html);
                        $(".pagination-container-user").html(data.data.pagination);
                    } else {
                        $("#booking-requests-user").empty();
                        $(".pagination-container-user").empty();
                        $('.no-bookings-information-user').show();
                    }

                }
            });

        }

        //reject from user
        $(document).on('click', '#reject_user_booking', function (e) {
            e.preventDefault();
            if (window.confirm(bdb_booking.areyousure)) {
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'cancelled';
                // preparing data for ajax
                var ajax_data = {
                    'action': 'bdb_user_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: bdb_booking.ajax_url,
                    data: ajax_data,

                    success: function (data) {

                        // display loader class
                        $this.parents('li').removeClass('loading');

                        bdb_user_bookings_manage();

                    }
                });
            }
        });

        $('div.pagination-container-user').on('click', 'a', function (e) {
            e.preventDefault();

            var page = $(this).parent().data('paged');

            bdb_user_bookings_manage(page);

            $('body, html').animate({
                scrollTop: $(".dashboard-list-box").offset().top
            }, 600);

            return false;
        });

        // dashboard owner approved area
        function bdb_owner_approved_bookings_manage(page = 1) {
            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_owner_approved_bookings_manage',
                'listing_id': $('#listing_id').val(),
                'listing_status': 'confirmed',
                'dashboard_type': $('#dashboard_type').val(),
                'limit': bookingsLimit,
                'offset': bookingsOffset,
                'page': page,
                //'nonce': nonce
            };
            if ($('#bdb_listing_status').attr('data-status')) ajax_data.listing_status = $('#bdb_listing_status').attr('data-status');
            if ($('#bdb_listing_id_approved').attr('data-status')) ajax_data.listing_id = $('#bdb_listing_id_approved').attr('data-status');

            // display loader class
            $(".dashboard-list-box").addClass('loading');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {

                    // display loader class
                    $(".dashboard-list-box").removeClass('loading');
                    if (data.data.html) {
                        $('.no-bookings-information-approved').hide();
                        $("#booking-requests-approved").html(data.data.html);
                        $(".pagination-container-approved").html(data.data.pagination);
                    } else {
                        $("#booking-requests-approved").empty();
                        $(".pagination-container-approved").empty();
                        $('.no-bookings-information-approved').show();
                    }

                }
            });

        }

        // dashboard owner deleted area
        function bdb_owner_deleted_bookings_manage(page = 1) {
            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_owner_approved_bookings_manage',
                'listing_id': $('#listing_id').val(),
                'listing_status': 'cancelled',
                'dashboard_type': $('#dashboard_type').val(),
                'limit': bookingsLimit,
                'offset': bookingsOffset,
                'page': page,
                //'nonce': nonce
            };
            if ($('#bdb_listing_status').attr('data-status')) ajax_data.listing_status = $('#bdb_listing_status').attr('data-status');
            if ($('#bdb_listing_id_approved').attr('data-status')) ajax_data.listing_id = $('#bdb_listing_id_approved').attr('data-status');

            // display loader class
            $(".dashboard-list-box").addClass('loading');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {
                    // display loader class
                    $(".dashboard-list-box").removeClass('loading');
                    if (data.data.html) {
                        $('.no-bookings-information-cancelled').hide();
                        $("#booking-requests-cancelled").html(data.data.html);
                        $(".pagination-container-cancelled").html(data.data.pagination);
                    } else {
                        $("#booking-requests-cancelled").empty();
                        $(".pagination-container-cancelled").empty();
                        $('.no-bookings-information-cancelled').show();
                    }

                }
            });

        }

        $(document).on('click', '#owner_cancel', function (e) {
            e.preventDefault();
            if (window.confirm(bdb_booking.areyousure)) {
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'cancelled';
                // preparing data for ajax
                var ajax_data = {
                    'action': 'bdb_owner_approved_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: bdb_booking.ajax_url,
                    data: ajax_data,

                    success: function (data) {

                        // display loader class
                        $this.parents('li').removeClass('loading');

                        bdb_owner_approved_bookings_manage();

                    }
                });
            }
        });
        $(document).on('click', '#owner_delete', function (e) {
            e.preventDefault();
            if (window.confirm(bdb_booking.areyousure)) {
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'deleted';
                // preparing data for ajax
                var ajax_data = {
                    'action': 'bdb_owner_approved_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: bdb_booking.ajax_url,
                    data: ajax_data,

                    success: function (data) {

                        // display loader class
                        $this.parents('li').removeClass('loading');

                        bdb_owner_deleted_bookings_manage();

                    }
                });
            }
        });
        $('.bdb-approved').on('click', function (e) {
            bdb_owner_approved_bookings_manage();
        });
        $('.bdb-listing-approved').on('click', function (e) {
            bdb_owner_approved_bookings_manage();
        });

        $('div.pagination-container-approved').on('click', 'a', function (e) {
            e.preventDefault();

            var page = $(this).parent().data('paged');

            bdb_owner_approved_bookings_manage(page);

            $('body, html').animate({
                scrollTop: $(".dashboard-list-box").offset().top
            }, 600);

            return false;
        });


        // dashboard owner pending area
        function bdb_owner_pending_bookings_manage(page = 1) {
            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_owner_approved_bookings_manage',
                'listing_id': $('#listing_id').val(),
                'listing_status': 'waiting',
                'dashboard_type': $('#dashboard_type').val(),
                'limit': bookingsLimit,
                'offset': bookingsOffset,
                'page': page,
                //'nonce': nonce
            };
            if ($('#bdb_listing_status').attr('data-status')) ajax_data.listing_status = $('#bdb_listing_status').attr('data-status');
            if ($('#bdb_listing_id_waiting').attr('data-status')) ajax_data.listing_id = $('#bdb_listing_id_waiting').attr('data-status');
            // display loader class
            $(".dashboard-list-box").addClass('loading');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {
                    // display loader class
                    $(".dashboard-list-box").removeClass('loading');
                    if (data.data.html) {
                        $('.no-bookings-information-waiting').hide();
                        $("#booking-requests-waiting").html(data.data.html);
                        $(".pagination-container-waiting").html(data.data.pagination);
                    } else {
                        $("#booking-requests-waiting").empty();
                        $(".pagination-container-waiting").empty();
                        $('.no-bookings-information-waiting').show();
                    }

                }
            });

        }

        $(document).on('click', '#owner_approved', function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.parents('li').addClass('loading');
            var status = 'confirmed';

            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_owner_approved_bookings_manage',
                'booking_id': $(this).data('booking_id'),
                'status': status,
                //'nonce': nonce
            };
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {

                    // display loader class
                    $this.parents('li').removeClass('loading');

                    bdb_owner_pending_bookings_manage();

                }
            });

        });

        $(document).on('click', '#owner_reject', function (e) {
            e.preventDefault();
            if (window.confirm(bdb_booking.areyousure)) {
                var $this = $(this);
                $this.parents('li').addClass('loading');
                var status = 'cancelled';
                // preparing data for ajax
                var ajax_data = {
                    'action': 'bdb_owner_approved_bookings_manage',
                    'booking_id': $(this).data('booking_id'),
                    'status': status,
                    //'nonce': nonce
                };
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: bdb_booking.ajax_url,
                    data: ajax_data,

                    success: function (data) {

                        // display loader class
                        $this.parents('li').removeClass('loading');

                        bdb_owner_pending_bookings_manage();

                    }
                });
            }
        });

        $('.bdb-listing-waiting').on('click', function (e) {
            bdb_owner_pending_bookings_manage();
        });

        $('div.pagination-container-waiting').on('click', 'a', function (e) {
            e.preventDefault();

            var page = $(this).parent().data('paged');

            bdb_owner_pending_bookings_manage(page);

            $('body, html').animate({
                scrollTop: $(".dashboard-list-box").offset().top
            }, 600);

            return false;
        });

        // dashboard owner cancelled area
        function bdb_owner_cancelled_bookings_manage(page = 1) {
            // preparing data for ajax
            var ajax_data = {
                'action': 'bdb_owner_approved_bookings_manage',
                'listing_id': $('#listing_id').val(),
                'listing_status': 'cancelled',
                'dashboard_type': $('#dashboard_type').val(),
                'limit': bookingsLimit,
                'offset': bookingsOffset,
                'page': page,
                //'nonce': nonce
            };
            if ($('#bdb_listing_status').attr('data-status')) ajax_data.listing_status = $('#bdb_listing_status').attr('data-status');
            if ($('#bdb_listing_id_cancelled').attr('data-status')) ajax_data.listing_id = $('#bdb_listing_id_cancelled').attr('data-status');
            // display loader class
            $(".dashboard-list-box").addClass('loading');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: bdb_booking.ajax_url,
                data: ajax_data,

                success: function (data) {
                    // display loader class
                    $(".dashboard-list-box").removeClass('loading');
                    if (data.data.html) {
                        $('.no-bookings-information-cancelled').hide();
                        $("#booking-requests-cancelled").html(data.data.html);
                        $(".pagination-container-cancelled").html(data.data.pagination);
                    } else {
                        $("#booking-requests-cancelled").empty();
                        $(".pagination-container-cancelled").empty();
                        $('.no-bookings-information-cancelled').show();
                    }

                }
            });

        }
        $('.bdb-listing-cancelled').on('click', function (e) {
            bdb_owner_cancelled_bookings_manage();
        });

        $('div.pagination-container-cancelled').on('click', 'a', function (e) {
            e.preventDefault();

            var page = $(this).parent().data('paged');

            bdb_owner_cancelled_bookings_manage(page);

            $('body, html').animate({
                scrollTop: $(".dashboard-list-box").offset().top
            }, 600);

            return false;
        });

        $("#bdb_paid").click(function () {
            $("#bdb_paid").addClass("active");
            $("#bdb_all_status").removeClass("active");
            $("#bdb_confirmed").removeClass("active");
        });
        $("#bdb_confirmed").click(function () {
            $("#bdb_confirmed").addClass("active");
            $("#bdb_all_status").removeClass("active");
            $("#bdb_paid").removeClass("active");
        });
        $("#bdb_all_status").click(function () {
            $("#bdb_all_status").addClass("active");
            $("#bdb_confirmed").removeClass("active");
            $("#bdb_paid").removeClass("active");
        });

        //Booking Calendar Month picker
        $('.directorist-booking-month-picker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            dateFormat: 'MM yy',
            nextText: '',
            prevText: '',
            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            },
            onChangeMonthYear: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var dashMonth    = parseInt( month )  + 1;
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                var listingId = $('.directorist-listing-id').val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                calendarContainer().addClass('directorist-booking-calendar--loader');
                dashboard_calender( dashMonth, year, listingId );
            }
        });
        $(".directorist-booking-month-picker").focus(function () {
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
            $("#ui-datepicker-div").addClass('directorist-monthpicker');
        });

        //Month Picker Placeholder
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        let newDate = new Date();
        $('.directorist-booking-month-picker').val(`${months[newDate.getMonth()]} ${newDate.getFullYear()}`)

        //Add Booking Modal
        $(document).on("click", ".directorist-booking-calender__calender-top-right button", function (e) {
            $(".directorist-booking-modal").addClass("show");
        });
        $(document).on("click", ".directorist-booking-modal__cross, .directorist-booking-modal__footer-cancel", function (e) {
            $(".directorist-booking-modal").removeClass("show");
        });

        //User Listings active status & Pass data-id to '.directorist-listing-id' input
        const userListings = document.querySelectorAll('.directorist-user-listing');
        const listingsIdInput = document.querySelector('.directorist-listing-id');
        if(userListings.length !==0){
            userListings[0].classList.add('active');
            listingsIdInput.value = userListings[0].getAttribute('data-id');
        }
        
        //User listings list event
        let calendarContainer = () => $('#directorist-calendar-outer-container');
        $('body').on("click", '.directorist-user-listing', function(event) {
            event.preventDefault();
            $('.directorist-user-listing').removeClass('active');
            $(this).addClass('active');
            var id = $(this).attr('data-id');
            var month =  '';
            var year =  '';
            $('.directorist-listing-id').val( id );
            calendarContainer().addClass('directorist-booking-calendar--loader');
            dashboard_calender( month, year, id );
        });

        $('body').on("click", '.directorist-booking-calender__wrapper .directorist-dash-prev', function(event) {
            event.preventDefault();
            var month =  $(this).data("prev-month");
            var year =  $(this).data("prev-year");
            var listingId = $('.directorist-listing-id').val();
            calendarContainer().addClass('directorist-booking-calendar--loader');
            dashboard_calender( month, year, listingId );
        });

        $('body').on("click", '.directorist-booking-calender__wrapper .directorist-dash-next', function(event) {
            event.preventDefault();
            var month =  $(this).data("next-month");
            var year  =  $(this).data("next-year");
            var listingId = $('.directorist-listing-id').val();
            calendarContainer().addClass('directorist-booking-calendar--loader');
            dashboard_calender( month, year, listingId );
        });

        function submit_calendar_update_unav_days(){
            var days = $(".bdb_calender_unavailable").val();
            if(days){
              var array = days.split("|");
              $.each( array, function( key, day ) {
                if( day ) {
                  $("td.directorist-calendar-day[data-date='" + day +"']").addClass('not_active');
                }
              });
            }
        }

        var directorist_pricing = $('.directorist-price').val();
        $('body .directorist-calendar-day:not(.directorist-weekend) .directorist-calendar-price .directorist-calendar-price-money__currency span').html(directorist_pricing);

        var weekend_price = $('.directorist-weekend-price').val();
        $('body .directorist-calendar-day.directorist-weekend .directorist-calendar-price .directorist-calendar-price-money__currency span').html(weekend_price);

        function submit_calendar_update_price(){
            var prices = $(".bdb_calender_price").val();
            if(prices){
                var obj = JSON.parse(prices);
                $.each( obj, function( day, price ) {
                if( day ) {
                    $("td.directorist-calendar-day[data-date='" + day +"'] .directorist-calendar-price .directorist-calendar-price-money__currency span").html(price);
                }
                });
            }

        }

        function dashboard_calender( month, year, listingId ){
            $.ajax({
                type   : "post",
                dataType : "json",
                url    : bdb_add_booking.ajax_url,
                data   : { action: "dashboard_rent_calendar", month : month, year: year, listingId: listingId},
                success  : function(data) {
                    $(".directorist-booking-calender__wrapper").html( data.response );
                    $(".directorist-price").val( data.pricing.normal_price );
                    $(".directorist-weekend-price").val( data.pricing.weekend_price );
                    $(".bdb_calender_price").val( data.pricing.calender_price );
                    calendarContainer().removeClass('directorist-booking-calendar--loader');
                    var directorist_pricing = $('.directorist-price').val();
                    $('body .directorist-calendar-day:not(.directorist-weekend) .directorist-calendar-price .directorist-calendar-price-money__currency span').html(directorist_pricing);
                    var weekend_price = $('.directorist-weekend-price').val();
                    $('body .directorist-calendar-day.directorist-weekend .directorist-calendar-price .directorist-calendar-price-money__currency span').html(weekend_price);
                    submit_calendar_update_price();
                    submit_calendar_update_unav_days();
                    multipleDaySelection();
                }
            })
        }

        submit_calendar_update_price();

        //Calendar multiple-day events
        function multipleDaySelection(){
            let dayRows = document.querySelectorAll('.directorist-calendar-day-row');
            dayRows.forEach(row=>{
                let bookedDay = row.querySelectorAll('.directorist-calendar-day__booked');
                bookedDay.forEach(day=>{
                    let prevDay = day.previousElementSibling;
                    let nextDay = day.nextElementSibling;
                    prevDay == null && !day.nextElementSibling.classList.contains('directorist-calendar-day_available') ? day.classList.add('bdb-booked-start') : '';
                    nextDay == null && !day.previousElementSibling.classList.contains('directorist-calendar-day_available') ? day.classList.add('bdb-booked-end') : '';

                    prevDay == null && !day.nextElementSibling.classList.contains('directorist-calendar-day_available') && day.querySelector('.directorist-calendar-event-title').innerHTML == '' ? day.classList.add('bdb-booked-start-tail') : '';
                    nextDay == null && !day.previousElementSibling.classList.contains('directorist-calendar-day_available') && day.querySelector('.directorist-calendar-event-title').innerHTML == '' ? day.classList.add('bdb-booked-end-tail') : '';


                    prevDay != null && day.previousElementSibling.classList.contains('directorist-calendar-day_available') ? day.classList.add('bdb-booked-start') : '';
                    nextDay != null && day.nextElementSibling.classList.contains('directorist-calendar-day_available') ? day.classList.add('bdb-booked-end') : '';

                    prevDay == null && (day.nextElementSibling.classList.contains('directorist-calendar-day_available') || day.nextElementSibling.classList.contains('directorist-empty-calendar-day')) ? (day.classList.remove('bdb-booked-end'), day.classList.remove('bdb-booked-start'), day.classList.add('bdb-booked-single')) : '';
                    nextDay == null && (day.previousElementSibling.classList.contains('directorist-calendar-day_available') || day.previousElementSibling.classList.contains('directorist-empty-calendar-day')) ? (day.classList.remove('bdb-booked-start'), day.classList.remove('bdb-booked-end'), day.classList.add('bdb-booked-single')) : '';
                })
            })
        }
        multipleDaySelection();

        //Listing search
        let userInput = document.querySelector('.directorist-booking-calender__search input[type=text]');
        userInput.addEventListener('keyup', listingSearch);
        function listingSearch() {
            let listFilter = userInput.value.toUpperCase();
            let listWrapper = document.querySelector('.directorist-booking-calender__search-content ul');
            let listItems = listWrapper.querySelectorAll('li');

            listItems.forEach(item=>{
                let itemVal = item.textContent || item.innerText;
                itemVal.toUpperCase().indexOf(listFilter) > -1 ? item.style.display = "" : item.style.display = "none";
            })
        }

    });
})(this.jQuery);