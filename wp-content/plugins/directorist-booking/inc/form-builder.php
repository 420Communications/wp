<?php 
if ( ! class_exists( 'BD_Form_builder' ) ) {
    class BDB_Form_builder
    {
        public function __construct()
        {
            add_filter( 'atbdp_form_preset_widgets', array( $this, 'atbdp_form_preset_widgets' ) );
            add_filter( 'directorist_field_template', array( $this, 'directorist_field_template' ), 10, 2 );
            add_filter( 'atbdp_single_listing_content_widgets', array( $this, 'atbdp_single_listing_content_widgets' ) );
            add_filter( 'directorist_single_item_template', array( $this, 'directorist_single_item_template' ), 10, 2 );
            add_filter('atbdp_ultimate_listing_meta_user_submission', array($this, 'listing_meta_user_submission'), 10, 2);
            add_filter( 'directorist_plan_allowed_field', array( $this, 'directorist_plan_allowed_field' ), 10, 3 );

            /*Search Form Layout*/
            add_filter( 'directorist_search_form_widgets', array( $this, 'added_search_form_widgets' ) );
            add_filter( 'directorist_search_field_template', array( $this, 'search_field_templates' ), 10, 2 );
        }

        public function search_field_templates( $template, $field_data ) {
            if ( 'guestNumber' === $field_data['widget_name'] ) {
                $template = BD_Booking()->load_template( 'number-of-guest', [ 'field_data' => $field_data ] );
            }
            if ( 'checkInOut' === $field_data['widget_name'] ) {
                $template = BD_Booking()->load_template( 'check-in-out', [ 'field_data' => $field_data ] );
                wp_enqueue_script('bdb-daterangepicker');
                wp_enqueue_script('bdb-main-js');
                wp_enqueue_style('bdb-daterangepicker-style');
            }

            return $template;
        }

        public function directorist_plan_allowed_field( $state, $field_data, $plan_id ){ 

            $field_key = ( isset( $field_data['field_key'] ) ) ? $field_data['field_key'] : '';

            if( 'bdb' === $field_key ){
                $booking = get_post_meta( $plan_id, '_fm_booking', true );
                if( $booking ){ $state = true; }
            }
            
            return $state;
        }

        /**
         * Save meta for frontend
         */
        public function listing_meta_user_submission( $metas, $info ) {

            $metas['_bdb']                                = !empty($info['bdb']) ? atbdp_sanitize_array($info['bdb']) : array(); // we are expecting array value
            $metas['_bdb_hide_booking']                   = !empty($info['bdb_hide_booking'])? sanitize_text_field($info['bdb_hide_booking']) : '';
            $metas['_bdb_payment_booking']                = !empty($info['bdb_payment_booking'])? sanitize_text_field($info['bdb_payment_booking']) : '';
            $metas['_bdb_instant_booking']                = !empty($info['bdb_instant_booking'])? sanitize_text_field($info['bdb_instant_booking']) : '';
            $metas['_bdb_reservation_fee']                = !empty($info['bdb_reservation_fee'])? (int) $info['bdb_reservation_fee'] : '';
            $metas['_bdb_reservation_guest']              = !empty($info['bdb_reservation_guest'])? (int) $info['bdb_reservation_guest'] : '';
            $metas['_bdb_slot_status']                    = !empty($info['bdb_slot_status'])? sanitize_text_field($info['bdb_slot_status']) : '';
            $metas['_bdb_display_slot_available_text']    = !empty($info['bdb_display_slot_available_text'])? sanitize_text_field($info['bdb_display_slot_available_text']) : '';
            $metas['_bdb_display_available_time']         = !empty($info['bdb_display_available_time'])? sanitize_text_field($info['bdb_display_available_time']) : '';
            $metas['_bdb_slot_available_text']            = !empty($info['bdb_slot_available_text'])? sanitize_text_field($info['bdb_slot_available_text']) : '';
            $metas['_bdb_available_time_text']            = !empty($info['bdb_available_time_text'])? sanitize_text_field($info['bdb_available_time_text']) : '';
            $metas['_bdb_booking_type']                   = !empty($info['bdb_booking_type'])? $info['bdb_booking_type'] : '';
            $metas['_bdb_event_ticket']                   = !empty($info['bdb_event_ticket'])? $info['bdb_event_ticket'] : '';
            $metas['_bdb_display_available_ticket']       = !empty($info['bdb_display_available_ticket'])? $info['bdb_display_available_ticket'] : '';
            $metas['_bdb_available_ticket_text']          = !empty($info['bdb_available_ticket_text'])? $info['bdb_available_ticket_text'] : '';
            $metas['_bdb_maximum_ticket_allowed']         = !empty($info['bdb_maximum_ticket_allowed'])? $info['bdb_maximum_ticket_allowed'] : '';
            return $metas;

        }
        public function directorist_single_item_template( $template, $field_data ) {

            if( 'booking' === $field_data['widget_name'] ) {
            // Stop if pricing plan dosen't allows booking
            // Check Restriction
            $restricted = atbdp_check_booking_restriction( get_the_ID() );
            if ( $restricted ) { return; }

            $hide_booking                   = get_post_meta( get_the_ID(), '_bdb_hide_booking', true);
            if( ! empty( $hide_booking ) ) return;
         //   ob_start();
            ?>
            <div class="booking-wrapper atbd_content_module">
                    <div class="atbd_content_module__tittle_area">
                        <div class="atbd_area_title">
                            <h4>
                                <span class="fa fa-picture-o atbd_area_icon"></span>
                                <?php esc_attr_e( 'Booking', 'directoirst-booking' ); ?>
                            </h4>
                        </div>
                    </div>
                    <div class="booking-content">
                        <div class="booking-grid-two bdb_widget">
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
                            $days_list = array(
                                0 => __('Monday', 'directorist-booking'),
                                1 => __('Tuesday', 'directorist-booking'),
                                2 => __('Wednesday', 'directorist-booking'),
                                3 => __('Thursday', 'directorist-booking'),
                                4 => __('Friday', 'directorist-booking'),
                                5 => __('Saturday', 'directorist-booking'),
                                6 => __('Sunday', 'directorist-booking'),
                            );

                            include BDB_DIR . '/templates/booking.php'; ?>
                        </div>
                    </div><!-- ends: .booking-content -->
                </div><!-- ends: .booking-wrapper -->
        <?php
           // $template =  ob_get_clean();
            }

            return $template;
        }

        public function atbdp_single_listing_content_widgets($widgets)
        {
            $widgets['booking'] = [
                'options' => [
                    'icon' => [
                        'type'  => 'icon',
                        'label' => 'Icon',
                        'value' => 'la la-address-card',
                    ],
                ]
            ];
            return $widgets;
        }

        public function directorist_field_template( $template, $field_data ) {
           
            if( 'booking' === $field_data['widget_name'] ) {
                $booking = get_directorist_option( 'enable_booking', 1 );
                if( ! empty( $booking ) ) {
                    
                    global $pagenow, $post;
                    $listing_id = ! empty( $field_data['form'] ) ? $field_data['form']->get_add_listing_id() : '';
                    $id = get_the_ID();
                    $plan_allows_booking = true;
                    $booking_is_enabled  = get_directorist_option( 'enable_booking', 1 );
                    if ( is_fee_manager_active() && ! is_admin() ) {
                        $plan_id             = get_post_meta( $listing_id, '_fm_plans', true );
                        $plan_allows_booking = atbdp_plan_allows_booking( $plan_id  );
                    }

                    if (  empty( $booking_is_enabled ) || empty( $plan_allows_booking ) ) { return; }

                    $booking_type = get_directorist_option('booking_type', 'all');
                    $booking_type_default_value = get_directorist_option('booking_type_default_value', 'service');
                    $booking_type_default_value = !empty( $booking_type_default_value ) ? $booking_type_default_value : 'service';
                   
                    $slot_available_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                    $available_ticket_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                    $available_time_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                    ?>
                    <div class="atbd_content_module atbd-booking-information">
        
                        <div class="atbdb_content_module_contents">
        
                            <?php
                            include BDB_TEMPLATES_DIR . 'booking-fields.php';
                            ?>
        
                        </div>
                    </div>
                    <?php
                }
            }
            return $template;
        }

        // Search form (check in, check out, guest) widgets
        public function added_search_form_widgets ( $widgets ) {

            $guestNumber = [
                'label' => __( 'Booking Guests', 'directorist-booking' ),
                'icon'  => 'uil uil-users-alt',
                'options' => [
                    'label' => [
                        'type'  => 'text',
                        'label' => __( 'Label', 'directorist-booking' ),
                        'value' => __( 'Guests', 'directorist-booking' ),
                    ],
                    'placeholder' => [
                        'type'  => 'text',
                        'label' => __( 'Placeholder', 'directorist-booking' ),
                        'value' => 'Guest Number',
                    ],
                    'required' => [
                        'type'  => 'toggle',
                        'label'  => __( 'Required', 'directorist-booking' ),
                        'value' => false,
                    ],
                ]
            ];

            $checkInOut = [
                'label' => __( 'Check-in - Check-out', 'directorist-booking' ),
                'icon'  => 'uil-check-square',
                'options' => [
                    'label' => [
                        'type'  => 'text',
                        'label' => __( 'Label - Checkin', 'directorist-booking' ),
                        'value' => 'Check In',
                    ],
                    'label_2' => [
                        'type'  => 'text',
                        'label' => __( 'Label - Checkout', 'directorist-booking' ),
                        'value' => 'Check Out',
                    ],
                    'required' => [
                        'type'  => 'toggle',
                        'label' => __( 'Required', 'directorist-booking' ),
                        'value' => false,
                    ],
                ]
            ];

            $widgets_name = array(
                'checkInOut'  => $checkInOut,
                'guestNumber' => $guestNumber,
            );

            // Register widgets.
            foreach ( $widgets_name as $key => $value ) {
                $widgets['other_widgets']['widgets'][$key] = $value;
            }
    
            return $widgets;
        }

        public function atbdp_form_preset_widgets( $widgets ) {

            $widgets['booking'] = array(
                'label' => 'Booking',
                'icon'  => 'fa fa-address-card',
                'options' => array( 
                    'type' => [
                        'type'  => 'hidden',
                        'value' => 'booking',
                    ], 
                    'field_key' => [
                        'type'   => 'meta-key',
                        'hidden' => true,
                        'value'  => 'bdb',
                    ],
                    /* 'field_key' => [
                        'type'   => 'meta-key',
                        'hidden' => true,
                        'value'  => 'bdb_event_ticket',
                    ], */
                    'label' => [
                        'type'  => 'text',
                        'label' => 'Label',
                        'value' => 'Booking',
                    ],
                )
            );
            return $widgets;
        }
    }
}
