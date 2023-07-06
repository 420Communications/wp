/* eslint-disable */
(function ($) {
	$(document).ready(function () {
		function triggerBooking() {
			var inputClicked = false;
			$('body').on('click', 'a.booking-confirmation-btn', function (e) {
				const button = $(this);
				button.addClass('loading');

				e.preventDefault();
				// full name validation
				const first_name = $("input[name='firstname']").val();
				const email = $("input[name='email']").val();
				var guest_booking = $("input[name='guest_booking']").val();
				if (first_name === '') {
					$('#first_name').css('border-color', 'red');
					return false;
				}
				// email validation
				if (email === '') {
					$('#email').css('border-color', 'red');
					return false;
				}
				if (guest_booking == 'yes') {
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: bdb_booking.ajax_url,
						data: {
							action: 'guest_booking',
							guest_email: email
						},
						success(data) {
							if (data.error_msg) {
								$('.directorist-confirmation-error').html(data.error_msg);
							} else {
								$('#booking-confirmation').submit();
							}
						}
					});
				} else {
					$('#booking-confirmation').submit();
				}



			});

			$('body').on('click', 'a.directorist-book-now', function (e) {
				const button = $(this);
				if (inputClicked == false) {
					$('.directorist-booking-time-picker,.directorist-booking-time-slots-dropdown,.date-picker-listing-rental').addClass(
						'bounce'
					);
				} else {
					button.addClass('loading');
				}
				e.preventDefault();

				const freeplaces = button.data('freeplaces');

				setTimeout(function () {
					button.removeClass('loading');
					$('.directorist-booking-time-picker,.directorist-booking-time-slots-dropdown,.date-picker-listing-rental').removeClass(
						'bounce'
					);
				}, 3000);

				try {
					if (freeplaces > 0) {
						// preparing data for ajax
						const startDataSql = moment(
							$('#date-picker').data('daterangepicker').startDate,
							['MM/DD/YYYY']
						).format('YYYY-MM-DD');
						const endDataSql = moment($('#date-picker').data('daterangepicker').endDate, [
							'MM/DD/YYYY',
						]).format('YYYY-MM-DD');

						var ajax_data = {
							listing_type: $('#listing_type').val(),
							listing_id: $('#listing_id').val(),
							// 'nonce': nonce
						};
						if (startDataSql) ajax_data.date_start = startDataSql;
						if (endDataSql) ajax_data.date_end = endDataSql;
						if ($('input#slot').val()) ajax_data.slot = $('input#slot').val();
						if ($('.directorist-booking-time-picker').val()) ajax_data._hour = $('.directorist-booking-time-picker').val();
						if ($('.adults').text()) ajax_data.adults = $('.adults').text();
						if ($('.childrens').val()) ajax_data.childrens = $('.childrens').val();
						if ($('#tickets').text()) ajax_data.tickets = $('#tickets').text();
						var services = [];
						$.each($("input[name='_service[]']:checked"), function () {
							services.push($(this).val());
						});
						ajax_data.services = services;
						$('input#booking').val(JSON.stringify(ajax_data));
						$('#form-booking').submit();
					}
				} catch (e) {
					console.log(e);
				}

				if ($('#listing_type').val() == 'event') {
					var ajax_data = {
						listing_type: $('#listing_type').val(),
						listing_id: $('#listing_id').val(),
						date_start: $('.booking-event-date span').html(),
						date_end: $('.booking-event-date span').html(),
						// 'nonce': nonce
					};
					var services = [];
					$.each($("input[name='_service[]']:checked"), function () {
						services.push($(this).val());
					});
					ajax_data.services = services;

					// converent data
					ajax_data.date_start = moment(ajax_data.date_start, wordpress_data_format.date).format(
						'YYYY-MM-DD'
					);
					ajax_data.date_end = moment(ajax_data.date_end, wordpress_data_format.date).format(
						'YYYY-MM-DD'
					);
					if ($('#tickets').text()) ajax_data.tickets = $('#tickets').text();
					$('input#booking').val(JSON.stringify(ajax_data));
					if ($('#tickets').text() && $('#tickets').text() !== 'Qty') {
						$('#form-booking').submit();
					}
				}
			});

			$('.directorist-booking-date-picker-service').each(function (id, el) {
				$(el).daterangepicker({
					opens: 'left',
					singleDatePicker: ($(this).attr('booking_type') == 'rent' ? false : true),
					timePicker: false,
					minDate: moment().subtract(0, 'days'),
					locale: {
						format: 'MMMM D, YYYY',
						firstDay: parseInt(wordpress_data_format.day),
						applyLabel: bdb_booking.applyLabel,
						cancelLabel: bdb_booking.cancelLabel,
						fromLabel: bdb_booking.fromLabel,
						toLabel: bdb_booking.toLabel,
						customRangeLabel: bdb_booking.customRangeLabel,
						daysOfWeek: [
							bdb_booking.day_short_su,
							bdb_booking.day_short_mo,
							bdb_booking.day_short_tu,
							bdb_booking.day_short_we,
							bdb_booking.day_short_th,
							bdb_booking.day_short_fr,
							bdb_booking.day_short_sa,
						],
					},
					isInvalidDate: function (date) {
						// working only for rental
						if (typeof disabedDates == 'undefined') return false;
						if (disabedDates) {
							if (jQuery.inArray(date.format("YYYY-MM-DD"), disabedDates) !== -1) return true;
						}

					}
				});
			});

			var inputClicked = false;

			function directorist_booking_availability() {

				inputClicked = true;
				var bookingType = $(this).attr("booking_type");
				if (bookingType != 'rent' && !$('input#slot').val() && !$('.directorist-booking-time-picker').val()) {
					$('#negative-feedback').fadeIn();

					return;
				}

				const startDataSql = moment($('#date-picker').data('daterangepicker').startDate, [
					'MM/DD/YYYY',
				]).format('YYYY-MM-DD');
				const endDataSql = moment($('#date-picker').data('daterangepicker').endDate, [
					'MM/DD/YYYY',
				]).format('YYYY-MM-DD');

				// preparing data for ajax
				const ajax_data = {
					action: 'checking_booking_availability',
					listing_id: $('input#listing_id').val(),
					date_start: startDataSql,
					date_end: endDataSql,
					// 'nonce': nonce
				};

				if ($('input#slot').val()) ajax_data.slot = $('input#slot').val();
				if ($('input.adults').val()) ajax_data.adults = $('input.adults').val();
				if ($('.directorist-booking-time-picker').val()) ajax_data.hour = $('.directorist-booking-time-picker').val();

				// loader class
				$('a.directorist-book-now').addClass('loading');
				$('a.book-now-notloggedin').addClass('loading');
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: bdb_booking.ajax_url,
					data: ajax_data,

					success(data) {
						// loader class
						if (data.success == true) {
							if (data.data.free_places > 0) {
								$('a.directorist-book-now').data('freeplaces', data.data.free_places);
								$('.directorist-booking-error-msg').fadeOut();
								$('a.directorist-book-now').removeClass('inactive');
								if (data.data.price) {
									if (bdb_booking.currency_position == 'before') {
										$('.directorist-booking-estimated-cost span').html(
											`${bdb_booking.currency_symbol} ${
																					data.data.price
																			}`
										);
									} else {
										$('.directorist-booking-estimated-cost span').html(
											`${data.data.price} ${
																					bdb_booking.currency_symbol
																			}`
										);
									}

									$('.directorist-booking-estimated-cost').fadeIn();
								}
							} else {
								$('a.directorist-book-now').data('freeplaces', 0);
								$('.directorist-booking-error-msg').fadeIn();

								$('.directorist-booking-estimated-cost').fadeOut();

								$('.directorist-booking-estimated-cost span').html('');
							}
						} else {
							$('a.directorist-book-now').data('freeplaces', 0);
							$('.directorist-booking-error-msg').fadeIn();

							$('.directorist-booking-estimated-cost').fadeOut();
						}
						$('a.directorist-book-now').removeClass('loading');
						$('a.book-now-notloggedin').removeClass('loading');
					},
				});
			}

			let lastDayOfWeek;

			function update_directorist_booking() {
				$('a.directorist-book-now').addClass('loading');
				const date = $('#date-picker').data('daterangepicker').endDate._d;
				let dayOfWeek = date.getDay() - 1;

				if (date.getDay() == 0) {
					dayOfWeek = 6;
				}

				const startDate = moment($('#date-picker').data('daterangepicker').startDate, [
					'MM/DD/YYYY',
				]).format('YYYY-MM-DD');
				const endDate = moment($('#date-picker').data('daterangepicker').endDate, [
					'MM/DD/YYYY',
				]).format('YYYY-MM-DD');
				const ajax_data = {
					action: 'update_booking_slots',
					listing_id: $('input#listing_id').val(),
					date_start: startDate,
					date_end: endDate,
					slot: dayOfWeek,
				};
				$.ajax({
					type: 'POST',
					dataType: 'json',
					data: ajax_data,
					url: bdb_booking.ajax_url,

					success(data) {
						$('.directorist-booking-time-slots-dropdown .directorist-booking-panel-dropdown-scrollable').html(data.data);

						if (dayOfWeek != lastDayOfWeek) {
							$('.directorist-booking-panel-dropdown-scrollable .time-slot input').prop(
								'checked',
								false
							);

							$('.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown input#slot').val('');
							$('.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown a').html(
								$('.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown a').attr(
									'data-placeholder'
								)
							);
							$(' .directorist-booking-estimated-cost span').html(' ');
						}
						lastDayOfWeek = dayOfWeek;

						if (!$(`.directorist-booking-panel-dropdown-scrollable .time-slot[data-day='${dayOfWeek}']`).length) {

							$('.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown a').html($('.directorist-booking-no-slots').html());
						} else {
							// when we dont have slots for this day reset cost and show no slots
							$('.directorist-booking-no-slots').hide();
							$(' .directorist-booking-estimated-cost span').html(' ');
						}
						// show only slots for this day
						$('.directorist-booking-panel-dropdown-scrollable .time-slot').hide();

						$(`.directorist-booking-panel-dropdown-scrollable .time-slot[data-day='${dayOfWeek}']`).show();
						$('.time-slot').each(function () {
							const timeSlot = $(this);
							$(this).find('input')
								.on('change', function () {
									const timeSlotVal = timeSlot.find('strong').text();
									const slotArray = [
										timeSlot.find('strong').text(),
										timeSlot.find('input').val(),
									];

									$(
										'.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown input#slot'
									).val(JSON.stringify(slotArray));

									$('.directorist-booking-panel-dropdown.directorist-booking-time-slots-dropdown a').html(
										timeSlotVal
									);
									$('.directorist-booking-panel-dropdown').removeClass('active');

									directorist_booking_availability();
								});
						});
						$('a.directorist-book-now').removeClass('loading');
						$('a.book-now-notloggedin').removeClass('loading');
					},
				});
				let is_open = true;
				if ($('.directorist-booking-time-picker').length) {
					//console.log(availableDays[dayOfWeek]);
					if (availableDays) {
						const availableDaysHours = availableDays[dayOfWeek];
						const currentHourArr = [];
						const availableTime = [];
						if (availableDaysHours) {
							$('.directorist-booking-time-picker-wrap').show();
							availableDaysHours.forEach(function (hour, index) {
								const opening_hour = moment(hour.start, ['h:mm:ss a']).format();
								const closing_hour = moment(hour.close, ['h:mm A']).format();
								const availableOpen = moment(hour.start, ['h:mm A']).format(
									'h:mm a'
								);
								const availableClose = moment(hour.close, ['h:mm A']).format(
									'h:mm a'
								);
								if (opening_hour < closing_hour) {
									console.log("clear");
								}
								const availableValue = `${availableOpen} - ${availableClose}`;

								const checking_open = moment(hour.start, ['h:mm A']).format(
									'HH:MM'
								);
								const checking_close = moment(hour.close, ['h:mm A']).format(
									'HH:MM'
								);
								// get hour in 24 format
								const current_hour = $('.directorist-booking-time-picker').val();
								// check if current hour bar is open
								if (
									current_hour >= checking_open &&
									current_hour <= checking_close
								) {
									currentHourArr.push(true);
								} else {
									currentHourArr.push(false);
								}
								availableTime.push(availableValue);
								if (availableTime.length) {
									let content = '<div>';
									availableTime.forEach(function (value, index) {
										content += `<span class="directorist-available-time">${value}</span>`;
									});
									content += '</div>';
									$('.directorist-available-time-list').html(content);
								}
							});
							if (currentHourArr.includes(true)) {
								is_open = true;
								$('a.directorist-book-now').show();
								$('.directorist-available-time-block').show();
								$('a.directorist-book-now').removeClass('db-not-available');
								$('a.directorist-book-now').addClass('db-available');
								$('#negative-feedback').fadeOut();
								directorist_booking_availability();
							} else {
								is_open = false;
								$('#negative-feedback').fadeIn();
								$('.directorist-available-time-block').show();
								$('.directorist-booking-estimated-cost span').html('');
								$('a.directorist-book-now').removeClass('db-available');
								$('a.directorist-book-now').addClass('db-not-available');
								$('a.directorist-book-now').hide();
							}
						} else {
							is_open = false;
							$('#negative-feedback').fadeIn();
							$('.directorist-booking-estimated-cost span').html('');
							$('a.directorist-book-now').removeClass('db-available');
							$('a.directorist-book-now').addClass('db-not-available');
							$('a.directorist-book-now').hide();
							$('.directorist-available-time-block').hide();

						}
					}
				}
			}


			// if slots exist update them
			if ($('.time-slot').length) {
				update_directorist_booking();
			}

			// if slots exist update them
			if ($('.directorist-booking-time-picker').length) {
				update_directorist_booking();
			}
			$('body').on('change', '.directorist-booking-time-picker', function () {
				update_directorist_booking();
			});

			// show only services for actual day from date picker
			$('#date-picker').on('apply.daterangepicker', update_directorist_booking);

			// when slot is selected check if there are available bookings
			$('#date-picker').on('apply.daterangepicker', directorist_booking_availability);
			$('#date-picker').on('cancel.daterangepicker', directorist_booking_availability);

			$('.directorist-booking-ts-dropdown-toggle').on('click', function (e) {
				e.preventDefault();
				$('.directorist-booking-panel-dropdown-content').toggleClass('directorist-booking-pdc-active');
			});
			$('.directorist-booking-panel-dropdown-content').on('click', '.time-slot', function (e) {
				$('.directorist-booking-panel-dropdown-content').removeClass('directorist-booking-pdc-active');
			});

			// flatpicker time picker
			const currentDate = new Date();
			const currentHour = currentDate.getHours();
			const currentMin = currentDate.getMinutes();
			if ($('.directorist-booking-time-picker-wrap input.directorist-booking-time-picker').length) {
				$('.directorist-booking-time-picker-wrap input.directorist-booking-time-picker').flatpickr({
					enableTime: true,
					noCalendar: true,
					dateFormat: 'H:i',
					defaultHour: currentHour,
					defaultMinute: currentMin,
				});
			}
			$('.directorist-booking-event-tickets .atbd-dropdown-item').on('click', function () {
				var count_ticket = $(this).attr('data-id');
				var reservation_fee = $(this).attr('data-fee');
				var price = $(this).attr('data-price');
				var total_price = (price * count_ticket) + parseInt(reservation_fee);

				if (reservation_fee || price) {
					$('.directorist-booking-estimated-cost').fadeIn();
					if (bdb_booking.currency_position == 'before') {
						$('.directorist-booking-estimated-cost span').html(`${bdb_booking.currency_symbol} ${total_price}`);
					} else {
						$('.directorist-booking-estimated-cost span').html(
							`${reservation_fee} ${
											bdb_booking.currency_symbol
									}`
						);
					}
				}
				$('.atbd-dropdown-items').hide();
			});

			$('.atbd-drop-select .atbd-dropdown-item').on('click', function () {
				$('.directorist-dropdown__links').hide();
			});

			// get data-id
			const ticketsId = document.getElementById('tickets');
			document.querySelectorAll('.directorist-booking-event-tickets .atbd-dropdown-item').forEach(function (item) {
				const items = item.getAttribute('data-id');
				var pExt = '<span>s</span>';
				item.addEventListener('click', function () {
					$(this)
						.parent()
						.prev(ticketsId)
						.attr('data-id', `${items}`);
					if ($(this).parent().prev(ticketsId).attr('data-id') > 1 && $('.directorist-book-now').children('span').length === 0) {
						$('.directorist-book-now').each(function (id, elm) {
							$(elm).append(pExt);
						});
					} else if ($(this).parent().prev(ticketsId).attr('data-id') <= 1 && $('.directorist-book-now').children('span').length) {
						$('.directorist-book-now').each(function (id, elm) {
							$(elm).children('span').remove();
						});
					}
				});
			});

			//payment method option
			$(".atbd-payment-action").each(function (i, e) {
				$(e).on("click", function (elm) {
					elm.preventDefault();
					if ($(".atbd-payment-action").siblings("ul").hasClass("active") === true) {
						$(".atbd-payment-action").siblings("ul").removeClass("active");
					}
					$(this).siblings("ul").toggleClass("active");
				});
			});
			$("body").on("click", function (e) {
				if (!e.target.closest(".atbd-payment-action")) {
					$(".atbd-payment-action").siblings("ul").removeClass("active");
				}

			});

			//credit card number split
			$('#ccn').keyup(function () {
				var foo = $(this).val().split(" - ").join(""); // remove hyphens
				if (foo.length > 0) {
					foo = foo.match(new RegExp('.{1,4}', 'g')).join(" - ");
				}
				$(this).val(foo);
			});

			//add payment method accordion
			$(".directorist-wallet-payment-method__input input").each(function (i, e) {
				$(".directorist-wallet-payment-method__fields").slideUp();
				if ($(e).prop('checked')) {
					$(".directorist-wallet-payment-method__fields").slideUp();
					$(e).closest(".directorist-wallet-payment-method__input").siblings(".directorist-wallet-payment-method__fields").slideDown();
				}
				$(e).on("change", function () {
					if ($(this).prop('checked')) {
						$(".directorist-wallet-payment-method__fields").slideUp();
						$(this).closest(".directorist-wallet-payment-method__input").siblings(".directorist-wallet-payment-method__fields").slideDown();
					}
				});
			});

			$("#bdb_submit_payment_method").on("click", function (event) {
				event.preventDefault();
				$.ajax({
					type: "post",
					url: bdb_booking.ajax_url,
					data: {
						action: "bdb_payment_method", //calls wp_ajax_nopriv_ajaxlogin
						_nonce: $("#bdb_payment_method_nonce").val(),
						payment_method: $(".bdb_payment_method:checked").attr("value"),
						paypal_email: $("#bdb-paypal-email").val(),
						bank_details: $("#bdb-bank-details").val(),
						other: $("#bdb-other-details").val(),
					},
					beforeSend: function () {
						//$("#atbdp-send-system-info-submit").html("Sending");
					},
					success: function (data) {
						//console.log(data);
						$("#directorist-booking-payment-notice").html(data.data.success);
					},
					error: function (data) {
						console.log(data);
					},
				});
			});

			$('.atbd-dropdown-toggle:not(#tickets)').on('click', function () {
				$(this).siblings('.atbd-dropdown-items').slideToggle();
			});

			// Hide Clicked Anywhere
			$(document).bind('click', function (e) {
				let clickedDom = $(e.target);
				if (!clickedDom.hasClass('atbd-dropdown-toggle'))
					$('.atbd-dropdown-items').slideUp();
			});

			// select data-status
			const bpStatus = document.getElementById('bdb_listing_status');
			document.querySelectorAll('.bdb-approved').forEach(function (item) {
				const items = item.getAttribute('data-status');
				item.addEventListener('click', function () {
					bpStatus.setAttribute('data-status', `${items}`);
				});
			});

			//custom select
			const atbdSelect = document.querySelectorAll('.atbd-drop-select');
			if (atbdSelect !== null) {
				atbdSelect.forEach(function (el) {
					el.querySelectorAll('.atbd-dropdown-item').forEach(function (item) {
						item.addEventListener('click', function (e) {
							e.preventDefault();
							el.querySelector('.atbd-dropdown-toggle').textContent = item.textContent;
							el.querySelectorAll('.atbd-dropdown-item').forEach(function (elm) {
								elm.classList.remove('atbd-active');
							});
							item.classList.add('atbd-active');
						});
					});
				});
			}
		}

		triggerBooking();
		window.addEventListener('triggerBooking', triggerBooking);

		//Dashboard aside dropdown
		$('.atbdp_all_booking_nav .atbdp_all_booking_nav-link').on('click', function () {
			$(this).toggleClass('atbdp_all_booking_nav-link--active');
		})

		//Dashboard Calender Width on Resize Fixed
		if ($('.directorist-booking-calender__adv').innerWidth() < 800) {
			$('.directorist-booking-calender__adv').addClass('directorist-booking-calender__adv--res-fix');
		}

		//Booking check-in - check-out date range picker
		function checkInOut(){
			if ($('.directorist-booking-entry').length) {
				$('.directorist-booking-entry').daterangepicker({
					autoUpdateInput: false,
					locale: {
						cancelLabel: 'Clear'
					}
				});
				$('.directorist-booking-entry').on('apply.daterangepicker', function (ev, picker) {
					$(this).children('input[name="custom_field[directorist-booking-check-in]"]').val(picker.startDate.format('ddd, D MMM'));
					$(this).children('input[name="custom_field[directorist-booking-check-out]"]').val(picker.endDate.format('ddd, D MMM'));

				});
				$('.directorist-booking-entry').on('cancel.daterangepicker', function (ev, picker) {
					$(this).children('.directorist-booking-entry__data').val('');
				});
			}
		}
		checkInOut();
		window.addEventListener('directorist-search-form-nav-tab-reloaded', checkInOut);

		// To Stop Input Box Number Quickly Increasing Decreasing in Chrome
		$(document).on('mouseup', function (e) {
			e.stopPropagation()
		});

	});
})(jQuery);