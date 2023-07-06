<?php
wp_enqueue_style('bdb-daterangepicker-style');
wp_enqueue_style('bdb-style');
wp_enqueue_script('bdb-moment');
wp_enqueue_script('bdb-flatpickr');
wp_enqueue_script('bdb-daterangepicker');
wp_enqueue_script('bdb-main-js');
$values                            = get_post_meta(get_the_ID(), '_bdb', true);
$slot_status                       = get_post_meta(get_the_ID(), '_bdb_slot_status', true);
$reservation_guest                 = get_post_meta(get_the_ID(), '_bdb_reservation_guest', true);
$payment_booking                   = get_post_meta(get_the_ID(), '_bdb_payment_booking', true);
if( $payment_booking ) {
    $reservation_fee                   = get_post_meta(get_the_ID(), '_bdb_reservation_fee', true);
}
$display_slot_available_text       = get_post_meta(get_the_ID(), '_bdb_display_slot_available_text', true);
$display_available_time            = get_post_meta(get_the_ID(), '_bdb_display_available_time', true);
$display_available_time            = get_post_meta(get_the_ID(), '_bdb_display_available_time', true);
$display_available_ticket          = get_post_meta(get_the_ID(), '_bdb_display_available_ticket', true);
$available_ticket_text             = get_post_meta(get_the_ID(), '_bdb_available_ticket_text', true);
$slot_available_text               = get_post_meta(get_the_ID(), '_bdb_slot_available_text', true);
$available_time_text               = get_post_meta(get_the_ID(), '_bdb_available_time_text', true);
$set_booking_type                  = get_directorist_option('booking_type', 'service');
$set_booking_type                  = !empty( $set_booking_type ) ? $set_booking_type : 'service';
$booking_type                      = get_post_meta(get_the_ID(), '_bdb_booking_type', true);
$booking_type                      = ( !empty( $booking_type ) && 'undefined' !== $booking_type ) ? $booking_type : $set_booking_type;
$slot_available_text               = !empty($slot_available_text) ? sanitize_text_field($slot_available_text) : '';
$login_page                        = ATBDP_Permalink::get_login_page_url();
$guest_booking                     = get_directorist_option( 'bdb_guest_booking' );
$bdb_request_booking_label         = get_directorist_option( 'bdb_request_booking_label', __('Request Booking', 'directorist-booking') );$bdb_book_ticket_label             = get_directorist_option( 'bdb_book_ticket_label', __('Book Ticket', 'directorist-booking') );
$bdb_login_booking_label           = get_directorist_option( 'bdb_login_booking_label', __('Login for Booking', 'directorist-booking') );
$bdb_reservation_fee_label         = get_directorist_option( 'bdb_reservation_fee_label', __('Reservation Fee', 'directorist-booking') );

$records = BD_Booking()->bdb_booking_database->get_bookings( date('Y-m-d H:i:s'),  date('Y-m-d H:i:s', strtotime('+3 years')), array( 'listing_id' => get_the_ID(), 'type' => 'reservation' ) );
       

if( $records ) {
    foreach ($records as $record)
    {

        // when we have one day reservation
        if ($record['date_start'] == $record['date_end'])
        {
            $disable_dates[] = date('Y-m-d', strtotime($record['date_start']));
        } else {
            
            // if we have many days reservations we have to add every date between this days
            $period = new DatePeriod(
                new DateTime( date( 'Y-m-d', strtotime( $record['date_start']) ) ),
                new DateInterval( 'P1D' ),
                new DateTime( date( 'Y-m-d', strtotime( $record['date_end'] . ' +1 day') ) )
            );

            foreach ($period as $day_number => $value) {
                $disable_dates[] = $value->format('Y-m-d');  
            }

        }

    }

    if ( isset( $disable_dates ) && 'rent' == $booking_type )	
        {
            ?>
            <script>
                var disabedDates = <?php echo ( ! empty( $booking_type && 'rent' == $booking_type ) ) ? json_encode( $disable_dates ) : ''; ?>;
            </script>
            <?php
        }
        
}

$days_list = array(
    0 => __('Monday', 'directorist-booking'),
    1 => __('Tuesday', 'directorist-booking'),
    2 => __('Wednesday', 'directorist-booking'),
    3 => __('Thursday', 'directorist-booking'),
    4 => __('Friday', 'directorist-booking'),
    5 => __('Saturday', 'directorist-booking'),
    6 => __('Sunday', 'directorist-booking'),
);
echo $args['before_widget'];
echo '<div class="atbd_widget_title">';
echo $args['before_title'] . esc_html(apply_filters('widget_title', $title)) . $args['after_title'];
echo '</div>';
?>
<form id="form-booking" method="post" action="<?php echo bdb_booking_confirmation_page(); ?>">

    <?php if( 'service' == $booking_type || 'rent' == $booking_type ) { ?>

        <div class="directorist-form-group">
            <input type="text" id="date-picker" booking_type ="<?php echo $booking_type; ?>" readonly="readonly" class="directorist-booking-date-picker-service directorist-form-element" autocomplete="off" placeholder="<?php esc_attr_e('Date', 'directorist-booking'); ?>" value="" />
        </div>

    <?php } ?>    

    <?php if ('service' == $booking_type) { ?>
        <?php if ( !empty( $slot_status ) && 'time_slot' == $slot_status ) { ?>
            <div class="directorist-booking-dropdown-wrap">
                <div class="directorist-booking-panel-dropdown directorist-booking-time-slots-dropdown">
                    <a href="#" class="directorist-booking-ts-dropdown-toggle" data-placeholder="<?php esc_html_e('Time Slots', 'directorist-booking') ?>"><?php esc_html_e('Time Slots', 'directorist-booking') ?></a>

                    <div class="directorist-booking-panel-dropdown-content">
                        <div class="directorist-booking-no-slots"><?php esc_html_e('No slots for this day', 'directorist-booking') ?></div>
                        <div class="directorist-booking-panel-dropdown-scrollable">
                            <input id="slot" type="hidden" name="slot" value="" />
                            <div>
                                <?php
                                foreach ($values as $day => $day_slots) {
                                    if (empty($day_slots)) continue;

                                    foreach ($day_slots as $key => $day_slot) {
                                        $slot = $day_slot['slots'];
                                        $number = $key; ?>

                                        <?php if( ! empty( $day_slot['start'] ) && ! empty( $day_slot['close'] ) ) { ?>

                                            <div class="time-slot" data-day="<?php echo $day; ?>">
                                                <input type="radio" name="time-slot" id="<?php echo $day . '|' . $number; ?>" value="<?php echo $day . '|' . $number; ?>">
                                                <label for="<?php echo $day . '|' . $number; ?>">
                                                    <span class="day"><?php echo $days_list[$day]; ?></span>
                                                    <strong><?php echo $day_slot['start'] . '-' . $day_slot['close']; ?></strong>
                                                    <?php if (!empty($display_slot_available_text)) { ?>
                                                        <span><?php echo $slot . ' ' . $slot_available_text; ?></span>
                                                    <?php } ?>
                                                </label>
                                            </div>

                                        <?php } ?>

                                <?php
                                    }
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="directorist-booking-time-picker-wrap">
                <div class="directorist-form-group">
                    <input type="time" class="directorist-booking-time-picker directorist-form-element flatpickr-input active" name="_hours">
                </div>
                <?php if (!empty($display_available_time)) { ?>
                    <div class="directorist-available-time-block">
                        <span><?php echo !empty($available_time_text) ? $available_time_text : __('Available Time'); ?>:</span>
                        <div class="directorist-available-time-list">

                        </div>
                    </div>
                <?php } ?>
            </div>
            <script>
                var availableDays = <?php echo !empty($values) ? json_encode($values, true) : json_encode('', true); ?>;
            </script>
        <?php } ?>
        <?php } elseif ('event' == $booking_type) {
        $event_ticket                      = get_post_meta(get_the_ID(), '_bdb_event_ticket', true);
        $event_ticket                      = !empty( $event_ticket ) ? $event_ticket : 0;
        $event_tickets_sold                = get_post_meta(get_the_ID(), '_event_tickets_sold', true);
        $event_tickets_sold                = !empty($event_tickets_sold) ? $event_tickets_sold : 0;
        $maximum_ticket_allowed            = get_post_meta(get_the_ID(), '_bdb_maximum_ticket_allowed', true);
        if( $payment_booking ) {
            $price                   = get_post_meta(get_the_ID(), '_price', true);
        }
        if ( isset( $event_tickets_sold ) ) {
            $available_ticket                  = $event_ticket - $event_tickets_sold;
        }
        if ( !empty( $available_ticket ) ) {
            $maximum_ticket_allowed = ($maximum_ticket_allowed < $available_ticket) ? $maximum_ticket_allowed : $available_ticket;
        ?>
            <div class="directorist-booking-event-tickets">
                <div class="directorist-dropdown directorist-dropdown-js atbd-drop-select">
                    <a href="" class="atbd-dropdown-toggle directorist-dropdown__toggle directorist-dropdown__toggle-js" id="tickets" data-drop-toggle="atbd-toggle"><?php _e('Qty','directorist-booking'); ?></a>
                    <div class="directorist-dropdown__links directorist-dropdown__links-js atbd-dropdown-items">
                        <?php for ( $i = 1; $i <= $maximum_ticket_allowed; $i++ ) { ?>
                        <a href="" class="atbd-dropdown-item" data-price="<?php echo ! empty( $price ) ? $price : 0; ?>" data-fee = "<?php echo ! empty( $reservation_fee ) ? $reservation_fee : 0; ?>" data-id="<?php echo $i; ?>"><?php echo $i; ?></a>
                       <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <p><?php _e('The tickets have sold out', 'directorist-booking'); ?></p>
        <?php } ?>
    <?php } ?>
    
    <?php if ( ( 'rent' == $booking_type || 'service' == $booking_type ) &&  ! empty( $reservation_guest ) ) { ?>
            <div class="directorist-dropdown directorist-dropdown-js atbd-drop-select">
                <a href="" class="atbd-dropdown-toggle adults directorist-dropdown__toggle directorist-dropdown__toggle-js" data-drop-toggle="atbd-toggle"><?php _e("Guest", "directorist-booking") ?></a>
                <div class="directorist-dropdown__links directorist-dropdown__links-js">
                    <?php
                    for ( $i = 1; $i <= $reservation_guest; $i++ ) { ?>
                        <a href="" class="atbd-dropdown-item"><?php printf("%s %s", $i, __("Person", "directorist-booking")) ?></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

    <div>
        <input id="listing_id" type="hidden" name="listing_id" value="<?php echo get_the_ID(); ?>" />
        <input id="booking" type="hidden" name="value" value="booking_form" />
        <input id="listing_type" type="hidden" name="listing_type" value="<?php echo !empty($booking_type) ? $booking_type : 'service'; ?>" />
        <?php if ( is_user_logged_in() || ! empty( $guest_booking ) ) { ?>
            <a href="#" class="directorist-btn directorist-btn-primary directorist-btn-sm directorist-book-now"><?php echo !empty( 'event' == $booking_type ) ? $bdb_book_ticket_label : $bdb_request_booking_label; ?></a>
        <?php } else { ?>
            <a href="<?php echo !empty($login_page) ? $login_page : ''; ?>" class="login-booking"><?php echo $bdb_login_booking_label; ?></a>
        <?php } ?>
    </div>
    <?php if( 'event' == $booking_type && ! empty( $available_ticket ) && ! empty( $display_available_ticket ) ) { ?>
    <div class="directorist-booking-available-ticket">
        <span><?php echo !empty( $available_ticket_text ) ? $available_ticket_text : __('Available Tickets :','directorist-booking'); ?></span>
        <strong><?php echo $available_ticket; ?></strong>
    </div>
    <?php } ?>
    <div class="directorist-booking-estimated-cost" style="display:none;">
        <strong><?php echo $bdb_reservation_fee_label; ?></strong>
        <span></span>
    </div>
    <div class="directorist-booking-error-message" style="display: none;">
        <?php esc_html_e('Unfortunately this request can\'t be processed. Try different dates please.', 'directorist-booking'); ?>
    </div>
</form>


<div id="info"></div>

<?php
echo $args['after_widget'];
?>
