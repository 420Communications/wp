(function ($) {

	$('#disable_slot_status').on('change', function () {
		$('.bdb-select-hours-wrapper').toggleClass('slots-active');
	});
	if ($('#disable_slot_status').is(':checked')) {
		$('.bdb-select-hours-wrapper').toggleClass('slots-active');
	}

	// select booking type
	const displaySlot = () => $('.directorist-booking-slot-available-check').hide();
	const slotAvailable = () => $('.directorist-booking-slot-available-text').hide();
	const slotsActive = () => $('.bdb-select-hours-wrapper');
	const availableTime = () => $('.directorist-booking-available-time').hide();
	const availableTimeText = () => $('.directorist-booking-available-time-text').hide();
	const bookingEvent = () => $('.directorist-booking-event');
	const bookingService = () => $('.directorist-booking-service');
	const bookingRent = () => $('.directorist-booking-rental');
	const bookingGuests = () => $('.directorist-booking-guest-reservation');
	const slotTextDisable = $('#bdb_slot_available_text').prop('disabled', true);
	const textDisable = $('#bdb_available_time_text').prop('disabled', true);
	const ticketDisable = $('#bdb_available_ticket_text').prop('disabled', true);
	

	$(window).load(function () {
		function bookigTypeDefault() {
			if (bdb_booking.booking_type !== 'all') {
				if (bdb_booking.booking_type === 'service') {
					bookingService().show();
					bookingGuests().show();
					bookingEvent().hide();
					bookingRent().hide();
				} else if (bdb_booking.booking_type === 'event') {
					bookingEvent().show();
					bookingService().hide();
					bookingGuests().hide();
					bookingRent().hide();
				} else if (bdb_booking.booking_type === 'rent') {
					bookingRent().show();
					bookingEvent().hide();
					bookingService().hide();
					bookingGuests().show();
				}
			}
			$('#slot_status').each((index, element) => {
				if ($(element).is(':checked')) {
					displaySlot().show();
					slotAvailable().show();
					slotsActive().addClass('slots-active');
					availableTime().hide();
					availableTimeText().hide();
				}
			});

			$('#time_picker').each((index, element) => {
				if ($(element).is(':checked')) {
					availableTime().show();
					availableTimeText().show();
					displaySlot().hide();
					slotAvailable().hide();
				}
			});

			$('input[name="bdb_booking_type"]').each((index, element) => {
				if ($(element).is(':checked') && $(element).data('booking-type') === 'service') {
					bookingService().show();
					bookingGuests().show();
					bookingEvent().hide();
					bookingRent().hide();
				}

				if ($(element).is(':checked') && $(element).data('booking-type') === 'event') {
					bookingEvent().css('display', 'block');
					bookingService().hide();
					bookingGuests().hide();
					bookingRent().hide();
				}

				if ($(element).is(':checked') && $(element).data('booking-type') === 'rent') {
					bookingRent().show();
					bookingEvent().hide();
					bookingService().hide();
					bookingGuests().show();
				}
			});

			$('#display_slot_available_text').each((index, element) => {
				if ($(element).is(':checked')) {
					slotTextDisable.prop('disabled', false);
				} else {

					slotTextDisable.prop('disabled', true);
				}
			});

			$('#display_available_time').each((index, element) => {
				if ($(element).is(':checked')) {
					textDisable.prop('disabled', false);
				} else {
					textDisable.prop('disabled', true);
				}
			});

			$('#display_available_ticket').each((index, element) => {
				if ($(element).is(':checked')) {
					ticketDisable.prop('disabled', false);
				} else {
					ticketDisable.prop('disabled', true);
				}
			});

			$('.directorist-booking-timing-type input[name="bdb_slot_status"]').each((index, element) => {
				if ($('#slot_status').is(':checked')) {
					$('#dataDom').addClass('directorist-slot-active');
				} else if ($('#time_picker').is(':checked')) {
					$('#dataDom').removeClass('directorist-slot-active');
				}
			});


			/* Duplicate time dropdown */
			let duplicateTimeToggle = document.querySelector('.directorist-btn--duplicate-time-toggle');
			let duplicateTimeDropdown = document.querySelector('.directorist-booking-time-duplicate-dropdown');
			let duplicateTimeDropdownContent = document.querySelector('.directorist-booking-time-duplicate-dropdown__content');
			if (duplicateTimeToggle !== null) {
				duplicateTimeToggle.addEventListener('click', function (e) {
					e.preventDefault();
					e.currentTarget.nextElementSibling.classList.toggle('directorist-booking-time-duplicate-dropdown__content--active');
				})
			}
			document.body.addEventListener('click', function (e) {
				if (!e.target.closest('.directorist-booking-time-duplicate-dropdown') && duplicateTimeDropdownContent !== null && duplicateTimeDropdownContent.classList.contains('directorist-booking-time-duplicate-dropdown__content--active')) {
					duplicateTimeDropdownContent.classList.remove('directorist-booking-time-duplicate-dropdown__content--active');
				}
			})

			/* Select all checkboxes */
			let selectAllCheckboxes = document.getElementById('directorist-checkbox-select-all');
			let daysCheckboxes = document.getElementsByName('directorist-booking-duplicate-days');

			//Hide active day from copy list
			setTimeout(() => {
				let activeDayDataKey = $(".directorist-booking-weekdays-tab-nav__item.directorist-active").attr("data-key");
				$("[data-day-id='" + activeDayDataKey + "']").closest('.directorist-checkbox').hide();
			}, 500);

			//Select function
			function checkboxesSelect() {
				var ele = document.getElementsByName('directorist-booking-duplicate-days');
				for (var i = 0; i < ele.length; i++) {
					if (ele[i].type == 'checkbox')
						ele[i].checked = true;
				}
			}

			//deSelect function
			function checkboxesDeSelect() {
				var ele = document.getElementsByName('directorist-booking-duplicate-days');
				for (var i = 0; i < ele.length; i++) {
					if (ele[i].type == 'checkbox')
						ele[i].checked = false;

				}
			}

			//Toggle selection
			if (selectAllCheckboxes !== null) {
				selectAllCheckboxes.addEventListener('change', function (e) {
					if (this.checked === true) {
						checkboxesSelect();

					} else {
						checkboxesDeSelect();
					}
				})
			}

			//deSelect 'select all' checkbox if any item marked unchecked
			daysCheckboxes.forEach(elm => {
				elm.addEventListener('change', function (e) {
					if (elm.checked === false) {
						selectAllCheckboxes.checked = false;
					}
					if (document.querySelectorAll('.directorist-booking-time-duplicate-dropdown__content__inner .directorist-checkbox:not(.directorist-checkbox--select-all) input:checked').length === daysCheckboxes.length) {
						selectAllCheckboxes.checked = true;
					}
				})
			})

			//Reset all checkbox
			let resetCheckbox = document.querySelector('.directorist-booking-duplicate-dropdown-reset');
			if (resetCheckbox !== null) {
				resetCheckbox.addEventListener('click', function (e) {
					e.preventDefault();
					checkboxesDeSelect();
					selectAllCheckboxes.checked = false;
				})
			}
		}
		bookigTypeDefault();
		window.addEventListener('directorist-reload-plupload', bookigTypeDefault);
	});

	// booking type
	$('body').on('change', 'input[name="bdb_booking_type"]', function () {
		if ($(this).is(':checked') && $(this).data('booking-type') === 'service') {
			bookingService().show();
			bookingGuests().show();
			bookingEvent().hide();
			bookingRent().hide();
		}

		if ($(this).is(':checked') && $(this).data('booking-type') === 'event') {
			bookingEvent().css('display', 'block');
			bookingService().hide();
			bookingGuests().hide();
			bookingRent().hide();
		}
		if ($(this).is(':checked') && $(this).data('booking-type') === 'rent') {
			bookingRent().show();
			bookingEvent().hide();
			bookingService().hide();
			bookingGuests().show();
		}
	});

	$('body').on('change', '.directorist-booking-timing-type input[name="bdb_slot_status"]', function () {
		if ($('#slot_status').is(':checked')) {
			availableTime().hide();
			availableTimeText().hide();
			displaySlot().show();
			slotAvailable().show();
			$('#dataDom').addClass('directorist-slot-active');
		} else if ($('#time_picker').is(':checked')) {
			displaySlot().hide();
			slotAvailable().hide();
			availableTime().show();
			availableTimeText().show();
			$('#dataDom').removeClass('directorist-slot-active');
		}
	});

	$('body').on('change', '#display_slot_available_text', function () {
		if ($(this).is(':checked')) {
			slotTextDisable.prop('disabled', false);
			$('.directorist-booking-slot-available-text').removeClass('directorist-booking-disabled');
		} else {
			slotTextDisable.prop('disabled', true);
			$('.directorist-booking-slot-available-text').addClass('directorist-booking-disabled');
		}
	});
	if ($('#display_slot_available_text').is(':checked')) {
		slotTextDisable.prop('disabled', false);
		$('.directorist-booking-slot-available-text').removeClass('directorist-booking-disabled');
	} else {
		slotTextDisable.prop('disabled', true);
		$('.directorist-booking-slot-available-text').addClass('directorist-booking-disabled');
	}

	$('body').on('change', '#display_available_time', function () {
		if ($(this).is(':checked')) {
			textDisable.prop('disabled', false);
			$('.directorist-booking-available-time-text').removeClass('directorist-booking-disabled');
		} else {
			textDisable.prop('disabled', true);
			$('.directorist-booking-available-time-text').addClass('directorist-booking-disabled');
		}
	});
	if ($('#display_available_time').is(':checked')) {
		textDisable.prop('disabled', false);
		$('.directorist-booking-available-time-text').removeClass('directorist-booking-disabled');
	} else {
		textDisable.prop('disabled', true);
		$('.directorist-booking-available-time-text').addClass('directorist-booking-disabled');
	}

	$('body').on('change', '#display_available_ticket', function () {
		if ($(this).is(':checked')) {
			ticketDisable.prop('disabled', false);
			$('.directorist-available-ticket-text').removeClass('directorist-booking-disabled');
		} else {
			ticketDisable.prop('disabled', true);
			$('.directorist-available-ticket-text').addClass('directorist-booking-disabled');
		}
	});
	if ($('#display_available_ticket').is(':checked')) {
		ticketDisable.prop('disabled', false);
		$('.directorist-available-ticket-text').removeClass('directorist-booking-disabled');
	} else {
		ticketDisable.prop('disabled', true);
		$('.directorist-available-ticket-text').addClass('directorist-booking-disabled');
	}


	// hide booking
	const bdbExtras = () => $('.directorist-booking-extras');
	$('body').on('change', '#hide_booking', function () {
		bdbExtras().toggleClass('directorist-hide');
	});



})(jQuery);