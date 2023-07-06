<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

class Directorist_Booking_Payment
{
    public function __construct()
    {
        add_filter('atbdp_checkout_form_final_data', array($this, 'bdb_checkout_form_data'), 10, 2);
        add_action('atbdp_order_status_changed', array($this, 'atbdp_order_status_changed'),10,3);
        add_action('atbdp_order_created', array($this, 'atbdp_order_created'),10,3);
        add_action('atbdp_cc_form', array($this, 'atbdp_cc_form'));
        add_filter('atbdp_payment_receipt_data', array($this, 'atbdp_payment_receipt_data'), 10, 3);
        add_filter('atbdp_order_details', array($this, 'atbdp_order_details'), 10, 3);
        add_filter('atbdp_order_items', array($this, 'atbdp_order_items'), 10, 4);
        add_filter('atbdp_payment_receipt_button_text', array($this, 'atbdp_payment_receipt_button_text'), 10, 2);
        add_filter('atbdp_payment_receipt_button_link', array($this, 'atbdp_payment_receipt_button_link'), 10, 2);
        add_filter('atbdp_enable_monetization_checkout', array($this, 'atbdp_enable_monetization_checkout'));
        add_filter('atbdp_featured_active_checkout', array($this, 'atbdp_featured_active_checkout'));
        add_filter( 'atbdp_order_for', array( $this, 'atbdp_order_for' ), 10, 2 );
        add_filter( 'atbdp_checkout_not_now_link', array( $this, 'atbdp_checkout_not_now_link' ), 10, 2 );
    }

    public function atbdp_checkout_not_now_link( $url ) {
        if( isset( $_POST['confirmed']) ) {
             $url = ATBDP_Permalink::get_dashboard_page_link();
        }
        return $url;
    }

    public function atbdp_order_for( $featured, $order_id ) {
        $booking_id = get_post_meta( $order_id, '_booking_id', true);
        if( ! empty( $booking_id ) ) {
            $featured = __( 'Booking', 'directorist-booking' );
        }
        return $featured;
    }
    public function bdb_checkout_form_data($data,$listing_id) {

        $reservation_fee = get_post_meta($listing_id, '_bdb_reservation_fee', true);
        $p_title = get_the_title($listing_id);
        $datas = array();
        if(isset($_POST['confirmed'])) {
            $_user_id = get_current_user_id();

            $data = json_decode(wp_unslash(htmlspecialchars_decode(wp_unslash($_POST['value']))), true);
            $error = false;
            /* if (get_transient('bdb_last_booking' . $_user_id) == $data['listing_id'] . ' ' . $data['date_start'] . ' ' . $data['date_end']) {
                $message =  __('Sorry, it looks like you\'ve already made that reservation', 'directorist-booking');
                include BDB_TEMPLATES_DIR . 'booking-success.php';
               die();
            } */

            set_transient('bdb_last_booking' . $_user_id, $data['listing_id'] . ' ' . $data['date_start'] . ' ' . $data['date_end'], 60 * 15);
            $services = (isset($data['services'])) ? $data['services'] : false;
            $comment_services = false;
            $listing_meta    = get_post_meta($data['listing_id'], '', true);
            // detect if website was refreshed
            $instant_booking = get_post_meta($data['listing_id'], '_bdb_instant_booking', true);
            // because we have to be sure about listing type
            $listing_meta = get_post_meta($data['listing_id'], '', true);
            $listing_type = get_post_meta($data['listing_id'], '_bdb_booking_type', true);
            $listing_owner = get_post_field('post_author', $data['listing_id']);
            $tickets      = ( 'event' == $listing_type ) ? $data['tickets'] : 0;

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

                    $booking_id = BD_Booking()->bdb_booking_database->insert_booking(array(
                        'owner_id' => $listing_owner,
                        'listing_id' => $data['listing_id'],
                        'date_start' => $data['date_start'],
                        'date_end' => $data['date_start'],
                        'comment' => json_encode($comment),
                        'type' => 'reservation',
                        'price' =>BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $data['tickets'] ),
                    ));

                    $already_sold_tickets = (int)get_post_meta($data['listing_id'], '_event_tickets_sold', true);
                    $sold_now = $already_sold_tickets + $data['tickets'];
                    update_post_meta($data['listing_id'], '_event_tickets_sold', $sold_now);

                    $status = apply_filters('bdb_event_default_status', 'waiting');
                    if ($instant_booking == 'check_on') {
                        $status = 'waiting';
                    }
                    $changed_status = BD_Booking()->bdb_booking_database->set_booking_status($booking_id, $status);

                    break;

                case 'rent' :

                    // get default status
                    $status = apply_filters('bdb_rental_default_status', 'waiting');

                    // count free places
                    $free_places = BD_Booking()->bdb_booking_database->count_free_places($data['listing_id'], $data['date_start'], $data['date_end']);

                    if ($free_places > 0) {

                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                        //check count_per_guest

                        if ($count_per_guest) {

                            $multiply = 1;
                            if (isset($data['adults'])) $multiply = $data['adults'];

                            $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);
                        } else {
                            $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], 1, $services);
                        }

                        $booking_id = BD_Booking()->bdb_booking_database->insert_booking(array(
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
                                'adults' => isset( $data['adults'] ) ? $data['adults'] : '',
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
                        $changed_status = BD_Booking()->bdb_booking_database->set_booking_status($booking_id, $status);

                    } else {

                        $error = true;
                        $message = __('Unfortunately those dates are not available anymore.', 'directorist-booking');

                    }

                    break;

                case 'service' :

                    $status = apply_filters('bdb_service_default_status', 'waiting');
                    if (!empty($instant_booking)) {
                        $status = 'waiting';
                    }
                    // when we dealing with opening hours
                    if (!isset($data['slot'])) {
                        $count_per_guest = get_post_meta($data['listing_id'], "_count_per_guest", true);
                        //check count_per_guest

                        if ($count_per_guest) {

                            $multiply = 1;
                            if (isset($data['adults'])) $multiply = $data['adults'];

                            $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);
                        } else {
                            $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], 1, $services);
                        }

                        $booking_id = BD_Booking()->bdb_booking_database->insert_booking(array(
                            'owner_id' => $listing_owner,
                            'listing_id' => $data['listing_id'],
                            'date_start' => $data['date_start'] . ' ' . $data['_hour'] . ':00',
                            'date_end' => $data['date_end'] . ' ' . $data['_hour'] . ':00',
                            'comment' => json_encode(array('first_name' => $_POST['firstname'],
                                //'last_name' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'phone' => $_POST['phone'],
                                'adults' => isset( $data['adults'] ) ? $data['adults'] : '',
                                'message' => $_POST['message'],
                                'service' => $comment_services,

                            )),
                            'type' => 'reservation',
                            'price' => $price,
                        ));

                        //$changed_status = BD_Booking()->bdb_booking_database->set_booking_status($booking_id, $status);

                    } else {

                        // here when we have enabled slots

                        $free_places = BD_Booking()->bdb_booking_database->count_free_places($data['listing_id'], $data['date_start'], $data['date_end'], $data['slot']);

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

                                $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $multiply, $services);
                            } else {
                                $price = BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], 1, $services);
                            }
                            $booking_id = BD_Booking()->bdb_booking_database->insert_booking(array(
                                'owner_id' => $listing_owner,
                                'listing_id' => $data['listing_id'],
                                'date_start' => $data['date_start'] . ' ' . $hour_start,
                                'date_end' => $data['date_end'] . ' ' . $hour_end,
                                'comment' => json_encode(array('first_name' => $_POST['firstname'],
                                    //'last_name' => $_POST['lastname'],
                                    'email' => $_POST['email'],
                                    'phone' => $_POST['phone'],
                                    //'childrens' => $data['childrens'],
                                    'adults' => isset($data['adults']) ? $data['adults'] : 0,
                                    'message' => $_POST['message'],
                                    'service' => $comment_services,

                                )),
                                'type' => 'reservation',
                                'price' => $price,
                            ));


                            $status = apply_filters('bdb_service_slots_default_status', 'waiting');
                            if (!empty($instant_booking)) {
                                $status = 'waiting';
                            }

                            $changed_status = BD_Booking()->bdb_booking_database->set_booking_status($booking_id, $status);

                        } else {

                            $error = true;
                            $message = __('Those dates are not available.', 'directorist-booking');

                        }

                    }

                    break;
            }
            $this->booking_id = !empty($booking_id) ? $booking_id : '';
            $datas[] = array(
                'type' => 'header',
                'title' => __('Booking for ', 'directorist-booking') . $p_title
            );

            $datas[] = array(
                'type' => 'checkbox',
                'name' => 'booking',
                'value' => 1,
                'selected' => 1,
                'title' => __('Booking for ', 'directorist-booking') . $p_title,
                'desc' => __('Booking charge for this listing ', 'directorist-booking'),
                'price' => BD_Booking()->bdb_booking_database->calculate_price($data['listing_id'], $data['date_start'], $data['date_end'], $tickets )
            );
            return $datas;
        }

        return $data;
    }

    public function atbdp_order_details($order_details, $order_id, $listing_id){
        if(isset($_POST['confirmed']) && 'done' == $_POST['confirmed']) {
            $p_title = get_the_title($listing_id);
            $reservation_fee = get_post_meta($listing_id, '_bdb_reservation_fee', true);
            $order_details[] = array(
                'active' => '1',
                'label' => 'Booking',
                'price' => ! empty( $_POST['price'] ) ? $_POST['price'] : '',
                'show_ribbon' => '1',
            );
            return $order_details;
        }
        return $order_details;
    }

    public function atbdp_order_items($order_items = null, $order_id = null, $listing_id = null, $data = null) {
        global $wpdb;
        $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `order_id`=' . esc_sql($order_id), 'ARRAY_A');
        if(!empty($booking_data) && ($order_id == $booking_data['order_id'])) {
            $reservation_fee = $booking_data['price'];
            $p_title = get_the_title($listing_id[0]);
            $order_items[] = array(
                'title' => $p_title,
                'desc' => "description",
                'price' => $booking_data['price'],
            );
            return $order_items;
        }
        return $order_items;

    }

    public function atbdp_payment_receipt_data($receipt_data, $order_id, $listing_id) {
        global $wpdb;
        $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `order_id`=' . esc_sql($order_id), 'ARRAY_A');
        if(!empty($booking_data) && ($order_id == $booking_data['order_id'])) {
            $status     = get_post_meta($order_id,'_payment_status',true);
            $booking_id = get_post_meta($order_id,'_booking_id',true);
            if('completed' == $status) {
                //BD_Booking()->bdb_booking_database->set_booking_status($booking_id, 'paid');
                if( $booking_id ) {
                    bdb_commission_system( $order_id );
                }
            }
            $p_title = get_the_title($listing_id[0]);
            $reservation_fee = get_post_meta($listing_id[0], '_bdb_reservation_fee', true);
            $receipt_data = array(
                'title' => $p_title,
                'desc' => '',
                'price' => $booking_data['price'],
            );
            return $receipt_data;
        } else {
            return $receipt_data;
        }
    }

    public function atbdp_cc_form() {
        $confirmed = isset($_POST['confirmed']) ? $_POST['confirmed'] : '';
        $booking_id = !empty($this->booking_id) ? $this->booking_id : '';
        if(!empty($confirmed)) {
            printf("<input type='hidden' name='confirmed' value='%s'>", $confirmed);
            printf("<input type='hidden' name='booking_id' value='%s'>", $booking_id);
        }
    }

    public function atbdp_order_created ($order_id, $listing_id) {
        if(isset($_POST['confirmed']) && 'done' == $_POST['confirmed']) {
            $instant_booking = get_post_meta( $listing_id, '_bdb_instant_booking', true );
            $booking_id = isset( $_POST['booking_id'] ) ? $_POST['booking_id'] : '';
            update_post_meta( $order_id,'_booking_id',$booking_id );
            if( !empty( $instant_booking ) ) {
                $status = 'confirmed';
            } else {
                $status = 'waiting';
            }
            BD_Booking()->bdb_booking_database->set_booking_status( $booking_id, $status, $order_id , 'yes', 'no' );
            add_filter( 'atbdp_reviewed_listing_status_controller_argument', array( $this, 'atbdp_reviewed_listing_status_controller_argument' ) );
        }
    }

    public function atbdp_reviewed_listing_status_controller_argument( $args ) {
        $post_status         = get_post_status( $args['ID'] );
        $args['post_status'] = $post_status;
        return $args;
    }

    public function atpp_reviewed_listing_status_controller_argument( $status ) {
       $status = 'publish';
       return $status;
    }

    public function atbdp_order_status_changed($new_status, $old_status, $post_id) {
        if( 'completed' == $new_status ) {
            $booking_id = get_post_meta( $post_id,'_booking_id',true );
            BD_Booking()->bdb_booking_database->set_booking_status( $booking_id, 'paid' );
            if( $booking_id ) {
                bdb_commission_system( $post_id );
                add_filter( 'atpp_reviewed_listing_status_controller_argument', array( $this, 'atpp_reviewed_listing_status_controller_argument' ) );
            }
        }
    }

    /**
     * Change the button text of payment receipt
     *
     *
     */
    public function atbdp_payment_receipt_button_text( $text, $order_id ) {
        global $wpdb;
        $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `order_id`=' . esc_sql($order_id), 'ARRAY_A');
        if(!empty($booking_data) && ($order_id == $booking_data['order_id'])) {
            $text = __( 'Go to My Bookings', 'directorist-booking' );
            return $text;
        } else {
            return $text;
        }
    }

    /**
     * Change the button text of payment receipt
     *
     * @return void
     */
    public function atbdp_payment_receipt_button_link($button_link, $order_id) {
        global $wpdb;
        $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `order_id`=' . esc_sql($order_id), 'ARRAY_A');
        if(!empty($booking_data) && ($order_id == $booking_data['order_id'])) {
            $button_link = ATBDP_Permalink::get_dashboard_page_link() . '/#active_my_booking';
            return $button_link;
        } else {
            return $button_link;
        }
    }

    /**
     * Customize the dependency of monetization for accessing to checkout page
     *
     *
    */
    public function atbdp_enable_monetization_checkout($enable_monetization) {
        if(isset($_POST['confirmed'])) {
            $enable_monetization = 1;
            return $enable_monetization;
        }else {
            return $enable_monetization;
        }
    }
    /**
     * Customize the dependency of monetization for accessing to checkout page
     *
     * @return void
    */
    public function atbdp_featured_active_checkout($enable_featured) {
        if(isset($_POST['confirmed'])) {
            $enable_featured = 1;
            return $enable_featured;
        }else {
            return $enable_featured;
        }
    }
}