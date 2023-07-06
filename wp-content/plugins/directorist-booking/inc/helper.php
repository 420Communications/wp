<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
if (!function_exists('bdb_date_time_wp_format')) {
    function bdb_date_time_wp_format()
    {
        /**
         * Add date format into javascript
         */
        $dateFormat = get_option('date_format');
        $timeFormat = get_option('time_format');
        $rawFormat = $dateFormat;
        $dateFormat = explode('-', $dateFormat);

        preg_match_all('/[a-zA-Z]+/', $rawFormat, $output);

        foreach ($output[0] as $dataType) {

            switch (strtolower($dataType)) {
                case 'j' :
                    $convertedType[] = 'DD';
                    break;
                case 'y' :
                    $convertedType[] = 'YYYY';
                    break;
                case 'd' :
                    $convertedType[] = 'DD';
                    break;
                case 'm' :
                    $convertedType[] = 'MM';
                    break;
                case 'f' :
                    $convertedType[] = 'MM';
                    break;
            }

        }

        $convertedData['date'] = $convertedType[0] . '/' . $convertedType[1] . '/' . $convertedType[2];
        $convertedData['day'] = intval(get_option('start_of_week'));
        $convertedData['raw'] = $rawFormat;
        $convertedData['time'] = $timeFormat;
        return $convertedData;
    }
}

if (!function_exists('bdb_day_fields')) {
    function bdb_day_fields($day = 'monday', $number = 0)
    { ?>
        <div class="bdb-select-hours bdb_<?php echo $day; ?>_section" id="<?php echo $day; ?>ID-0">
            <div class="bdb-select-from bdb-custom-select">
                <label for="bdb-<?php echo $day; ?>-from"><?php _e("Time From", "directorist-booking"); ?></label>
                <input type="time" name="bdb[<?php echo $number; ?>][0][start]"
                       class="bdb-time-input bdb-<?php echo $day; ?>-start" id="bdb-<?php echo $day; ?>-from"
                       value="">
            </div>
            <div class="bdb-select-from bdb-custom-select">
                <label for="bdb-<?php echo $day; ?>-to"><?php _e("Time To", "directorist-booking"); ?></label>
                <input type="time" name="bdb[<?php echo $number; ?>][0][close]"
                       class="bdb-time-input bdb-<?php echo $day; ?>-close" id="bdb-<?php echo $day; ?>-to"
                       value="">
            </div>
            <div class="bdb-select-from bdb-custom-select">
                <label for="bdb-<?php echo $day; ?>-slots"><?php _e("Slots", "directorist-booking"); ?></label>
                <input type="number" name="bdb[<?php echo $number; ?>][0][slots]"
                       class="bdb-time-input bdb-<?php echo $day; ?>-slots" id="bdb-<?php echo $day; ?>-slots"
                       value="">
            </div>
            <!--<button class="bdb-remove" type="button">&times;</button>-->
        </div>
        <?php
    }
}
if(!function_exists('bdb_create_required_pages')) {
    function bdb_create_required_pages()
    {
        $options = get_option('atbdp_option');
        $page_exists = get_option('atbdp_booking_confirmation');
        // $op_name is the page option name in the database.
        // if we do not have the page id assigned in the settings with the given page option name, then create an page
        // and update the option.
        $id = array();
        if (!$page_exists) {
            $id = wp_insert_post(
                array(
                    'post_title' => 'Booking Confirmation',
                    'post_content' => '[directorist_booking_confirmation]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'comment_status' => 'closed'
                )
            );
        }
        // if we have new options then lets update the options with new option values.
        if ($id) {
            update_option('atbdp_booking_confirmation', 1);
            $options['booking_confirmation'] = (int)$id;
            update_option('atbdp_option', $options);

        };
    }
}

if(!function_exists('bdb_booking_confirmation_page')) {
    function bdb_booking_confirmation_page()
    {

        $link = home_url();
        $id = get_directorist_option('booking_confirmation'); // get the page id of the search page.
        if( $id ) $link = get_permalink( $id );



        return apply_filters('bdb_booking_confirmation_page_url', $link );
    }
}

if(!function_exists('bdb_count_my_bookings')) {
    function bdb_count_my_bookings($user_id){
        global $wpdb;
        $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation')", "ARRAY_A" );

        return $wpdb->num_rows;
    }
}

if(!function_exists('bdb_count_bookings')) {
    function bdb_count_bookings($user_id,$status = ''){
        global $wpdb;
        if(empty($status)) {
            $status_sql = "";
        } elseif ( $status == 'approved' ) {
            $status_sql = "AND status IN ('confirmed','paid')";
        } else {
            $status_sql = "AND status='$status'";
        }

        $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE owner_id=$user_id $status_sql", "ARRAY_A" );
        return $wpdb->num_rows;
    }
}

function bdb_count_my_bookings($user_id){
    global $wpdb;
    $result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "directorist_booking` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation')", "ARRAY_A" );

    return $wpdb->num_rows;
}

function bdb_ajax_pagination($pages = '', $current = false, $range = 2 ) {
    if(!empty($current)){
        $paged = $current;
    } else {
        global $paged;
    }

    $output = false;
    if(empty($paged)) $paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){


        $output .= '<nav id="db-booking-pagination" class="db-booking-pagination"><ul class="db-pagination">';
        $output .=  ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li class="db-item-number db-item-number-next"  data-paged="next"><a href="#"><i class="sl sl-icon-arrow-left"></i></a></li>' : '';
        //$output .=  ( $paged > 1 ) ? '<li><a class="previouspostslink" href="#"">'.__('Previous','directorist-booking').'</a></li>' : '';
        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
            {
                if ( $paged == $i ){
                    $output .=  '<li class="current db-item-number" data-paged="'.$i.'"><a href="#">'.$i.' </a></li>';
                } else {
                    $output .=  '<li class="db-item-number" data-paged="'.$i.'"><a href="#">'.$i.'</a></li>';
                }
            }
        }
        // $output .=  ( $paged < $pages ) ? '<li><a class="nextpostslink" href="#">'.__('Next','directorist-booking').'</a></li>' : '';
        $output .=  ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li class="db-item-number  db-item-number-prev"  data-paged="prev"><a  href="#"><i class="sl sl-icon-arrow-right"></i></a></li>' : '';
        $output .=  '</ul></nav>';


    }
    return $output;
}

function bdb_commission_system( $post_id ) {
    $listing_id             = get_post_meta( $post_id, '_listing_id', true);
    $author_id              = get_post_field( 'post_author', $listing_id );
    $payment_method         = get_user_meta( $author_id, 'bdb_payment_method', true );
    $payment_method         = ! empty( $payment_method ) ? $payment_method : 'No Payment Method';
    $paypal_email           = get_user_meta( $author_id, 'bdb_paypal_email', true);
    $bank_details           = get_user_meta( $author_id, 'bdb_bank_details', true);
    $payment_details        = __( 'No details', 'directorist-booking' );
    if( 'paypal' == $payment_method ) {
        $payment_details   = ! empty( $paypal_email ) ? esc_attr( $paypal_email )  : '';
    } elseif( 'bank_transfer' == $payment_method ) {
        $payment_details   = ! empty( $bank_details ) ? esc_attr( $bank_details )  : '';
    }
    $author_name     = get_the_author_meta( 'user_nicename', $author_id );
    $date_format     = get_option( 'date_format' );
    $t               = get_the_time( 'U' );
    $amount          = get_post_meta( $post_id, '_amount', true );
    $amount          = is_numeric( $amount ) ? $amount : 0;
    $commission_rate = get_directorist_option( 'bdb_commission_rate', 10 );
    $site_fee        = ( $commission_rate / 100 ) * $amount;
    $balance_pay     = $amount - $site_fee;
    $commissions     = new WP_Query( array(
        'post_type' => 'bdb_commission'
    ) );
    $update_commission   = false;
    while( $commissions->have_posts() ) {
        $commissions->the_post();
        $username               = get_post_meta( get_the_ID(), '_username', true );
        if( $author_name == $username ) {
            $update_commission = true;
        }
        $old_order_id           = get_post_meta( get_the_ID(), '_order_id', true );
        $_old_order_id          = unserialize( base64_decode( $old_order_id     ) );
        if( $author_name == $username && ! in_array( $post_id, $_old_order_id )) {

            $old_listing_name               = get_post_meta( get_the_ID(), '_listing_name', true );
            $old_publish_date               = get_post_meta( get_the_ID(), '_publish_date', true );
            $old_order_id                   = get_post_meta( get_the_ID(), '_order_id', true );
            $old_amount                     = get_post_meta( get_the_ID(), '_amount', true );
            $old_site_fee                   = get_post_meta( get_the_ID(), '_site_fee', true );
            $old_balance_pay                = get_post_meta( get_the_ID(), '_balance_pay', true );
            $old_total_balance_pay          = get_post_meta( get_the_ID(), '_total_balance_pay', true );
            $old_total                      = get_post_meta( get_the_ID(), '_total', true );
            $old_total                      = is_numeric( $old_total ) ? $old_total : 0;
            $old_order_count                = get_post_meta( get_the_ID(), '_order_count', true );

            $_old_listing_name              = unserialize( base64_decode( $old_listing_name ) );
            $_old_publish_date              = unserialize( base64_decode( $old_publish_date ) );
            $_old_order_id                  = unserialize( base64_decode( $old_order_id     ) );
            $_old_amount                    = unserialize( base64_decode( $old_amount ) );
            $_old_site_fee                  = unserialize( base64_decode( $old_site_fee ) );
            $_old_balance_pay               = unserialize( base64_decode( $old_balance_pay ) );

            $listing_id                     = get_post_meta( $post_id, '_listing_id', true );
            $new_listing_name[]             = get_the_title( $listing_id );
            $new_publish_date[]             = date_i18n( $date_format, $t );
            $new_order_id[]                 = $post_id;
            $new_amount[]                   = $amount;
            $new_site_fee[]                 = $site_fee;
            $new_balance_pay[]              = $balance_pay;

            $update_listing_name            = array_merge( $_old_listing_name, $new_listing_name );
            $update_publish_date            = array_merge( $_old_publish_date, $new_publish_date );
            $update_order_id                = array_merge( $_old_order_id    , $new_order_id     );
            $update_amount                  = array_merge( $_old_amount, $new_amount);
            $update_site_fee                = array_merge( $_old_site_fee , $new_site_fee );
            $update_balance_pay             = array_merge( $_old_balance_pay , $new_balance_pay );

            $_update_listing_name           = base64_encode( serialize( $update_listing_name ) );
            $_update_publish_date           = base64_encode( serialize( $update_publish_date ) );
            $_update_order_id               = base64_encode( serialize( $update_order_id     ) );
            $_update_amount                 = base64_encode( serialize( $update_amount ) );
            $_update_site_fee               = base64_encode( serialize( $update_site_fee ) );
            $_update_balance_pay            = base64_encode( serialize( $update_balance_pay ) );
            $_update_total                  = $old_total + $amount;
            $_update_total_balance_pay      = $old_total_balance_pay + $balance_pay;
            $_update_order_count            = $old_order_count + 1;
            update_post_meta( get_the_ID(), '_listing_name', $_update_listing_name );
            update_post_meta( get_the_ID(), '_publish_date', $_update_publish_date );
            update_post_meta( get_the_ID(), '_order_id', $_update_order_id );
            update_post_meta( get_the_ID(), '_amount', $_update_amount );
            update_post_meta( get_the_ID(), '_site_fee', $_update_site_fee );
            update_post_meta( get_the_ID(), '_balance_pay', $_update_balance_pay );
            update_post_meta( get_the_ID(), '_total_balance_pay', $_update_total_balance_pay );
            update_post_meta( get_the_ID(), '_total', $_update_total );
            update_post_meta( get_the_ID(), '_order_count', $_update_order_count );
            update_post_meta( get_the_ID(), '_payment_method', $payment_method );
            update_post_meta( get_the_ID(), '_payment_details', $payment_details );
        }

    }
    wp_reset_query();
    $_listing_name   = [];
    $_order_id       = [];
    $_publish_date   = [];
    $_amount         = [];
    $_site_fee       = [];
    $_balance_pay    = [];
    if( $update_commission ) {

    } else {
        $commission_id  = wp_insert_post( array(
            'post_content'      => '',
            'post_title'        => $author_name,
            'post_status'       => 'publish',
            'post_type'         => 'bdb_commission',
            'comment_status'    => false,
        ) );
        $commissions = new WP_Query( array(
        'post_type' => 'bdb_commission'
    ) );
    $listing_id             = get_post_meta( $post_id, '_listing_id', true );
    $_listing_name[]        = get_the_title( $listing_id );
    $listing_name           = base64_encode( serialize( $_listing_name ) );
    $_publish_date[]        = date_i18n( $date_format, $t );
    $publish_date           = base64_encode( serialize( $_publish_date ) );
    $_order_id[]            = $post_id;
    $order_id               = base64_encode( serialize( $_order_id ) );
    $_amount[]              = $amount;
    $amount                 = base64_encode( serialize( $_amount ) );
    $_site_fee[]            =  $site_fee;
    $site_fee               = base64_encode( serialize( $_site_fee ) );
    $_balance_pay[]         = $balance_pay;
    $total_balance_pay      = $balance_pay;
    $balance_pay            = base64_encode( serialize( $_balance_pay ) );

    update_post_meta( $commission_id, '_username', $author_name );
    update_post_meta( $commission_id, '_listing_name', $listing_name );
    update_post_meta( $commission_id, '_publish_date', $publish_date );
    update_post_meta( $commission_id, '_order_id', $order_id );
    update_post_meta( $commission_id, '_amount', $amount );
    update_post_meta( $commission_id, '_site_fee', $site_fee );
    update_post_meta( $commission_id, '_balance_pay', $balance_pay );
    update_post_meta( $commission_id, '_total_balance_pay', $total_balance_pay );
    update_post_meta( $commission_id, '_total', $amount );
    update_post_meta( $commission_id, '_order_count', 1 );
    update_post_meta( $commission_id, '_payment_method', $payment_method );
    update_post_meta( $commission_id, '_payment_details', $payment_details );
    }

}

function wallet_array_value( $payout_meta_names ) {

    $meta_value = [];
    foreach( $payout_meta_names as $values ) {
        // $meta_value[] = $values;
        if( is_array( $values )  ) {
            foreach( $values as $value ) {
                $meta_value[] = $value;
            }
        } else {
            $meta_value[] = $values;
        }
    }
    return $meta_value;
}


if ( ! function_exists( 'atbdp_check_booking_restriction' ) ) {
    function atbdp_check_booking_restriction( $post_id = '' ) {
        // Check Restriction
        $restricted = apply_filters( 'atbdp_booking_is_restricted', false, $post_id );

        return $restricted;
    }
}

if ( ! function_exists( 'searchForId' ) ) {
    function searchForId( $date_start ) {
        global $wpdb;
        $post = get_posts( 
                array( 
                    'post_type'     => ATBDP_POST_TYPE, 
                    'numberposts'   => 1,
                    'author'        => get_current_user_id(),
                    'meta_key'      => '_bdb_booking_type',
                    'meta_value'    => 'rent'
                )
            );
        $post_id = ! empty( $post ) ? $post[0]->ID : 0;   
        $listing_id = ! empty( $_POST['listingId'] ) ? $_POST['listingId'] : $post_id;
        $booking_data = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `listing_id`=' . esc_sql($listing_id), 'ARRAY_A');
        if( ! empty( $booking_data ) ) {
            foreach ($booking_data as $val) {
                $_date_start = explode( " ", $val['date_start'] );
                $_date_end   = explode( " ", $val['date_end'] );
                $start_time  = $_date_start[0];
                $end_time    = $_date_end[0];
                if ( $_date_start[0] == $date_start && $val['status'] == 'owner_reservations' ) {
                    return array(
                        'class_name' => 'day__disable',
                    );
                } elseif( $_date_start[0] == $date_start && $val['status'] != 'owner_reservations' && $val['type'] != 'special_price' ) {
                    $display_name = get_the_author_meta( 'display_name', $val['bookings_author'] );
                    $u_pro_pic_id = get_user_meta( $val['bookings_author'], 'pro_pic', true );
                    $u_pro_pic    = $u_pro_pic_id ? wp_get_attachment_image_src( $u_pro_pic_id, 'directory-large' ) : '';

                    return array(
                        'class_name' => 'day__booked',
                        'name'       => $display_name,
                        'img_src'    => ! empty( $u_pro_pic[0] ) ? $u_pro_pic[0] : 'https://via.placeholder.com/40x40'
                    );
                } elseif( $date_start >= $start_time && $date_start <= $end_time && $val['status'] != 'owner_reservations' ) {
                    return array(
                        'class_name' => 'day__booked',
                    );
                }
            }
        }
        return false;
     }
}

if ( ! function_exists( 'dashboardPrice' ) ) {
    function dashboardPrice() {
        $post = get_posts( 
                array( 
                    'post_type'     => ATBDP_POST_TYPE, 
                    'numberposts'   => 1,
                    'author'        => get_current_user_id(),
                    'meta_key'      => '_bdb_booking_type',
                    'meta_value'    => 'rent'
                )
            );
        $post_id = ! empty( $post ) ? $post[0]->ID : '';    
        $listing_id = ! empty( $_POST['listingId'] ) ? $_POST['listingId'] : $post_id;
        
        $normal_price            = (int) get_post_meta( $listing_id, '_price', true );
        $weekend_price           = (int) get_post_meta( $listing_id, '_bdb_weekend_price', true );
        $calender_price          = get_post_meta( $listing_id, '_bdb_calender_price', true );

        return array(
            'normal_price'   => ! empty( $normal_price ) ? $normal_price : '',
            'weekend_price'  => ! empty( $weekend_price ) ? $weekend_price : '',
            'calender_price' => ! empty( $calender_price ) ? $calender_price : ''
        );
     }
}

// TO check input type required
if ( ! function_exists( 'required' ) ) {
    function required( $value = '' ) {
        echo empty( $value ) ? '' : esc_html( 'required="true"' );
    }
}

// Modified search query to show booking-rent type listing.
add_filter('atbdp_search_listings_meta_queries', 'booking_listing_search_query_argument');
add_filter('atbdp_listing_search_query_argument', 'booking_listing_search_query_argument');

function booking_listing_search_query_argument( $args ) {

    global $wpdb;
    $checkin_query_args  = isset( $_REQUEST['custom_field']['directorist-booking-check-in'] ) ? $_REQUEST['custom_field']['directorist-booking-check-in'] : '';
    $checkout_query_args = isset( $_REQUEST['custom_field']['directorist-booking-check-out'] ) ? $_REQUEST['custom_field']['directorist-booking-check-out'] : '';
    $guest_query_args    = isset( $_REQUEST['custom_field']['guest'] ) ? $_REQUEST['custom_field']['guest'] : '';
    
    $booked_ids = $has_guest_listing_id = $rent_ids = [];

    foreach ( $args as $key => $value ) {
        if ( isset( $args[$key]['key'] ) ) {
            if (  '_directorist-booking-check-in' == $args[$key]['key'] || '_directorist-booking-check-out' == $args[$key]['key'] || '_guest' == $args[$key]['key'] ) {      
                unset( $args[$key] );
            }
        }
    }

    // Get booked ids - checkin 
    if ( $checkin_query_args || $checkout_query_args ) {

        $check_in   = date_format( date_create( $checkin_query_args ), 'Y-m-d 00:00:00' );
        $checkout   = date_format( date_create( $checkout_query_args ), 'Y-m-d 23:59:59' );
        
        // Get booked id of specific date.
        $sql = sprintf( "SELECT * FROM {$wpdb->prefix}directorist_booking WHERE date_start BETWEEN '%s' AND '%s'", $check_in, $checkout ); 

        $booked_listings = $wpdb->get_results( $sql );

        if ( is_array( $booked_listings ) ) {

            foreach ( $booked_listings as $booked_listing ) {

                $id           = $booked_listing->listing_id;
                $booking_type = get_post_meta( $id, '_bdb_booking_type', true );

                if ( 'rent' === $booking_type ) {
                    $booked_ids[] = $id;
                }
            }

            $booked_ids = array_unique( $booked_ids );  //check booked unique ids.
        }
    }

    // Get ids where booking type is rent
    $listings = get_atbdp_listings_ids();
    foreach ( $listings->posts as $id ) {
        $booking_type = get_post_meta( $id, '_bdb_booking_type', true );
        if ( 'rent' === $booking_type ) {
            $rent_ids[] = $id;
        }
    }

    // Exclude booked ids.
    $ids_array  = ! empty( $booked_ids ) ? array_diff( $rent_ids, array_unique( $booked_ids ) ) : $rent_ids;

    // Get listing ids where guest amount match
    if ( $guest_query_args ) {
        foreach ( $ids_array as $id ) {
            $guest_number = get_post_meta( $id, '_bdb_reservation_guest', true );

            if ( $guest_query_args == $guest_number ) {
                $has_guest_listing_id[] = $id;
            }
        }
       // Merged checkin & check out ids
        $ids_array = $has_guest_listing_id;
    }
   
    // Update Query Args
    if ( $checkin_query_args || $checkout_query_args || $guest_query_args ) {
        $args['post__in'] = ! empty( $ids_array ) ? $ids_array : [0];
    }

    return $args;
}