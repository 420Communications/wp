<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

class Directorist_Booking_Database
{
    public $booking_id;
    public function __construct()
    {
        add_action('wp_ajax_update_booking_slots', array($this, 'ajax_update_booking_slots'));
        add_action('wp_ajax_nopriv_update_booking_slots', array($this, 'ajax_update_booking_slots'));
        add_action('wp_ajax_checking_booking_availability', array($this, 'ajax_checking_booking_availability'));
        add_action('wp_ajax_nopriv_checking_booking_availability', array($this, 'ajax_checking_booking_availability'));
        add_shortcode('directorist_booking_confirmation', array($this, 'directorist_booking_confirmation'));
        // for bookings dashboard
        add_action('wp_ajax_bdb_bookings_manage', array($this, 'ajax_bdb_bookings_manage'));
        add_action('wp_ajax_bdb_user_bookings_manage', array($this, 'ajax_bdb_user_bookings_manage'));
        add_action('wp_ajax_bdb_owner_approved_bookings_manage', array($this, 'ajax_bdb_owner_approved_bookings_manage'));
        //guest booking
        add_action( 'wp_ajax_guest_booking' , array( $this, 'ajax_guest_booking' ) );
        add_action( 'wp_ajax_nopriv_guest_booking', array( $this, 'ajax_guest_booking' ) );
    }

    public function ajax_guest_booking() {
        if( ! empty( $_POST['guest_email'] ) ) {
            $guest_email = $_POST['guest_email'];
            $string = $guest_email;
            $explode = explode("@", $string);
            array_pop($explode);
            $userName = join('@', $explode);
            //check if username already exist
            if ( username_exists( $userName ) ) {
                $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 1, 5);
                $userName = $userName . $random;
            }
            // Check if user exist by email
            if ( email_exists( $guest_email ) ) {
                wp_send_json(array(
                        'error' => true,
                        'error_msg' => __( 'Email already registered. Please login first', 'directorist-booking' ),
                ));
                die();
            } else {
                // lets register the user
                $reg_errors = new WP_Error;
                if ( empty( $reg_errors->get_error_messages() )) {
                    $password = wp_generate_password( 12, false );
                    $userdata = array(
                        'user_login' => $userName,
                        'user_email' => $guest_email,
                        'user_pass' => $password,
                    );
                    $user_id = wp_insert_user( $userdata ); // return inserted user id or a WP_Error
                    wp_set_current_user( $user_id, $guest_email );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'atbdp_user_registration_completed', $user_id );
                    update_user_meta( $user_id, '_atbdp_generated_password', $password );
                    wp_new_user_notification( $user_id, null, 'admin' ); // send activation to the admin
                    ATBDP()->email->custom_wp_new_user_notification_email( $user_id );
                }
            }
        }
    }

    public function directorist_booking_confirmation()
    {
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');
        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_style('bdb-style');
        if (!isset($_POST['value'])) {
           $cant_access = __("Sorry, you can't access", "directorist-booking");
            return $cant_access;
        }
        // here we adding booking into database
        if (isset($_POST['confirmed'])) {
            $_user_id = get_current_user_id();

            $data = json_decode(wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true);
            $error = false;
            $services = (isset($data['services'])) ? $data['services'] : false;
            $comment_services = false;
            if (!empty($services)) {
                $currency_abbr = get_option('bdb_currency');
                $currency_postion = get_option('bdb_currency_postion');
                $currency_symbol = bdb_Core_Listing::get_currency_symbol($currency_abbr);
                $comment_services = '<ul>';
                $bookable_services = bdb_get_bookable_services($data['listing_id']);
                $i = 0;
                foreach ($bookable_services as $key => $service) {
                    $i++;
                    if (in_array('service_' . $i, $services)) {
                        $comment_services .= '<li>' . $service['name'] . '<span class="services-list-price-tag">';
                        if (empty($service['price']) || $service['price'] == 0) {
                            $comment_services .= esc_html__('Free', 'directorist-booking');
                        } else {
                            if ($currency_postion == 'before') {
                                $comment_services .= $currency_symbol . ' ';
                            }
                            $comment_services .= $service['price'];
                            if ($currency_postion == 'after') {
                                $comment_services .= ' ' . $currency_symbol;
                            }
                        }
                        $comment_services .= '</span></li>';

                    }
                }
                $comment_services .= '</ul>';
            }
            $listing_meta    = get_post_meta($data['listing_id'], '', true);
            // detect if website was refreshed
            $instant_booking = get_post_meta($data['listing_id'], '_bdb_instant_booking', true);


            /* if (get_transient('bdb_last_booking' . $_user_id) == $data['listing_id'] . ' ' . $data['date_start'] . ' ' . $data['date_end']) {
               $message =  __('Sorry, it looks like you\'ve already made that reservation', 'directorist-booking');
                include BDB_TEMPLATES_DIR . 'booking-success.php';
                 return;
            } */

            set_transient('bdb_last_booking' . $_user_id, $data['listing_id'] . ' ' . $data['date_start'] . ' ' . $data['date_end'], 60 * 15);

            // because we have to be sure about listing type
            $listing_meta = get_post_meta($data['listing_id'], '', true);
            $listing_type = get_post_meta($data['listing_id'], '_bdb_booking_type', true);
            $booking_type                 = get_directorist_option('booking_type','all');
            $booking_type                 = !empty($booking_type) ? $booking_type : 'service';
            $listing_type                 = ( !empty($listing_type) && 'undefined' !== $listing_type ) ? $listing_type : $booking_type;
            $bdb_payment_booking          = get_post_meta( $data['listing_id'], '_bdb_payment_booking', true );
            $listing_owner = get_post_field('post_author', $data['listing_id']);

            switch ($listing_type) {
                case 'event' :

                    $comment = array(
                        'first_name' => $_POST['firstname'],
                        //'last_name' => $_POST['lastname'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'],
                        'message' => $_POST['message'],
                        'tickets' => $data['tickets'],
                        'service' => $comment_services,
                    );

                    $booking_id = self:: insert_booking(array(
                        'owner_id' => $listing_owner,
                        'listing_id' => $data['listing_id'],
                        'date_start' => $data['date_start'],
                        'date_end' => $data['date_start'],
                        'comment' => json_encode($comment),
                        'type' => 'reservation',
                        'price' => self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $data['tickets']),
                    ));

                    $already_sold_tickets = (int)get_post_meta($data['listing_id'], '_event_tickets_sold', true);
                    $sold_now = $already_sold_tickets + $data['tickets'];
                    update_post_meta($data['listing_id'], '_event_tickets_sold', $sold_now);

                    $status = apply_filters('bdb_event_default_status', 'waiting');
                    if( ! empty( $instant_booking ) ) {
                        $status = 'confirmed';
                    }
                    $changed_status = self:: set_booking_status($booking_id, $status);

                    break;

                case 'rent' :

                    // get default status
                    $status = apply_filters('bdb_rental_default_status', 'waiting');
                    if (!empty($instant_booking)) {
                        $status = 'confirmed';
                    }

                    // count free places
                    $free_places = self:: count_free_places($data['listing_id'], $data['date_start'], $data['date_end']);

                    if ($free_places > 0) {

                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                        //check count_per_guest

                        if ($count_per_guest) {

                            $multiply = 1;
                            if (isset($data['adults'])) $multiply = $data['adults'];

                            $price = self:: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'] );
                        } else {
                            $price = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end']);
                        }

                        $booking_id = self:: insert_booking(array(
                            'owner_id' => $listing_owner,
                            'listing_id' => $data['listing_id'],
                            'date_start' => $data['date_start'],
                            'date_end' => $data['date_end'],
                            'comment' => json_encode(array(
                                'first_name' => $_POST['firstname'],
                                //'last_name' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'phone' => $_POST['phone'],
                                'message' => $_POST['message'],
                                //'childrens' => $data['childrens'],
                                //'adults' => $data['adults'],
                                'service' => $comment_services,
                                // 'tickets' => $data['tickets']
                            )),
                            'type' => 'reservation',
                            'price' => $price,
                        ));

                        $status = apply_filters('bdb_event_default_status', 'waiting');
                        if ($instant_booking == 'check_on') {
                            $status = 'confirmed';
                        }
                        $changed_status = self:: set_booking_status($booking_id, $status);

                    } else {

                        $error = true;
                        $message = __('Unfortunately those dates are not available anymore.', 'directorist-booking');

                    }

                    break;

                case 'service' :

                    $status = apply_filters('bdb_service_default_status', 'waiting');
                    if (!empty($instant_booking)) {
                        $status = 'confirmed';
                    }
                    // when we dealing with opening hours
                    if (!isset($data['slot'])) {
                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                        //check count_per_guest

                        if ($count_per_guest) {

                            $multiply = 1;
                            if (isset($data['adults'])) $multiply = $data['adults'];

                            $price = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end']);
                        } else {
                            $price = self:: calculate_price( $data['listing_id'], $data['date_start'], $data['date_end'] );
                        }
                        $data_hour = $data['_hour'] ? $data['_hour'] : '';
                        $booking_id = self:: insert_booking(array(
                            'owner_id' => $listing_owner,
                            'listing_id' => $data['listing_id'],
                            'date_start' => $data['date_start'] . ' ' . $data_hour . ':00',
                            'date_end' => $data['date_end'] . ' ' . $data['_hour'] . ':00',
                            'comment' => json_encode(array('first_name' => $_POST['firstname'],
                                //'last_name' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'phone' => $_POST['phone'],
                                'adults' => !empty($data['adults']) ? $data['adults'] : '',
                                'message' => $_POST['message'],
                                'service' => $comment_services,

                            )),
                            'type' => 'reservation',
                            'price' => $price,
                        ));

                        $changed_status = self:: set_booking_status($booking_id, $status);

                    } else {

                        // here when we have enabled slots

                        $free_places = self:: count_free_places($data['listing_id'], $data['date_start'], $data['date_end'], $data['slot']);

                        if ($free_places > 0) {

                            $slot = json_decode(wp_unslash($data['slot']));

                            // converent hours to mysql format
                            $hours = explode('-', $slot[0]);
                            $hour_start = date("H:i:s", strtotime($hours[0]));
                            $hour_end = date("H:i:s", strtotime($hours[1]));

                            $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                            //check count_per_guest
                            $services = (isset($data['services'])) ? $data['services'] : false;
                            if ($count_per_guest) {

                                $multiply = 1;
                                if (isset($data['adults'])) $multiply = $data['adults'];

                                $price = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end'] );
                            } else {
                                $price = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end']);
                            }
                            $booking_id = self:: insert_booking(array(
                                'owner_id' => $listing_owner,
                                'listing_id' => $data['listing_id'],
                                'date_start' => $data['date_start'] . ' ' . $hour_start,
                                'date_end' => $data['date_end'] . ' ' . $hour_end,
                                'comment' => json_encode(array('first_name' => $_POST['firstname'],
                                    //'last_name' => $_POST['lastname'],
                                    'email' => $_POST['email'],
                                    'phone' => $_POST['phone'],
                                    //'childrens' => $data['childrens'],
                                    'adults' => !empty($data['adults']) ? $data['adults'] : 0,
                                    'message' => $_POST['message'],
                                    'service' => $comment_services,

                                )),
                                'type' => 'reservation',
                                'price' => $price,
                            ));


                            $status = apply_filters('bdb_service_slots_default_status', 'waiting');
                            if (!empty($instant_booking)) {
                                $status = 'confirmed';
                            }

                            $changed_status = self:: set_booking_status($booking_id, $status);

                        } else {

                            $error = true;
                            $message = __('Those dates are not available.', 'directorist-booking');

                        }

                    }

                    break;
            }

            // when we have database problem with statuses
            if (!isset($changed_status)) {
                $message = __('We have some technical problem, please try again later or contact administrator.', 'directorist-booking');
                $error = true;
            }

            switch ($status) {

                case 'waiting' :

                    $message = esc_html__('Your booking is waiting for confirmation.', 'directorist-booking');

                    break;

                /*case 'confirmed' :

                    $message = esc_html__('', 'directorist-booking');

                    break;*/


                case 'cancelled' :

                    $message = esc_html__('Your booking was cancelled', 'directorist-booking');

                    break;
            }

            ob_start();
            include BDB_TEMPLATES_DIR . 'booking-success.php';
            return ob_get_clean();
        }


        $data = json_decode(wp_unslash($_POST['value']), true);
        if (isset($data['services'])) {
            $services = $data['services'];
        } else {
            $services = false;
        }

        // for slots get hours
        if (isset($data['slot'])) {
            $slot = json_decode(wp_unslash($data['slot']));
            $hour = $slot[0];

        } else if (isset($data['_hour'])) {
            $hour = $data['_hour'];
        }

        // prepare some data to template
        $data['submitteddata'] = htmlspecialchars($_POST['value']);

        //check listin type
        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
        //check count_per_guest

        if ($count_per_guest || $data['listing_type'] == 'event') {

            $multiply = 1;
            if (isset($data['adults'])) $multiply = $data['adults'];
            if (isset($data['tickets'])) $multiply = $data['tickets'];

            $data['price'] = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $data['tickets'] );
        } else {
            $data['price'] = self:: calculate_price($data['listing_id'], $data['date_start'], $data['date_end']);
        }
        if (isset($hour)) {
            $data['_hour'] = $hour;
        }

        ob_start();
        include BDB_TEMPLATES_DIR . 'booking-confirmation.php';
        return ob_get_clean();

        // if slots are sended change them into good form
        if (isset($data['slot'])) {

            // converent hours to mysql format
            $hours = explode(' - ', $slot[0]);
            $hour_start = date("H:i:s", strtotime($hours[0]));
            $hour_end = date("H:i:s", strtotime($hours[1]));
            // add hours to dates
            $data['date_start'] .= ' ' . $hour_start;
            $data['date_end'] .= ' ' . $hour_end;

        } else if (isset($data['_hour'])) {

            // when we dealing with normal hour from input we have to add second to make it real date format
            $hour_start = date("H:i:s", strtotime($hour));
            $data['date_start'] .= ' ' . $hour . ':00';
            $data['date_end'] .= ' ' . $hour . ':00';

        }
    }


    /*
     * Set booking status - we changing booking status only by this function
     *
     * @param  array $args list of parameters
     *
     * @return number of deleted records
     */
    public static function set_booking_status( $booking_id, $status,$order_id = '', $order_create = '' , $send_email = '' )
    {

        global $wpdb;

        $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `id`=' . esc_sql($booking_id), 'ARRAY_A');
        $booking_data = !empty($booking_data) ? $booking_data : array();
        $user_id = $booking_data['bookings_author'];
        $owner_id = $booking_data['owner_id'];
        $current_user_id = get_current_user_id();
        $listing_id = isset( $booking_data['listing_id'] ) ? $booking_data['listing_id'] : '';

        // get information about users
        $user_info  = get_userdata( $user_id );
        $owner_info = get_userdata( $owner_id );
        if( empty( $order_create ) ) {
             // only one time clicking blocking
             if ($booking_data['status'] == $status) return;
        }


        switch ($status) {

            // this is status when listing waiting for approval by owner
            case 'waiting' :
                if(!empty($order_id)) {
                    $update_values['order_id'] = $order_id;
                }
                $update_values['status'] = 'waiting';

                // mail for user
                $mail_to_user_args = array(
                    'email' => $user_info->user_email,
                    'booking' => $booking_data,
                );
                //do_action('bdb_mail_to_user_waiting_approval', $mail_to_user_args);
                $site_name              = get_bloginfo( 'name' );
                $sub = self::replace_in_content( get_directorist_option("bdb_mail_waiting_user_subject", __('Booking request for ==LISTING_TITLE== submitted successfully ', 'directorist-booking') ), $listing_id );
                $sub  = apply_filters( 'bdb_mail_waiting_user_subject', $sub );
                $body = self::replace_in_content( get_directorist_option("bdb_mail_waiting_user_body", __("Welcome ==USERNAME_WHO_BOOKED==,

                Your booking request has been submitted successfully and is waiting to be approved by the owner.

                Regards
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_mail_to_user_waiting_approval', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                if( 'no' != $send_email ) {
                    ATBDP()->email->send_mail($user_info->user_email, $sub, $body, $headers);
                }
                // mail for owner
                $mail_to_owner_args = array(
                    'email' => $owner_info->user_email,
                    'booking' => $booking_data,
                );
                $sub = self::replace_in_content( get_directorist_option("bdb_mail_waiting_owner_subject", __('New booking request for ==LISTING_TITLE==', 'directorist-booking') ), $listing_id );
                //do_action('bdb_mail_to_owner_new_reservation', $mail_to_owner_args);
                $site_name              = get_bloginfo( 'name' );
                $sub  = apply_filters( 'bdb_mail_waiting_owner_subject', $sub );
                $body = self::replace_in_content( get_directorist_option("bdb_mail_waiting_owner_body", __("
                Dear ==LISTING_OWNER==,

                You have received a new reservation from ==USER_EMAIL== for ==LISTING_TITLE== and is waiting to be approved in your Dashboard! ==CLICK_HERE== to review it.

                Regards,
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_mail_to_owner_new_reservation', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                if( 'no' != $send_email ) {
                    ATBDP()->email->send_mail($owner_info->user_email, $sub, $body, $headers);
                }
                break;

            // this is status when listing is confirmed by owner and waiting to payment
            case 'confirmed' :

                if(!empty($order_id)) {
                    $update_values['order_id'] = $order_id;
                }
                $update_values['status'] = 'confirmed';
                $site_name              = get_bloginfo( 'name' );
                $sub = self::replace_in_content( get_directorist_option("bdb_mail_approved_user_subject", __('Booking confirmation for ==LISTING_TITLE==') ), $listing_id );
                $sub  = apply_filters( 'bdb_mail_approved_user_subject', $sub );
                $body = self::replace_in_content( get_directorist_option("bdb_mail_approved_user_body", __("Hello ==USERNAME_WHO_BOOKED==,

                Congratulations! Your booking for ==LISTING_TITLE== has been confirmed.

                Regards,
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_approved_mail_traveler', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                if( 'no' != $send_email ) {
                    ATBDP()->email->send_mail($user_info->user_email, $sub, $body, $headers);
                }
                //$update_values['expiring'] = $expiring_date;
                // mail for user
               /* wp_mail( $user_info->user_email, __( 'Welcome traveler', 'directorist-booking' ), sprintf( __( 'Your reservation waiting for payment!', 'directorist-booking' )) );
                $mail_args = array(
                    'email' => $user_info->user_email,
                    'booking' => $booking_data,
                    //'expiration' => $expired_after,
                    //'payment_url' => $payment_url
                );
                do_action('bdb_mail_to_user_pay', $mail_args);*/


                break;

            // this is status when listing is confirmed by owner and already paid
            case 'paid' :

                // mail for owner
                $site_name              = get_bloginfo( 'name' );
                $sub  = self::replace_in_content( get_directorist_option("bdb_mail_paid_owner_subject", __('New payment received for "==LISTING_TITLE=="', 'directorist-booking') ), $listing_id );
                $body = self::replace_in_content( get_directorist_option("bdb_mail_paid_owner_body", __("Hello ==LISTING_OWNER==,

                You have received a new payment from ==USERNAME_WHO_BOOKED== for ==LISTING_TITLE==.

                Regards,
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_paid_mail_owner', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                if( 'no' != $send_email ) {
                    ATBDP()->email->send_mail( $owner_info->user_email, $sub, $body, $headers );
                }
                $mail_to_owner_args = array(
                    'email' => $owner_info->user_email,
                    'booking' => $booking_data,
                );
                do_action('bdb_mail_to_owner_paid', $mail_to_owner_args);
                $site_name              = get_bloginfo( 'name' );
                $sub  =  self::replace_in_content( get_directorist_option("bdb_mail_paid_user_subject", __('Payment confirmation for ==LISTING_TITLE==', 'directorist-booking') ), $listing_id );
                $body =  self::replace_in_content( get_directorist_option("bdb_mail_paid_user_body", __("Hello ==USERNAME_WHO_BOOKED==,

                You have successfully made a payment for ==LISTING_TITLE==.

                Thank you,
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_paid_mail_traveler', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                if( 'no' != $send_email ) {
                    ATBDP()->email->send_mail($user_info->user_email, $sub, $body, $headers);
                }
                // mail for user
                //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'directorist-booking' ), __( 'Your is paid!', 'directorist-booking' ) );
                $update_values['status'] = 'paid';
                $update_values['expiring'] = '';


                break;

            // this is status when listing is confirmed by owner and already paid
            case 'cancelled' :

                $site_name              = get_bloginfo( 'name' );
                $sub  =  self::replace_in_content( get_directorist_option("bdb_mail_cancel_user_subject", __('Booking Cancellation for ==LISTING_TITLE==', 'directorist-booking') ), $listing_id );
                $sub  = apply_filters( 'bdb_mail_cancelled_user_subject', $sub );
                $body =  self::replace_in_content( get_directorist_option("bdb_mail_cancel_user_body", __("Hello ==USERNAME_WHO_BOOKED==,

                Your booking for ==LISTING_TITLE== has been cancelled.

                Regards,
                ", 'directorist-booking') ), $listing_id, $user_info );
                $body = apply_filters('bdb_cancelled_mail_traveler', $body);
                $body = atbdp_email_html($sub, $body);
                $headers  = "From: {$site_name} <{$owner_info->user_email}>\r\n";
                ATBDP()->email->send_mail($user_info->user_email, $sub, $body, $headers);
                // mail for user
                //wp_mail( $user_info->user_email, __( 'Welcome traveler', 'directorist-booking' ), __( 'Your reservation was cancelled.', 'directorist-booking' ) );
                $mail_to_user_args = array(
                    'email' => $user_info->user_email,
                    'booking' => $booking_data,
                );
                do_action('bdb_mail_to_user_canceled', $mail_to_user_args);
                // delete order if exist
                /*if ($booking_data['order_id']) {
                    $order = wc_get_order($booking_data['order_id']);
                    $order->update_status('cancelled', __('Order is cancelled.', 'directorist-booking'));
                }*/
                $comment = json_decode($booking_data['comment']);
                $tickets_from_order = $comment->tickets;

                $sold_tickets = (int)get_post_meta($booking_data['listing_id'], "_event_tickets_sold", true);

                update_post_meta($booking_data['listing_id'], "_event_tickets_sold", $sold_tickets - $tickets_from_order);

                $update_values['status'] = 'cancelled';
                $update_values['expiring'] = '';

                break;

        }

        return $wpdb->update($wpdb->prefix . 'directorist_booking', $update_values, array('id' => $booking_id));

    }

    public static function replace_in_content( $content, $listing_id = 0, $info = null ) {
        $l_title        = get_the_title( $listing_id );
        $listing_url    = get_permalink( $listing_id );
        $site_name      = get_option('blogname');
        if( $info ) {
            $user_email     = $info->data->user_email;
            $user_name      = $info->data->display_name;
        }
        $owner_id      = get_post_field( 'post_author', $listing_id );
        $owner_name    = get_the_author_meta( 'display_name', $owner_id );
        $dashboard_link = ATBDP_Permalink::get_dashboard_page_link() . '/#active_my_booking';
        $find_replace = array(
            '==LISTING_TITLE=='         => ! empty( $l_title ) ? $l_title : '',
            '==USER_EMAIL=='            => ! empty( $user_email ) ? $user_email : '',
            '==LISTING_OWNER=='         => ! empty( $owner_name ) ? $owner_name : '',
            '==CLICK_HERE=='            => sprintf( '<a href="%s">%s</a>', $dashboard_link, __( 'Click Here', 'directorist-booking' ) ),
            '==USERNAME_WHO_BOOKED=='   => ! empty( $user_name ) ? $user_name : '',
            '==SITE_NAME=='             => $site_name,
        );

        return nl2br( strtr( $content, $find_replace ) );
    }

    public static function booking_delete( $booking_id ) {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . 'directorist_booking',
            ['id' => $booking_id],
            [ '%d' ]
        );
    }

    private static function delete_calender_bookings( $args )  {

        global $wpdb;

        return $wpdb -> delete( $wpdb -> prefix . 'directorist_booking', $args );

    }


    /*
     * Insert booking with args
     *
     * @param  array $args list of parameters
     *
     */
    public static function insert_booking($args)
    {

        global $wpdb;

        $insert_data = array(
            'bookings_author' => get_current_user_id(),
            'owner_id' => $args['owner_id'],
            'listing_id' => $args['listing_id'],
            'date_start' => date("Y-m-d H:i:s", strtotime($args['date_start'])),
            'date_end' => date("Y-m-d H:i:s", strtotime($args['date_end'])),
            'comment' => $args['comment'],
            'type' => $args['type'],
            'created' => current_time('mysql')
        );

        if (isset($args['order_id'])) $insert_data['order_id'] = $args['order_id'];
        if (isset($args['expiring'])) $insert_data['expiring'] = $args['expiring'];
        if (isset($args['status'])) $insert_data['status'] = $args['status'];
        if (isset($args['price'])) $insert_data['price'] = $args['price'];

        $wpdb->insert($wpdb->prefix . 'directorist_booking', $insert_data);

        return $wpdb->insert_id;

    }

    public static function update_reservations( $listing_id, $dates ) {

        // delecting old reservations
        self :: delete_calender_bookings ( array(
            'listing_id' => $listing_id,  
            'owner_id' => get_current_user_id(),
            'type' => 'reservation',
            'comment' => 'owner reservations') );

        // update by new one reservations
        foreach ( $dates as $date ) {
            
            self :: insert_booking( array(
                'listing_id' => $listing_id,  
                'type' => 'reservation',
                'owner_id' => get_current_user_id(),
                'date_start' => $date,
                'date_end' => date( 'Y-m-d H:i:s', strtotime('+23 hours +59 minutes +59 seconds', strtotime($date) ) ),
                'comment' =>  __('owner reservations', 'directorist-booking'),
                'order_id' => NULL,
                'status' => 'owner_reservations'
            )); 

        }

       
    }

    public static function update_special_prices( $listing_id, $prices ) {

        // deleting old special prices
        self :: delete_calender_bookings ( array(
            'listing_id' => $listing_id,  
            'owner_id' => get_current_user_id(),
            'type' => 'special_price') );

        // update by new one special prices
        foreach ( $prices as $date => $price) {
            
            self :: insert_booking( array(
                'listing_id' => $listing_id,  
                'type' => 'special_price',
                'owner_id' => get_current_user_id(),
                'date_start' => $date,
                'date_end' => $date,
                'comment' =>  $price,
                'order_id' => NULL,
                'status' => NULL
            ));
            
        }

    }

    public static function calculate_price($listing_id, $date_start, $date_end, $ticket = '')
    {

        // get all special prices between two dates from bdb settings special prices
        $special_prices_results  = self:: get_bookings_result($date_start, $date_end, array('listing_id' => $listing_id, 'type' => 'special_price'));
        $booking_type            = get_post_meta( $listing_id, '_bdb_booking_type', true );
        $normal_price            = (int) get_post_meta( $listing_id, '_price', true );
        $weekend_price           = (int) get_post_meta( $listing_id, '_bdb_weekend_price', true );
        

        // prepare special prices to nice array
        foreach ($special_prices_results as $result) {
            $special_prices[$result['date_start']] = $result['comment'];
        }

        $reservation_price = (int) get_post_meta($listing_id, '_bdb_reservation_fee', true);
        $price = $reservation_price;

        if( ! empty( $booking_type ) && 'event' == $booking_type && ! empty( $normal_price ) ) {
            
            $price   =  ( $normal_price * (int)$ticket ) + $price;
            
        } elseif ( 'rent' == $booking_type ) {

            $firstDay = new DateTime( $date_start );
            $lastDay  = new DateTime( $date_end . '23:59:59') ;

            //fix for not calculating last day of leaving
            if ( $date_start != $date_end ) $lastDay -> modify('-1 day');
            
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod( $firstDay, $interval, $lastDay );
        
            foreach ( $period as $current_day ) {

                // get current date in sql format
                $date = $current_day->format("Y-m-d 00:00:00");
                $day = $current_day->format("N");

                if ( isset( $special_prices[$date] ) ) 
                {
                    $price += $special_prices[$date];
                }
                else {

                    // when we have weekends
                    if ( isset( $weekend_price ) && $day == 6 || $day == 7) {
                        $price += $weekend_price;
                    } 
                    else $price += $normal_price;

                }

            }
        }

        return $price;

    }

    public function count_free_places($listing_id, $date_start, $date_end, $slot = 0)
    {
        $_slots                            = get_post_meta($listing_id, '_bdb', true);
        $slot_status                       = get_post_meta($listing_id, '_bdb_slot_status', true);
        $booking_type                      = get_post_meta($listing_id, '_bdb_booking_type', true);

        $free_places = 1;
        if ( 'rent' != $booking_type && ! empty( $slot_status ) && 'time_slot' == $slot_status ) {
            $slot = json_decode(wp_unslash($slot));

            // converent hours to mysql format
            $hours = explode(' - ', $slot[0]);
            $hour_start = date("H:i:s", strtotime($hours[0]));
            $hour_end = date("H:i:s", strtotime($hours[1]));

            // add hours to dates
            $date_start .= ' ' . $hour_start;
            $date_end .= ' ' . $hour_end;

            // get day and number of slot
            $day_and_number = explode('|', $slot[1]);
            $slot_day = $day_and_number[0];
            $slot_number = $day_and_number[1];

            // get amount of slots
            $slots_amount = $_slots[$slot_day][$slot_number];
            $slots_amount = $slots_amount['slots'];

            $free_places = $slots_amount;
        } else if ( 'rent' == $booking_type || 'time_picker' == $slot_status) {

            // if there are no slots then always is free place and owner menage himself
            return 1;

        }

        // get reservations to this slot and calculate amount
        $result = self:: get_bookings_result($date_start, $date_end, array('listing_id' => $listing_id, 'type' => 'reservation'));

        // count how many reservations we have already for this slot
        $reservetions_amount = count($result);


        // minus reservations from database
        $free_places -= $reservetions_amount;
        return $free_places;
    }

    public function ajax_checking_booking_availability()
    {
        $_POST['slot'] = isset($_POST['slot']) ? $_POST['slot'] : '';
        $ajax_out['free_places'] = $this->count_free_places($_POST['listing_id'], $_POST['date_start'], $_POST['date_end'], $_POST['slot']);

        $multiply = 1;
        if(isset($_POST['adults'])) $multiply = $_POST['adults'];
        if(isset($_POST['tickets'])) $multiply = $_POST['tickets'];

        $services = (isset($_POST['services'])) ? $_POST['services'] : false ;

        $ajax_out['price'] = self :: calculate_price( $_POST['listing_id'],  $_POST['date_start'], $_POST['date_end'] );

        wp_send_json_success($ajax_out);

    }

    /**
     * Get bookings result between dates filtred by arguments
     *
     * @param date $date_start in format YYYY-MM-DD
     * @param date $date_end in format YYYY-MM-DD
     * @param array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_bookings_result($date_start, $date_end, $args = '', $by = 'booking_date', $limit = '', $offset = '', $all = '')
    {
        global $wpdb;

        // if(strlen($date_start)<10){
        //     if($date_start) { $date_start = $date_start.' 00:00:00'; }
        //     if($date_end) { $date_end = $date_end.' 23:59:59'; }
        // }

        // setting dates to MySQL style
        $date_start = esc_sql(date("Y-m-d H:i:s", strtotime($wpdb->esc_like($date_start))));
        $date_end = esc_sql(date("Y-m-d H:i:s", strtotime($wpdb->esc_like($date_end))));

        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";
        if (is_array($args)) {
            foreach ($args as $index => $value) {

                $index = esc_sql($index);
                $value = esc_sql($value);

                if ($value == 'approved') {
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'paid') )";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if ($value == 'cancelled') {
                    $FILTER_CANCELLED = '';
                }

            }
        }
        if ($all == 'users') {
            $FILTER = "AND NOT comment='owner reservations'";
        } else {
            $FILTER = '';
        }

        if ($limit != '') $limit = " LIMIT " . esc_sql($limit);

        if (is_numeric($offset)) $offset = " OFFSET " . esc_sql($offset);

        switch ($by) {

            case 'booking_date' :
                $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE ((' $date_start' >= `date_start` AND ' $date_start' <= `date_end`) OR ('$date_end' >= `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` <= '$date_end')) $WHERE $FILTER $limit $offset", "ARRAY_A");
                break;


            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL)  $WHERE $FILTER_CANCELLED $limit $offset", "ARRAY_A");
                break;

        }

        return $result;
    }

    public function ajax_update_booking_slots()
    {

        $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : '';
        $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '';
        $date_end   = isset($_POST['date_end']) ? $_POST['date_end'] : '';
        $dayOfWeak  = date('w', strtotime($date_start));
        $slots      = get_post_meta($listing_id, '_bdb', true);
        $display_slot_available_text       = get_post_meta($listing_id, '_bdb_display_slot_available_text', true);
        $slot_available_text               = get_post_meta($listing_id, '_bdb_slot_available_text', true);
        $slot_available_text               = !empty($slot_available_text) ? sanitize_text_field($slot_available_text) : '';
        if ($dayOfWeak == 0) {
            $actual_day = 6;
        } else {
            $actual_day = $dayOfWeak - 1;
        }
        $_slots_for_day = !empty($slots[$actual_day]) ? $slots[$actual_day] : '';
        $new_slots = array();
        if (is_array($_slots_for_day) && !empty($_slots_for_day)) {

            foreach ($_slots_for_day as $key => $_slot_for_day) {
                $free_places = $_slot_for_day['slots'];
                $hour_start = date("H:i:s", strtotime($_slot_for_day['start']));
                $hour_end = date("H:i:s", strtotime($_slot_for_day['close']));
                $date_start = $_POST['date_start'] . ' ' . $hour_start;
                $date_end = $_POST['date_end'] . ' ' . $hour_end;
                $results = self:: get_bookings_result($date_start, $date_end, array('listing_id' => $listing_id, 'type' => 'reservation'));
                $reservations_amount = count($results);
                $free_places -= $reservations_amount;
                if ($free_places > 0) {
                    $start = date('h:i a', strtotime($_slot_for_day['start']));
                    //$start = $_slot_for_day['start'];
                    $close = date('h:i a', strtotime($_slot_for_day['close']));
                    //$close = $_slot_for_day['close'];
                    $new_slots[] = $start . ' - ' . $close . '|' . $free_places;

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
            ob_start();
            ?>
            <input id="slot" type="hidden" name="slot" value=""/>
            <input id="listing_id" type="hidden" name="listing_id" value="<?php echo $listing_id; ?>"/>
            <?php
            foreach($new_slots as $number => $slot) {
                $slot = explode('|' , $slot);
                //var_dump($slot[1]);
                ?>
                <!-- Time Slot -->
                <div class="time-slot" data-day="<?php echo $actual_day; ?>">
                    <input type="radio" name="time-slot" id="<?php echo $actual_day . '|' . $number; ?>"
                           value="<?php echo $actual_day . '|' . $number; ?>">
                    <label for="<?php echo $actual_day . '|' . $number; ?>">
                        <p class="day"></p>
                        <strong><?php echo $slot[0]; ?></strong>
                        <?php if(!empty($display_slot_available_text)) { ?>
                        <span><?php echo $slot[1] .' '. $slot_available_text;?></span>
                        <?php } ?>
                    </label>
                </div>
                <?php
            }
            $ajax_out = ob_get_clean();
        } else {
            // no slots
        }
        wp_send_json_success(!empty($ajax_out) ? $ajax_out : '');
    }

    /**
     * Get latest bookings number of bookings between dates filtered by arguments, used for pagination
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_newest_bookings( $args, $limit, $offset = 0 )  {

        global $wpdb;

        // setting dates to MySQL style

        // filter by parameters from args
        $WHERE = '';

        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND status IN ('confirmed','paid')";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }

            }
        }
        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);
        //if(isset($args['status']) && $args['status'])
        $offset = " OFFSET " . esc_sql($offset);

        // when we searching by created date automatically we looking where status is not null because we using it for dashboard booking
        $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE  NOT comment = 'owner reservations' $WHERE ORDER BY `" . $wpdb->prefix . "directorist_booking`.`created` DESC $limit $offset", "ARRAY_A" );


        return $result;

    }

    /**
     * Get bookings between dates filtred by arguments
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_bookings( $date_start, $date_end, $args = '', $by = 'booking_date', $limit = '', $offset = '' ,$all = '')  {

        global $wpdb;

        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";
        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND ( (`$index` = 'confirmed') OR (`$index` = 'paid') )";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if( $value == 'cancelled' ){
                    $FILTER_CANCELLED = '';
                }

            }
        }
        if($all == 'users'){
            $FILTER = "AND NOT comment='owner reservations'";
        } else {
            $FILTER = '';
        }

        if ( $limit != '' ) $limit = " LIMIT " . esc_sql($limit);

        if ( is_numeric($offset)) $offset = " OFFSET " . esc_sql($offset);

        switch ($by)
        {

            case 'booking_date' :
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE ((' $date_start' >= `date_start` AND ' $date_start' <= `date_end`) OR ('$date_end' >= `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` <= '$date_end')) $WHERE $FILTER $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;


            case 'created_date' :
                // when we searching by created date automatically we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL)  $WHERE $FILTER_CANCELLED $limit $offset", "ARRAY_A" );
                break;

        }

        return $result;

    }

    /**
     * Get maximum number of bookings between dates filtered by arguments, used for pagination
     *
     * @param  date $date_start in format YYYY-MM-DD
     * @param  date $date_end in format YYYY-MM-DD
     * @param  array $args fot where [index] - name of column and value of index is value
     *
     * @return array all records informations between two dates
     */
    public static function get_bookings_max( $date_start, $date_end, $args = '', $by = 'booking_date' )  {

        global $wpdb;

        // setting dates to MySQL style
        $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
        $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );

        // filter by parameters from args
        $WHERE = '';
        $FILTER_CANCELLED = "AND NOT status='cancelled' ";
        if ( is_array ($args) )
        {
            foreach ( $args as $index => $value )
            {

                $index = esc_sql( $index );
                $value = esc_sql( $value );

                if ( $value == 'approved' ){
                    $WHERE .= " AND (`$index` = 'confirmed') OR (`$index` = 'paid')";
                } else {
                    $WHERE .= " AND (`$index` = '$value')";
                }
                if( $value == 'cancelled' ){
                    $FILTER_CANCELLED = '';
                }

            }
        }

        switch ($by)
        {

            case 'booking_date' :
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE ((' $date_start' >= `date_start` AND ' $date_start' <= `date_end`) OR ('$date_end' >= `date_start` AND '$date_end' <= `date_end`) OR (`date_start` >= ' $date_start' AND `date_end` <= '$date_end')) AND NOT comment='owner reservations' $WHERE $FILTER_CANCELLED", "ARRAY_A" );
                break;


            case 'created_date' :
                // when we searching by created date automaticly we looking where status is not null because we using it for dashboard booking
                $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE (' $date_start' <= `created` AND ' $date_end' >= `created`) AND (`status` IS NOT NULL) AND  NOT comment = 'owner reservations' $WHERE $FILTER_CANCELLED", "ARRAY_A" );
                break;

        }

        return $wpdb->num_rows;

    }

    public static function ajax_bdb_bookings_manage(  )  {
        $current_user_id = get_current_user_id();
        // when we only changing status
        if ( isset( $_POST['status']) ) {

            wp_send_json_success( self :: set_booking_status( $_POST['booking_id'], $_POST['status']) );
        }

        $args = array (
            'owner_id' => get_current_user_id(),
            'type' => 'reservation'
        );
        $offset = ( absint( $_POST['page'] ) - 1 ) * absint( 10 );
        $limit =  10;

        if ( isset($_POST['listing_id']) &&  $_POST['listing_id'] != 'show_all'  ) $args['listing_id'] = $_POST['listing_id'];
        if ( isset($_POST['listing_status']) && $_POST['listing_status'] != 'show_all'  ) $args['status'] = $_POST['listing_status'];


        if ( $_POST['dashboard_type'] != 'user' ){
            if($_POST['date_start']==''){
                $ajax_out = self::get_newest_bookings( $args, $limit, $offset );
                $bookings_max_number = bdb_count_bookings(get_current_user_id(),$args['status']);
            } else {
                $ajax_out = self :: get_bookings( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date', $limit, $offset,'users' );
                $bookings_max_number = self :: get_bookings_max( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date');

            }
        }


//        if user dont have listings show his reservations
        if ( $_POST['dashboard_type'] == 'user' ) {
            unset( $args['owner_id'] );
            unset($args['status']);
            unset($args['listing_id']);

            $args['bookings_author'] = get_current_user_id();
            if($_POST['date_start']==''){
                $ajax_out = self :: get_newest_bookings( $args, $limit, $offset );
                $bookings_max_number = bdb_count_my_bookings(get_current_user_id(),$args['status']);
            } else {
                $ajax_out = self :: get_bookings( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date', $limit, $offset, 'users' );
                $bookings_max_number = self :: get_bookings_max( $_POST['date_start'], $_POST['date_end'], $args, 'booking_date');
            }

        }

        $result = array();
        //$template_loader = new bdb_Core_Template_Loader;
        $max_number_pages = ceil($bookings_max_number/$limit);
        ob_start();
        if($ajax_out){

            foreach ($ajax_out as $key => $value) {
                if ( isset($_POST['dashboard_type']) && $_POST['dashboard_type'] == 'user' ) {
                    include BDB_TEMPLATES_DIR .'/bookings/content-user-booking.php';
                    //$template_loader->set_template_data( $value )->get_template_part( 'booking/content-user-booking' );
                } else {
                    include BDB_TEMPLATES_DIR .'/bookings/content-booking.php';
                    //$template_loader->set_template_data( $value )->get_template_part( 'booking/content-booking' );
                }

            }
        }

        $result['pagination'] = bdb_ajax_pagination( $max_number_pages, absint( $_POST['page'] ) );
        $result['html'] = ob_get_clean();
        wp_send_json_success( $result );

    }


    public static function ajax_bdb_user_bookings_manage(  )  {
        $current_user_id = get_current_user_id();
        // when we only changing status
        if ( isset( $_POST['status']) ) {

            wp_send_json_success( self :: set_booking_status( $_POST['booking_id'], $_POST['status']) );
        }

        $args = array (
            'owner_id' => get_current_user_id(),
            'type' => 'reservation'
        );
        $offset = ( absint( $_POST['page'] ) - 1 ) * absint( 10 );
        $limit =  10;

        if ( isset($_POST['listing_id']) &&  $_POST['listing_id'] != 'show_all'  ) $args['listing_id'] = $_POST['listing_id'];
        if ( isset($_POST['listing_status']) && $_POST['listing_status'] != 'show_all'  ) $args['status'] = $_POST['listing_status'];


        unset( $args['owner_id'] );
        unset($args['status']);
        unset($args['listing_id']);

        $args['bookings_author'] = get_current_user_id();
        $ajax_out = self :: get_newest_bookings( $args, $limit, $offset );
        $bookings_max_number = bdb_count_my_bookings(get_current_user_id());


        $result = array();
        //$template_loader = new bdb_Core_Template_Loader;
        $max_number_pages = ceil($bookings_max_number/$limit);
        ob_start();
        if($ajax_out){

            foreach ($ajax_out as $key => $value) {
                include BDB_TEMPLATES_DIR .'/bookings/content-user-booking.php';
            }
        }

        $result['pagination'] = bdb_ajax_pagination( $max_number_pages, absint( $_POST['page'] ) );
        $result['html'] = ob_get_clean();
        wp_send_json_success( $result );

    }

    public static function ajax_bdb_owner_approved_bookings_manage(  )  {
        $current_user_id = get_current_user_id();
        // when we only changing status
        if ( isset( $_POST['status']) ) {
            if( 'deleted' == $_POST['status'] ) {
                wp_send_json_success( self :: booking_delete( $_POST['booking_id']) );
            }
            wp_send_json_success( self :: set_booking_status( $_POST['booking_id'], $_POST['status']) );
        }

        $args = array (
            'owner_id' => get_current_user_id(),
            'type' => 'reservation'
        );
        $offset = ( absint( $_POST['page'] ) - 1 ) * absint( 10 );
        $limit =  10;

        if ( isset($_POST['listing_id']) &&  $_POST['listing_id'] != 'show_all'  ) $args['listing_id'] = $_POST['listing_id'];
        if ( isset($_POST['listing_status']) && $_POST['listing_status'] != 'show_all'  ) $args['status'] = $_POST['listing_status'];

        $ajax_out = self :: get_newest_bookings( $args, $limit, $offset );
        $bookings_max_number = bdb_count_bookings(get_current_user_id(),$args['status']);



        $result = array();
        //$template_loader = new bdb_Core_Template_Loader;
        $max_number_pages = ceil($bookings_max_number/$limit);
        ob_start();
        if($ajax_out){

            foreach ($ajax_out as $key => $value) {
                include BDB_TEMPLATES_DIR .'/bookings/content-booking.php';
            }
        }

        $result['pagination'] = bdb_ajax_pagination( $max_number_pages, absint( $_POST['page'] ) );
        $result['html'] = ob_get_clean();
        wp_send_json_success( $result );

    }
}
