<?php
$bdb_value                      = get_post_meta( $listing_id, '_bdb', true );
$hide_booking                   = get_post_meta( $listing_id, '_bdb_hide_booking', true );
$instant_booking                = get_post_meta( $listing_id, '_bdb_instant_booking', true );
$calender_unavailable           = get_post_meta( $listing_id, '_bdb_calender_unavailable', true );
$calender_price                 = get_post_meta( $listing_id, '_bdb_calender_price', true );
$payment_booking                = get_post_meta( $listing_id, '_bdb_payment_booking', true );
$Reservation_fee                = get_post_meta( $listing_id, '_bdb_reservation_fee', true );
$weekend_price                  = get_post_meta( $listing_id, '_bdb_weekend_price', true );
$reservation_guest              = get_post_meta( $listing_id, '_bdb_reservation_guest', true );
$slot_status                    = get_post_meta( $listing_id, '_bdb_slot_status', true );
$display_available_ticket       = get_post_meta( $listing_id, '_bdb_display_available_ticket', true );
$available_ticket_text          = get_post_meta( $listing_id, '_bdb_available_ticket_text', true );
$display_slot_available_text    = get_post_meta( $listing_id, '_bdb_display_slot_available_text', true );
$display_available_time         = get_post_meta( $listing_id, '_bdb_display_available_time', true );
$slot_available_text            = get_post_meta( $listing_id, '_bdb_slot_available_text', true );
$available_time_text            = get_post_meta( $listing_id, '_bdb_available_time_text', true );
$slot_status_checked            = !empty( $slot_status ) ? $slot_status : 'time_slot';
$booking_type                   = get_post_meta( $listing_id, '_bdb_booking_type', true );
$event_ticket                   = get_post_meta( $listing_id, '_bdb_event_ticket', true );
$maximum_ticket_allowed         = get_post_meta( $listing_id, '_bdb_maximum_ticket_allowed', true );
$bdb_booking_hide               = get_directorist_option( 'bdb_booking_hide', 1 );
$bdb_timing_type                = get_directorist_option( 'bdb_timing_type', 1 );
$set_booking_type               = get_directorist_option( 'booking_type', 'all' );
$set_booking_type               = !empty( $set_booking_type ) ? $set_booking_type : 'all';
$booking_type_label             = get_directorist_option( 'bdb_booking_type_label', 'Booking Type' );
$booking_type                   = !empty( $booking_type ) ? $booking_type : 'service';
$bdb_slot_label                 = get_directorist_option( 'bdb_slot_label', __( 'Choose Timing Type', 'directorist-booking' ) );
$bdb_booking_hiding_label       = get_directorist_option( 'bdb_booking_hiding_label', __( 'Check it to hide booking', 'directorist-booking' ) );
$bdb_payment_booking_label      = get_directorist_option( 'bdb_payment_booking_label', __( 'Enable Payment', 'directorist-booking' ) );
$bdb_instant_booking_label      = get_directorist_option( 'bdb_instant_booking_label', __( 'Enable Instant Booking', 'directorist-booking' ) );
$bdb_reservation_fee_label      = get_directorist_option( 'bdb_reservation_fee_label', __( 'Reservation Fee', 'directorist-booking' ) );
$bdb_maximum_guests_label       = get_directorist_option( 'bdb_maximum_guests_label', __( 'Maximum Number of Guests', 'directorist-booking' ) );
$bdb_available_ticket_label     = get_directorist_option( 'bdb_available_ticket_label', __( 'Available Tickets', 'directorist-booking' ) );
$bdb_perbooking_ticket_label    = get_directorist_option( 'bdb_perbooking_ticket_label', __( 'Tickets Allowed Per Booking', 'directorist-booking' ) );
if ( !empty( $bdb_value ) ) {
    $bdb_value = $bdb_value;
} else {
    $bdb_value = '';
}
;
?>
<div class="directorist-booking-wrap">
    <div class="directorist-switch directorist-hide-booking">
        <input type="checkbox" name="bdb_hide_booking" id="hide_booking" class="directorist-switch-input" <?=( !empty( $hide_booking ) ) ? 'checked' : '';?> >
        <label for="hide_booking" class="directorist-switch-label"> <?php echo !empty( $bdb_booking_hiding_label ) ? sanitize_text_field( $bdb_booking_hiding_label ) : __( 'Check it to hide booking', 'directorist-booking' ); ?></label>
    </div>

    <div class="directorist-booking-extras">
        <?php if ( !empty( $bdb_booking_hide ) ) {?>
        <?php }?>
        <?php if ( 'all' == $set_booking_type ) { ?>
        <div class="directorist-booking-type-selection">
            <p class="directorist-booking-label"><?php echo !empty( $booking_type_label ) ? $booking_type_label : 'Choose Booking Type'; ?></p>
            <div class="directorist-form-group">
                <div class="directorist-radio directorist-radio--checked directorist-radio-circle">
                    <input type="radio" id="bdb-booking-type-service" name="bdb_booking_type" data-booking-type="service" value="service" <?php checked( 'service', $booking_type ); ?>>
                    <label for="bdb-booking-type-service" class="directorist-radio__label"><?php _e( 'Service', 'directorist-booking' );?></label>
                </div>
                <div class="directorist-radio directorist-radio--checked directorist-radio-circle">
                    <input type="radio" id="bdb-booking-type-event" name="bdb_booking_type"  data-booking-type="event" value="event" <?php checked( 'event', $booking_type ); ?>>
                    <label for="bdb-booking-type-event" class="directorist-radio__label"><?php _e( 'Event', 'directorist-booking' );?></label>
                </div>
                <div class="directorist-radio directorist-radio--checked directorist-radio-circle">
                    <input type="radio" id="bdb-booking-type-rent" name="bdb_booking_type"  data-booking-type="rent" value="rent" <?php checked( 'rent', $booking_type ); ?>>
                    <label for="bdb-booking-type-rent" class="directorist-radio__label"><?php _e( 'Rent', 'directorist-booking' );?></label>
                </div>
            </div>
        </div>
        <?php } else { ?>
            <input type="hidden" name="bdb_booking_type" data-booking-type="<?php echo $set_booking_type; ?>" value="<?php echo $set_booking_type; ?>">
        <?php } ?>

        <div class="directorist-booking-event directorist-mb-15">
            <div class="directorist-booking-available-ticket-check directorist-mb-15">
                <div class="directorist-checkbox directorist-checkbox-primary">
                    <input type="checkbox" name="bdb_display_available_ticket"
                        id="display_available_ticket" <?=(  ( 'on' === $display_available_ticket ) || ( '1' === $display_available_ticket ) ) ? 'checked' : $available_ticket_checked;?> >
                    <label for="display_available_ticket" class="directorist-checkbox__label"> <?php _e( 'Display Available Tickets', 'directorist-booking' );?> </label>
                </div>
            </div>
            <div class="directorist-available-ticket-text directorist-booking-disabled directorist-mb-15">
                <div class="directorist-form-group">
                    <label class="directorist-booking-label" for="bdb_available_ticket_text"> <?php _e( 'Available Tickets Text', 'directorist-booking' );?> </label>
                    <input type="text" class="directorist-form-element" name="bdb_available_ticket_text" value="<?php echo !empty( $available_ticket_text ) ? $available_ticket_text : 'Available Tickets :'; ?>"
                        id="bdb_available_ticket_text">
                </div>
            </div>
            <div class="directorist-booking-event-ticket directorist-mb-15">
                <div class="directorist-form-group">
                    <label class="directorist-booking-label" for="event_ticket"> <?php echo !empty( $bdb_available_ticket_label ) ? $bdb_available_ticket_label : 'Available Tickets'; ?></label>
                    <input type="number" class="directorist-form-element" name="bdb_event_ticket" value="<?php echo !empty( $event_ticket ) ? $event_ticket : ''; ?>"
                        id="event_ticket" min="0">
                </div>
            </div>
            <div class="directorist-max-allowed-tickets directorist-mb-15">
                <div class="directorist-form-group">
                    <label class="directorist-booking-label" for="maximum_ticket_allowed"> <?php echo !empty( $bdb_perbooking_ticket_label ) ? $bdb_perbooking_ticket_label : __( 'Tickets allowed per booking', 'directorist-booking' ); ?> <span class="directorist-tooltip"></span></label>
                    <input type="number" class="directorist-form-element" name="bdb_maximum_ticket_allowed" value="<?php echo !empty( $maximum_ticket_allowed ) ? $maximum_ticket_allowed : 5; ?>"
                        id="maximum_ticket_allowed">
                </div>
            </div>
        </div>
        <div class="directorist-booking-service">
            <?php if ( !empty( $bdb_timing_type ) ) {?>
            <div class="directorist-booking-timing-type">
                <p class="directorist-booking-label"><?php echo $bdb_slot_label; ?> <span class="directorist-tooltip" data-label="<?php _e( 'Choose your preferred timing type. The Time Slot option lets listing owners set their availability of services and the Time Picker option allows users to pick their preferred booking time for a service.
', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></p>
                <div class="directorist-booking-timing-type-select">
                    <div class="directorist-radio directorist-radio-theme-admin directorist-radio-circle">
                        <input type="radio" name="bdb_slot_status"
                               id="slot_status" value="time_slot" <?php checked( $slot_status_checked, 'time_slot' );?>>
                        <label for="slot_status" class="directorist-radio__label"><?php _e( 'Time Slot', 'directorist-booking' );?></label>
                    </div>
                    <div class="directorist-radio directorist-radio-theme-admin directorist-radio-circle">
                        <input type="radio" name="bdb_slot_status"
                               id="time_picker" value="time_picker" <?php checked( $slot_status_checked, 'time_picker' );?>>
                        <label for="time_picker" class="directorist-radio__label"><?php _e( 'Time Picker', 'directorist-booking' );?></label>
                    </div>
                </div>
            </div>
            <?php }?>
            <div class="directorist-booking-slot-available-wrapper">
                <div class="directorist-booking-slot-available-check directorist-mb-10">
                    <div class="directorist-checkbox directorist-checkbox-primary">
                        <input type="checkbox" name="bdb_display_slot_available_text" id="display_slot_available_text" <?=(  ( 'on' === $display_slot_available_text ) || ( '1' === $display_slot_available_text ) ) ? 'checked' : $slot_available_checked;?> >
                        <label for="display_slot_available_text" class="directorist-checkbox__label"> Display <span>"Slot Available"</span> Text<?php //_e( 'Display Slot Available Text', 'directorist-booking' );?> </label>
                    </div>
                </div>
                <div class="directorist-booking-slot-available-text directorist-booking-disabled">
                    <div class="directorist-form-group">
                        <label class="directorist-booking-label" for="bdb_slot_available_text"> Change Text<?php //_e( 'Slots Available Text', 'directorist-booking' );?> </label>
                        <input type="text" class="directorist-form-element" name="bdb_slot_available_text" value="<?php echo !empty( $slot_available_text ) ? $slot_available_text : 'Slots Available'; ?>"
                            id="bdb_slot_available_text">
                    </div>
                </div>
            </div>

            <div class="directorist-booking-available-time-wrapper">
                <div class="directorist-booking-available-time directorist-mb-10">
                    <div class="directorist-checkbox directorist-checkbox-primary">
                        <input type="checkbox" name="bdb_display_available_time"
                            id="display_available_time" <?=(  ( 'on' === $display_available_time ) || ( '1' === $display_available_time ) ) ? 'checked' : $available_time_checked;?> >
                        <label for="display_available_time" class="directorist-checkbox__label"> Display <span>"Available Time"</span> Text<?php //_e( 'Display Available Time', 'directorist-booking' );?> </label>
                    </div>
                </div>
                <div class="directorist-booking-available-time-text directorist-booking-disabled">
                    <div class="directorist-form-group">
                        <label class="directorist-booking-label" for="bdb_available_time_text"> Change <span>"Available Time"</span> Text<?php //_e( 'Available Time Text', 'directorist-booking' );?> </label>
                        <input type="text" class="directorist-form-element" name="bdb_available_time_text" value="<?php echo !empty( $available_time_text ) ? $available_time_text : 'Available Time'; ?>"
                            id="bdb_available_time_text">
                    </div>
                </div>
            </div>

            <?php
$hours = array(
    'monday_hours'    => !empty( $bdb_value[0] ) ? $bdb_value[0] : array(),
    'tuesday_hours'   => !empty( $bdb_value[1] ) ? $bdb_value[1] : array(),
    'wednesday_hours' => !empty( $bdb_value[2] ) ? $bdb_value[2] : array(),
    'thursday_hours'  => !empty( $bdb_value[3] ) ? $bdb_value[3] : array(),
    'friday_hours'    => !empty( $bdb_value[4] ) ? $bdb_value[4] : array(),
    'saturday_hours'  => !empty( $bdb_value[5] ) ? $bdb_value[5] : array(),
    'sunday_hours'    => !empty( $bdb_value[6] ) ? $bdb_value[6] : array(),
);
wp_localize_script( 'bdb-admin-main', 'booking_hours', array(
    'monday'    => __( 'Monday', 'directorist-booking' ),
    'tuesday'   => __( 'Tuesday', 'directorist-booking' ),
    'wednesday' => __( 'Wednesday', 'directorist-booking' ),
    'thursday'  => __( 'Thursday', 'directorist-booking' ),
    'friday'    => __( 'Friday', 'directorist-booking' ),
    'saturday'  => __( 'Saturday', 'directorist-booking' ),
    'sunday'    => __( 'Sunday', 'directorist-booking' ),
) );
//  wp_localize_script( 'bdb-front-main', 'booking_hours', $hours );

?>
            <div class="directorist-booking-time-slots-configure">
                <p class="directorist-booking-label">Configure Time Slots</p>
                <input id="bdb_hours" type="hidden" value='<?php echo json_encode( $hours ); ?>''>
                <div id="weeksDom" class="directorist-booking-weekdays-tab-nav"></div>
                <div id="dataDom"></div>
                <div class="directorist-booking-add-day directorist-mb-15">
                    <button class="directorist-btn directorist-btn--add-hours" type="button" id="bhAddNew"><span class="las la-plus"></span><?php _e( 'Add Hours', 'directorist-booking' );?></button>

                    <!-- Duplicate time dropdown -->
                    <div class="directorist-booking-time-duplicate-dropdown">
                        <button class="directorist-btn directorist-btn--duplicate-time-toggle" type="button">Copy The Time For <span class="las la-angle-down"></span></button>
                        <div class="directorist-booking-time-duplicate-dropdown__content">
                            <div class="directorist-booking-time-duplicate-dropdown__content__inner">
                                <div class="directorist-checkbox directorist-checkbox-primary directorist-checkbox--select-all">
                                    <input type="checkbox" name="" id="directorist-checkbox-select-all">
                                    <label for="directorist-checkbox-select-all" class="directorist-checkbox__label">Select All</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-monday" class="directorist-booking-duplicate-day" data-day-id="0">
                                    <label for="directorist-booking-duplicate-monday" class="directorist-checkbox__label">Monday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-tuesday" class="directorist-booking-duplicate-day" data-day-id="1">
                                    <label for="directorist-booking-duplicate-tuesday" class="directorist-checkbox__label">Tuesday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-wednesday" class="directorist-booking-duplicate-day" data-day-id="2">
                                    <label for="directorist-booking-duplicate-wednesday" class="directorist-checkbox__label">Wednesday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-thursday" class="directorist-booking-duplicate-day" data-day-id="3">
                                    <label for="directorist-booking-duplicate-thursday" class="directorist-checkbox__label">Thursday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-friday" class="directorist-booking-duplicate-day" data-day-id="4">
                                    <label for="directorist-booking-duplicate-friday" class="directorist-checkbox__label">Friday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-saturday" class="directorist-booking-duplicate-day" data-day-id="5">
                                    <label for="directorist-booking-duplicate-saturday" class="directorist-checkbox__label">Saturday</label>
                                </div>
                                <div class="directorist-checkbox directorist-checkbox-primary">
                                    <input type="checkbox" name="directorist-booking-duplicate-days" id="directorist-booking-duplicate-sunday" class="directorist-booking-duplicate-day" data-day-id="6">
                                    <label for="directorist-booking-duplicate-sunday" class="directorist-checkbox__label">Sunday</label>
                                </div>
                            </div>

                            <div class="directorist-booking-time-duplicate-dropdown__content__footer">
                                <button class="directorist-btn directorist-primary" id="diretorist-booking-btn-copy" type="button"><?php _e( 'Save', 'directorist-booking' );?></button>
                                <a href="#" class="directorist-booking-duplicate-dropdown-reset">Reset All</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <style>

            #weeksDom button{
                text-transform: capitalize;
            }

            .directorist-week-day-disable {
                display: none;
            }

            .directorist-week-day-disable.directorist-active {
                display: block;
            }
            </style>
        </div>

        <div class="directorist-booking-reservation-fee">
            <div class="directorist-form-group">
                <label class="directorist-booking-label" for="reservation_fee"> <?php echo !empty( $bdb_reservation_fee_label ) ? sanitize_text_field( $bdb_reservation_fee_label ) : __( 'Reservation Fee', 'directorist-booking' ); ?> <span class="directorist-tooltip" data-label="<?php _e( 'One time fee for booking', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></label>
                <div class="directorist-booking-input-more">
                    <input type="number" class="directorist-form-element" placeholder="0" name="bdb_reservation_fee" value="<?php echo !empty( $Reservation_fee ) ? $Reservation_fee : ''; ?>" id="reservation_fee" min="0">
                    <span for="reservation_fee"><?php echo get_directorist_option('g_currency', 'USD');?></span>
                </div>
            </div>
        </div>

        <div class="directorist-booking-weekend-price directorist-booking-rent directorist-booking-rental">
            <div class="directorist-form-group">
                <label class="directorist-booking-label" for="bdb_weekend_price"> <?php _e( 'Weekend Price', 'directorist-booking' ); ?> <span class="directorist-tooltip" data-label="<?php _e( 'Default price for weekend', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></label>
                <div class="directorist-booking-input-more">
                <input type="number" class="directorist-form-element directorist-weekend-price" placeholder="0" name="bdb_weekend_price" value="<?php echo ! empty( $weekend_price ) ? $weekend_price : ''; ?>" id="bdb_weekend_price" min="0">
                    <span for="bdb_weekend_price"><?php echo get_directorist_option('g_currency', 'USD');?></span>
                </div>
            </div>
        </div>

        <div class="directorist-booking-guest-reservation">
            <div class="directorist-form-group">
                <label class="directorist-booking-label" for="reservation_guest"> <?php echo !empty( $bdb_maximum_guests_label ) ? sanitize_text_field( $bdb_maximum_guests_label ) : __( 'Maximum Number of Guests', 'directorist-booking' ); ?> <span class="directorist-tooltip" data-label="<?php _e( 'Choose Maximum number of Guests Per Reservation.', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></label>
                <input type="number" class="directorist-form-element" placeholder="0" name="bdb_reservation_guest" value="<?php echo !empty( $reservation_guest ) ? $reservation_guest : ''; ?>"
                    id="reservation_guest" min="0">
            </div>
        </div>

        <div class="directorist-booking-options">
            <div class="diretorist-booking-payment directorist-mb-15">
                <div class="directorist-switch directorist-checkbox-primary">
                    <input type="checkbox" name="bdb_payment_booking" id="payment_booking" class="directorist-switch-input" <?=( !empty( $payment_booking ) ) ? 'checked' : '';?> >
                    <label for="payment_booking" class="directorist-switch-label"> <?php echo !empty( $bdb_payment_booking_label ) ? sanitize_text_field( $bdb_payment_booking_label ) : __( 'Enable Payment ', 'directorist-booking' ); ?> <span class="directorist-tooltip" data-label="<?php _e( 'Enable it to let users pay a booking fee.', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></label>
                </div>
            </div>
            <div class="directorist-booking-instant directorist-mb-15">
                <div class="directorist-switch directorist-checkbox-primary">
                    <input type="checkbox" name="bdb_instant_booking" id="instant_booking" class="directorist-switch-input" <?=( !empty( $instant_booking ) ) ? 'checked' : '';?> >
                    <label for="instant_booking" class="directorist-switch-label"> <?php echo !empty( $bdb_instant_booking_label ) ? sanitize_text_field( $bdb_instant_booking_label ) : __( 'Enable Instant Booking ', 'directorist-booking' ); ?> <span class="directorist-tooltip" data-label="<?php _e( 'Enable it to instantly approve booking requests.', 'directorist-booking' );?>"><i class="fa fa-question-circle"></i></span></label>
                </div>
            </div>
        </div>
        <div class='directorist-booking-rental'>
            <div class="directorist-booking-calendar">
                <h4>Availability Calendar</h4>
                <div class="directorist-booking-calendar__alert" role="alert">
                    <p>Click date in the calendar to mark the day as unavailable.</p>
                    <p>Click price to change the individual price.</p>
                </div>
            </div>
            <div class='directorist-booking-rent'>
                <input type="hidden" class="bdb_calender_unavailable" name="bdb_calender_unavailable" value="<?php echo ! empty( $calender_unavailable ) ? esc_attr ( $calender_unavailable ) : ''; ?>">
                <input type="hidden" class="bdb_calender_price" name="bdb_calender_price" value="<?php echo ! empty( $calender_price ) ? esc_attr ( $calender_price ) : ''; ?>">
                <?php  echo BD_Booking()->bdb_rent_calendar->getCalendarHTML(); ?>
            </div>
        </div>
    </div>

</div><!-- ends: .bdb-wrapper -->
