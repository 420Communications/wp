(function ($) {
	/*
	 *  Caching all fonts vars to use later on scripts
	 *  Input fields
	 */

	$(document).ready(function () {
		// activate license and set up updated
		$('#directorist_booking_activated input[name="directorist_booking_activated"]').on('change', function (
			event
		) {
			event.preventDefault();
			const form_data = new FormData();
			const directorist_booking_license = $(
				'#directorist_booking_license input[name="directorist_booking_license"]'
			).val();
			form_data.append('action', 'atbdp_directorist_booking_license_activation');
			form_data.append('directorist_booking_license', directorist_booking_license);
			$.ajax({
				method: 'POST',
				processData: false,
				contentType: false,
				url: bdb_admin__js_obj.ajaxurl,
				data: form_data,
				success(response) {
					if (response.status === true) {
						$('#success_msg').remove();
						$('#directorist_booking_activated').after(
							`<p id="success_msg">${response.msg}</p>`
						);
						location.reload();
					} else {
						$('#error_msg').remove();
						$('#directorist_booking_activated').after(
							`<p id="error_msg">${response.msg}</p>`
						);
					}
				},
				error(error) {
					// console.log(error);
				},
			});
		});
		// deactivate license
		$('#directorist_booking_deactivated input[name="directorist_booking_deactivated"]').on(
			'change',
			function (event) {
				event.preventDefault();
				const form_data = new FormData();
				const directorist_booking_license = $(
					'#directorist_booking_license input[name="directorist_booking_license"]'
				).val();
				form_data.append('action', 'atbdp_directorist_booking_license_deactivation');
				form_data.append('directorist_booking_license', directorist_booking_license);
				$.ajax({
					method: 'POST',
					processData: false,
					contentType: false,
					url: bdb_admin__js_obj.ajaxurl,
					data: form_data,
					success(response) {
						if (response.status === true) {
							$('#success_msg').remove();
							$('#directorist_booking_deactivated').after(
								`<p id="success_msg">${response.msg}</p>`
							);
							location.reload();
						} else {
							$('#error_msg').remove();
							$('#directorist_booking_deactivated').after(
								`<p id="error_msg">${response.msg}</p>`
							);
						}
					},
					error(error) {
						// console.log(error);
					},
				});
			}
		);

		function bookingHours(dataObject) {
			var weeks = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
			var weeksDom = $('#weeksDom');
			var dataDom = $('#dataDom');
			var weeksHtml = '';

			function timer(weekIndex, rowIndex, id, data, dataKey) {
				var htmlDom = `<div class="directorist-form-group directorist-booking-input">
                                                <label for="bdb-${id}-from">${bdb_add_booking.time_from_text}</label>
                                                <input type="time" data-key="${dataKey}" name="bdb[${weekIndex}][${rowIndex}][start]" class="directorist-form-element directorist-booking-input-item directorist-booking-${id}-start directorist-booking-input-start" id="bdb-${id}-from-${dataKey}" value="${data.start}">
                                                </div>
                                                <div class="directorist-form-group directorist-booking-input">
                                                <label for="bdb-${id}-to">${bdb_add_booking.time_to_text}</label>
                                                <input type="time" data-key="${dataKey}" name="bdb[${weekIndex}][${rowIndex}][close]" class="directorist-form-element directorist-booking-input-item directorist-booking-${id}-close directorist-booking-input-close" id="bdb-${id}-to-${dataKey}" value="${data.close}">
                                                </div>
                                                <div class="directorist-form-group directorist-booking-input directorist-booking-day-slots">
                                                <label for="bdb-${id}-slots">${bdb_add_booking.slots_text}</label>
                                                <input type="number" data-key="${dataKey}" name="bdb[${weekIndex}][${rowIndex}][slots]" class="directorist-form-element directorist-booking-input-item directorist-booking-${id}-slots directorist-booking-input-slots" id="bdb-${id}-slots-${dataKey}" value="${data.slots}" min="0">
                                                </div>
                                                <button data-id="${id}" data-key="${dataKey}" class="dashicons dashicons-trash diretorist-booking-hour-remove" type="button"></button>
                                        `;
				return htmlDom;
			}


			$(weeks).each(function (index, el) {
				var activeWeek = index === 0 ? "'directorist-booking-weekdays-tab-nav__item directorist-active'" : "directorist-booking-weekdays-tab-nav__item";
				if (el == 'monday') {
					var day = 'Monday';
				} else if (el == 'tuesday') {
					var day = 'Tuesday';
				} else if (el == 'wednesday') {
					var day = 'Wednesday';
				} else if (el == 'thursday') {
					var day = 'Thursday';
				} else if (el == 'friday') {
					var day = 'Friday';
				} else if (el == 'saturday') {
					var day = 'Saturday';
				} else if (el == 'sunday') {
					var day = 'Sunday';
				}
				weeksHtml += '<button class=' + activeWeek + ' data-id="' + el + '" data-key="' + index + '" type="button">' + day + '</button>';
			});
			$(weeksDom).html(weeksHtml);

			function domManipulation() {
				var dataHtml = '<div>';
				$(weeks).each(function (key, id) {
					var buttons = $('#weeksDom button');
					var activeWeek = $(buttons[key]).hasClass('directorist-active') ? "directorist-active" : null;
					dataHtml += `<div class="directorist-week-day-disable ${activeWeek}" id="${id}-${key}">
                                ${
                                        dataObject[key][id].map(function(data, ind) {
                                        return `<div class="directorist-booking-hour-selection directorist-hour-selection-${id}" id="${id}ID-${ind}">
                                                ${timer(key, ind, id, data, `${ind}`)}
                                        </div>`;
                                        }).join("")
                                        }
                                        </div>`;
				});
				dataHtml += '</div>'
				$(dataDom).html(dataHtml);
			}

			/* Time duplicated alert */
			function copyTimeAlert(classNames = "directorist-booking-time-duplicate-alert", alertText = "Time copied to selected days!") {
				let copyAlertTag = document.createElement('p');
				copyAlertTag.classList.add(classNames);
				copyAlertTag.textContent = alertText;
				return copyAlertTag;
			}

			/* Time duplicate action */
			$('#diretorist-booking-btn-copy').on('click', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				var array = JSON.parse(JSON.stringify(dataObject[key][id]));
				if (document.getElementById('directorist-checkbox-select-all').checked === true) {
					weeks.map((item, index) => {
						return dataObject[index][item] = [array.map(item => {
							return {
								start: item.start,
								close: item.close,
								slots: item.slots,
								id: item.id
							}
						})][0];
					});
				} else {
					document.querySelectorAll('.directorist-booking-duplicate-day:checked').forEach(elm => {
						let dayId = elm.getAttribute('data-day-id');
						weeks.map((item, index) => {
							return dataObject[dayId][item] = [array.map(item => {
								return {
									start: item.start,
									close: item.close,
									slots: item.slots,
									id: item.id
								}
							})][0];
						});
					})
				}

				/* Show alert after copy */
				let thisClosestsSibling = $(this).closest('.directorist-booking-time-duplicate-dropdown__content__footer').siblings('.directorist-booking-time-duplicate-dropdown__content__inner');
				if ($(".directorist-booking-time-duplicate-dropdown__content__inner .directorist-checkbox input:checked").length !== 0) {
					$(thisClosestsSibling).append(copyTimeAlert());
					setTimeout(() => {
						$('.directorist-booking-time-duplicate-alert').remove();
					}, 3000);
				} else {
					$(thisClosestsSibling).append(copyTimeAlert('directorist-booking-time-duplicate-alert--warning', 'Please select a day first!'));
					setTimeout(() => {
						$('.directorist-booking-time-duplicate-alert--warning').remove();
					}, 3000);
				}
			});

			$('body').on('click', '#weeksDom button', function () {
				var id = $(this).attr('data-id');
				var key = $(this).attr('data-key');
				$('#weeksDom button').removeClass('directorist-active');
				$('.bh-content').removeClass('directorist-active');

				$(id).addClass('directorist-active');
				$(this).addClass('directorist-active');

				domManipulation();

				if ($('.directorist-week-day-disable.directorist-active .directorist-booking-hour-selection').length < 1) {
					$('#bhAddNew').click();
				}

				$(".directorist-week-day-disable.directorist-active").addClass('directorist-active-loading');
				setTimeout(() => {
					$(".directorist-week-day-disable.directorist-active").removeClass('directorist-active-loading');
				}, 200);

				//Hide active day from copy list
				setTimeout(() => {
					let activeDayDataKey = $(".directorist-booking-weekdays-tab-nav__item.directorist-active").attr("data-key");
					$("[data-day-id='" + activeDayDataKey + "']").closest('.directorist-checkbox').siblings().show();
					$("[data-day-id='" + activeDayDataKey + "']").closest('.directorist-checkbox').hide();
				}, 500);
			});

			$('body').on('click', '.diretorist-booking-hour-remove', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				var keyDeleted = $(this).attr('data-key');
				dataObject[key][id] = dataObject[key][id].filter((item, key) => {
					return key !== parseInt(keyDeleted);
				});
				return domManipulation();
			});

			$('body').on('keyup change input', '.directorist-booking-input-start', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				var keyUpdate = $(this).attr('data-key');
				dataObject[key][id].map(item => {
					if (item.id === id + "ID-" + keyUpdate) {
						return item.start = $(this).val();
					}
				});
			});

			$('body').on('keyup change input', '.directorist-booking-input-close', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				var keyUpdate = $(this).attr('data-key');
				return dataObject[key][id].map(item => {
					if (item.id === id + "ID-" + keyUpdate) {
						item.close = $(this).val();
					}
				});
			});

			$('body').on('keyup change input', '.directorist-booking-input-slots', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				var keyUpdate = $(this).attr('data-key');

				dataObject[key][id].map(item => {
					if (item.id === id + "ID-" + keyUpdate) {
						item.slots = $(this).val();
					}
				});
			});

			$('#bhAddNew').on('click', function () {
				var id = $('#weeksDom button.directorist-active').attr('data-id');
				var key = $('#weeksDom button.directorist-active').attr('data-key');
				dataObject[key][id].push({
					start: "",
					close: "",
					slots: 1,
					id: id + "ID-" + dataObject[key][id].length,
				});
				return domManipulation();
			});

			$(window).on('load', function () {
				if($('#weeksDom').length){
					var id = $('#weeksDom button.directorist-active').attr('data-id');
					var key = $('#weeksDom button.directorist-active').attr('data-key');
					dataObject[key][id].map((item, ind) => {
						item.id = id + "ID-" + ind;
					});
				}
			});

			return domManipulation();

		}

		function bookingHoursInit() {
			var booking_hours = $('#bdb_hours').val() !== undefined ? JSON.parse($('#bdb_hours').val()) : undefined;

			if (typeof booking_hours !== 'undefined') {
				var monday_hours = booking_hours.monday_hours;
				var tuesday_hours = booking_hours.tuesday_hours;
				var wednesday_hours = booking_hours.wednesday_hours;
				var thursday_hours = booking_hours.thursday_hours;
				var friday = booking_hours.friday_hours;
				var saturday = booking_hours.saturday_hours;
				var sunday = booking_hours.sunday_hours;
			}

			bookingHours([{
					monday: monday_hours !== undefined ? monday_hours : []
				},
				{
					tuesday: tuesday_hours !== undefined ? tuesday_hours : []
				},
				{
					wednesday: wednesday_hours !== undefined ? wednesday_hours : []
				},
				{
					thursday: thursday_hours !== undefined ? thursday_hours : []
				},
				{
					friday: friday !== undefined ? friday : []
				},
				{
					saturday: saturday !== undefined ? saturday : []
				},
				{
					sunday: sunday !== undefined ? sunday : []
				},
			]);

			$('select[name="directory_type"]').on('change', function () {
				if (typeof booking_hours !== 'undefined') {
					var monday_hours = booking_hours.monday_hours;
					var tuesday_hours = booking_hours.tuesday_hours;
					var wednesday_hours = booking_hours.wednesday_hours;
					var thursday_hours = booking_hours.thursday_hours;
					var friday = booking_hours.friday_hours;
					var saturday = booking_hours.saturday_hours;
					var sunday = booking_hours.sunday_hours;
				}
				bookingHours([{
						monday: monday_hours !== undefined ? monday_hours : []
					},
					{
						tuesday: tuesday_hours !== undefined ? tuesday_hours : []
					},
					{
						wednesday: wednesday_hours !== undefined ? wednesday_hours : []
					},
					{
						thursday: thursday_hours !== undefined ? thursday_hours : []
					},
					{
						friday: friday !== undefined ? friday : []
					},
					{
						saturday: saturday !== undefined ? saturday : []
					},
					{
						sunday: sunday !== undefined ? sunday : []
					},
				]);
			});
			if ($('.directorist-week-day-disable.directorist-active .directorist-booking-hour-selection').length < 1) {
				$('#bhAddNew').click();
			}
		}
		bookingHoursInit();
		window.addEventListener('directorist-reload-plupload', bookingHoursInit);

		// Calendar
		$(window).on('load', function () {
			$('body').on('click','#directorist-calendar-outer-container span.directorist-calendar-day-date', function(e) {
				e.preventDefault();
				var td = $(this).closest('.directorist-calendar-day');
				var date = td.data('date');
				var $el = $(".bdb_calender_unavailable");

				if(td.hasClass('not_active')){
					td.removeClass('not_active');
					var current_dates = $el.val();
					current_dates = current_dates.replace(date + "|","");
					$el.val(current_dates);
				} else {
				   td.addClass('not_active');
				   $el.val( $el.val() + date + "|");
				}
			});

			$('body').on("keydown input", '#directorist-calendar-outer-container .directorist-calendar-price-money input', function() {
				var td = $(this).closest('.directorist-calendar-day');
				var date = td.data('date');
				var current_price = $(this).val();
				var json = {};
				var current_value = $(".bdb_calender_price").val();
				if(current_value) {
					var json = jQuery.parseJSON($(".bdb_calender_price").val());
				}
				json[date] = current_price;
				var stringit = JSON.stringify(json);
				$('.bdb_calender_price').val(stringit);
			});

			$('body').on('input', '.directory_pricing_field', function(e) {
				e.preventDefault();
				var price = $(this).val();
      			$('.directorist-calendar-day:not(.directorist-weekend) .directorist-calendar-price input').val( price );
				submit_calendar_update_price();
			});

			$('body').on('input', '.directorist-weekend-price', function(e) {
				e.preventDefault();
				var price = $(this).val();
      			$('.directorist-calendar-day.directorist-weekend .directorist-calendar-price input').val( price );
				submit_calendar_update_price();
			});

			function submit_calendar_update_unav_days(){
				var days = $(".bdb_calender_unavailable").val();

				if(days){
				  var array = days.split("|");

				  $.each( array, function( key, day ) {
					if( day ) {
					  $("td.directorist-calendar-day[data-date='"+day+"']").addClass('not_active');
					}
				  });
				}

			}

			function submit_calendar_update_price(){
				var prices = $(".bdb_calender_price").val();
				if(prices){
				   var obj = JSON.parse(prices);

					$.each( obj, function( day, price ) {
					if( day ) {
						$("td.directorist-calendar-day[data-date='"+day+"'] .directorist-calendar-price input").val(price);
					}
					});
				}

			}

			let calendarContainer = document.querySelector('#directorist-calendar-outer-container');
			$('body').on("click", '#directorist-calendar-outer-container .directorist-prev', function(event) {
				event.preventDefault();
				var month =  $(this).data("prev-month");
				var year =  $(this).data("prev-year");
				calendarContainer.classList.add('directorist-booking-calendar--loader');
				getCalendar(month,year);
			});

			$('body').on("click", '#directorist-calendar-outer-container .directorist-next', function(event) {
				event.preventDefault();
				var month =  $(this).data("next-month");
				var year  =  $(this).data("next-year");
				calendarContainer.classList.add('directorist-booking-calendar--loader');
				getCalendar(month,year);
			});

			function getCalendar(month,year){

				$.ajax({
					type   : "post",
					dataType : "json",
					url    : bdb_add_booking.ajax_url,
					data   : { action: "directorist_rent_calendar", month : month, year: year},
					success  : function(data) {
						$("#directorist-calendar-outer").html(data.response);
						var _normal_price = $('.directory_pricing_field').val();
						$('.directorist-calendar-day:not(.directorist-weekend) .directorist-calendar-price input').val(_normal_price);
						var _weekend_price = $('.directorist-weekend-price').val();
					    $('.directorist-calendar-day.directorist-weekend .directorist-calendar-price input').val(_weekend_price);
					    submit_calendar_update_price();
					    submit_calendar_update_unav_days();
						calendarContainer.classList.remove('directorist-booking-calendar--loader');
					}
				 })
			 }

			var directorist_pricing = $('.directory_pricing_field').val();
  			$('body .directorist-calendar-day:not(.directorist-weekend) .directorist-calendar-price input').val(directorist_pricing);

			var weekend_price = $('.directorist-weekend-price').val();
  			$('body .directorist-calendar-day.directorist-weekend .directorist-calendar-price input').val(weekend_price);
			submit_calendar_update_price();
			submit_calendar_update_unav_days();
		});

	});

})(jQuery);