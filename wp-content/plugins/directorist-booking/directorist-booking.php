<?php
/**
 * Plugin Name: Directorist - Booking
 * Plugin URI: http://directorist.com/plugins/directorist-booking
 * Description: This is an extension for Directorist Plugin.
 * Version: 1.6.1
 * Author: wpWax
 * Author URI: http://directorist.com
 * License: GPLv2 or later
 * Text Domain: directorist-booking
 * Domain Path: /languages
 */

// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');
if (!class_exists('BD_Booking')){
    final class BD_Booking
    {
        /** Singleton *************************************************************/

        /**
         * @var BD_Booking The one true BD_Booking
         * @since 1.0
         */
        private static $instance;
        /**
         * Directorist_Booking_Database Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_booking_database;
        /**
         * BDB_More_Field Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_more_fields;
        /**
         * Directorist_Booking_Payment Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_booking_payment;
        /**
         * Directorist_Booking_Dashboard Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_booking_dashboard;
        /**
         * Directorist_Booking_Settings Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_booking_settings;
        /**
         * BDB_Commission Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $BDB_Commission;
        /**
         * Directorist_Booking_Wallet Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_booking_wallet;
         /**
         * BDB_Form_builder Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_form_builder;
        /**
         * Directorist_Rent_Calendar Object.
         *
         * @var object|BD_Booking
         * @since 1.0
         */
        public $bdb_rent_calendar;
        /**
         * Main BD_Booking Instance.
         *
         * Insures that only one instance of BD_Booking exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0
         * @static
         * @static_var array $instance
         * @uses BD_Booking::setup_constants() Setup the constants needed.
         * @uses BD_Booking::includes() Include the required files.
         * @uses BD_Booking::load_textdomain() load the language files.
         * @see  BD_Booking()
         * @return object|BD_Booking The one true BD_Booking
         */
        public static function instance()
        {
            if ( !isset(self::$instance ) && !( self::$instance instanceof BD_Booking ) ) {
                self::$instance = new BD_Booking;
                self::$instance->setup_constants();
                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                add_filter( 'atbdp_listing_meta_admin_submission', array( self::$instance, 'atbdp_bdb_admin_submission' ) );
                add_filter( 'atbdp_listing_meta_user_submission', array( self::$instance, 'atbdp_listing_meta_user_submission') );
                add_action( 'wp_enqueue_scripts', array( self::$instance, 'load_needed_scripts' ) );
                add_action( 'admin_enqueue_scripts', array( self::$instance, 'load_needed_scripts_for_admin') );
                if ( get_option('atbdp_booking_confirmation') < 1 ) {
                    add_action( 'wp_loaded', array( self::$instance, 'add_custom_page' ) );
                }
                self::$instance->includes();
                self::$instance->bdb_booking_database = new Directorist_Booking_Database;
                self::$instance->bdb_booking_payment = new Directorist_Booking_Payment;
                self::$instance->bdb_booking_dashboard = new Directorist_Booking_Dashboard();
                self::$instance->bdb_booking_settings = new Directorist_Booking_Settings();
                self::$instance->BDB_Commission = new BDB_Commission();
                self::$instance->bdb_booking_wallet = new Directorist_Booking_Wallet();
                self::$instance->bdb_form_builder = new BDB_Form_builder();
                self::$instance->bdb_rent_calendar = new Directorist_Rent_Calendar();
                // add settings fields for our custom settings sections
                add_filter( 'atbdp_before_video_gallery_backend', array( self::$instance, 'add_backend_metabox' ) );
                //add shortocode settings
                add_filter( 'atbdp_pages_settings_fields', array( self::$instance, 'booking_pages_settings_fields' ) );
                // add license settings
                add_filter( 'atbdp_license_settings_controls', array( self::$instance, 'booking_license_settings_controls' ) );
                //register a widget
                add_action( 'widgets_init', array( self::$instance, 'add_widget_for_booking' ) );
                add_action( 'atbdp_after_contact_info_section', array( self::$instance, 'atbdp_after_contact_info_section' ), 10,3 );
                // license and auto update handler
                add_action( 'wp_ajax_atbdp_directorist_booking_license_activation', array( self::$instance, 'atbdp_directorist_booking_license_activation' ) );
                // license deactivation
                add_action( 'wp_ajax_atbdp_directorist_booking_license_deactivation' , array( self::$instance, 'atbdp_directorist_booking_license_deactivation' ) );

            }
            return self::$instance;
        }


        public function atbdp_directorist_booking_license_deactivation()
        {
            $license = !empty($_POST['directorist_booking_license']) ? trim($_POST['directorist_booking_license']) : '';
            $options = get_option('atbdp_option');
            $options['directorist_booking_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_directorist_booking_license', $license);
            $data = array();
            if ( !empty( $license ) ) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'deactivate_license',
                    'license' => $license,
                    'item_id' => ATBDP_BDB_POST_ID, // The ID of the item in EDD
                    'url' => home_url()
                );
                // Call the custom API.
                $response = wp_remote_post( ATBDP_AUTHOR_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = ( is_wp_error( $response ) && !empty( $response->get_error_message() ) ) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-booking');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    if ( !$license_data ) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-booking');
                        wp_send_json( $data );
                        die();
                    }
                    update_option( 'directorist_directorist_booking_license_status', $license_data->license );
                    if ( false === $license_data->success ) {
                        switch ($license_data->error) {
                            case 'expired' :
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-booking'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked' :
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-booking');
                                break;

                            case 'missing' :

                                $data['msg'] = __('Invalid license.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            case 'invalid' :
                            case 'site_inactive' :

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch' :

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-booking'), 'Directorist - Listings with Map');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            default :
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-booking');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License deactivated successfully!', 'directorist-booking');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-booking');
            }
            wp_send_json($data);
            die();
        }

        public function atbdp_directorist_booking_license_activation()
        {
            $license = !empty($_POST['directorist_booking_license']) ? trim($_POST['directorist_booking_license']) : '';
            $options = get_option('atbdp_option');
            $options['directorist_booking_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_directorist_booking_license', $license);
            $data = array();
            if (!empty($license)) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license' => $license,
                    'item_id' => ATBDP_BDB_POST_ID, // The ID of the item in EDD
                    'url' => home_url()
                );
                // Call the custom API.
                $response = wp_remote_post(ATBDP_AUTHOR_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-booking');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (!$license_data) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-booking');
                        wp_send_json($data);
                        die();
                    }
                    update_option('directorist_directorist_booking_license_status', $license_data->license);
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired' :
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-booking'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked' :
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-booking');
                                break;

                            case 'missing' :

                                $data['msg'] = __('Invalid license.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            case 'invalid' :
                            case 'site_inactive' :

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch' :

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-booking'), 'Directorist - Listings with Map');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-booking');
                                $data['status'] = false;
                                break;

                            default :
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-booking');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License activated successfully!', 'directorist-booking');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-booking');
            }
            wp_send_json($data);
            die();
        }


        public function load_needed_scripts()
        {
            wp_register_style('bdb-daterangepicker-style', plugin_dir_url(__FILE__) . 'public/assets/css/daterangepicker.css');
            if(is_rtl()) {
                wp_enqueue_style('bdb-css-rtl', plugin_dir_url(__FILE__) . '/public/assets/css/style-rtl.css');
            } else {
                wp_enqueue_style('bdb-style', plugin_dir_url(__FILE__) . 'public/assets/css/style.css');
            }
            wp_register_script('bdb-moment', plugin_dir_url(__FILE__) . 'public/assets/js/moment.min.js', array('jquery'));
            wp_register_script('bdb-flatpickr', plugin_dir_url(__FILE__) . 'public/assets/js/flatpickr.min.js', array('jquery'));
            wp_register_script('bdb-daterangepicker', plugin_dir_url(__FILE__) . 'public/assets/js/daterangepicker.js', array('jquery','bdb-moment'));
            $wordpress_data_format = bdb_date_time_wp_format();
            $ajax_url = admin_url( 'admin-ajax.php', 'relative' );
            $currency = get_directorist_option('g_currency', 'USD');
            $currency_position = get_directorist_option('g_currency_position', 'before');
            $currency_symbol = atbdp_currency_symbol($currency);
            $booking_array = array(
                'ajax_url'                	=> $ajax_url,
                'is_rtl'                  	=> is_rtl() ? 1 : 0,
                "applyLabel"				=> esc_html__( "Apply",'directorist-booking'),
                "cancelLabel" 				=> esc_html__( "Cancel",'directorist-booking'),
                "clearLabel" 				=> esc_html__( "Clear",'directorist-booking'),
                "fromLabel"					=> esc_html__( "From",'directorist-booking'),
                "toLabel" 					=> esc_html__( "To",'directorist-booking'),
                "customRangeLabel" 			=> esc_html__( "Custom",'directorist-booking'),
                "day_short_su"              => esc_html_x("Su", 'Short for Sunday', 'directorist-booking'),
                "day_short_mo"              => esc_html_x("Mo", 'Short for Monday','directorist-booking'),
                "day_short_tu"              => esc_html_x("Tu", 'Short for Tuesday','directorist-booking'),
                "day_short_we"              => esc_html_x("We", 'Short for Wednesday','directorist-booking'),
                "day_short_th"              => esc_html_x("Th", 'Short for Thursday','directorist-booking'),
                "day_short_fr"              => esc_html_x("Fr", 'Short for Friday','directorist-booking'),
                "day_short_sa"              => esc_html_x("Sa", 'Short for Saturday','directorist-booking'),
                'areyousure' 				=> esc_html__("Are you sure?","directorist-booking"),
                'currency_position' 		=> $currency_position,
                'currency_symbol' 		    => $currency_symbol,
            );
            wp_register_script('bdb-main-js', plugin_dir_url(__FILE__) . 'public/assets/js/main.js');
            wp_register_script('bdb-dashboard-js', plugin_dir_url(__FILE__) . '/public/assets/js/dashboard.js', array('jquery-ui-datepicker'));
            wp_localize_script('bdb-main-js','wordpress_data_format',$wordpress_data_format);
            wp_localize_script('bdb-main-js','bdb_booking',$booking_array);
            wp_localize_script('bdb-dashboard-js','wordpress_data_format',$wordpress_data_format);
            wp_localize_script('bdb-dashboard-js','bdb_booking',$booking_array);
            wp_register_style('bdb-front-css', plugin_dir_url(__FILE__) . 'admin/assets/css/bdb-main.css');

                $booking_type = get_directorist_option('booking_type', 'all');
                $booking_type_default_value = get_directorist_option('booking_type_default_value', 'service');
                $booking_type_default_value = !empty( $booking_type_default_value ) ? $booking_type_default_value : 'service';
                $warning_value = array(
                    'ajax_url'                	=> admin_url( 'admin-ajax.php', 'relative' ),
                    'confirmation_text' => __('Are you sure', 'directorist-booking'),
                    'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove!', 'directorist-booking'),
                    'confirm_delete' => __('Yes, Delete it!', 'directorist-booking'),
                    'deleted' => __('Deleted!', 'directorist-booking'),
                    'booking_type' => $booking_type,
                    'booking_type_default_value' => $booking_type_default_value,
                    'time_from_text' => __( 'Time From', 'directorist-booking' ),
                    'time_to_text' => __( 'Time To', 'directorist-booking' ),
                    'slots_text' => __( 'Slots', 'directorist-booking' )
                );
                wp_enqueue_script('bdb-front-main', plugin_dir_url(__FILE__) . 'admin/assets/js/main.js', array('jquery'), false, true);
                wp_enqueue_script('bdb-front-js', plugin_dir_url(__FILE__) . 'admin/assets/js/admin.js', array('jquery'), false, true);
                wp_enqueue_style('bdb-front-css', plugin_dir_url(__FILE__) . 'admin/assets/css/bdb-main.css');
                wp_localize_script('bdb-front-js', 'bdb_add_booking', $warning_value);
                wp_localize_script('bdb-front-main', 'bdb_booking', array_merge( $booking_array, $warning_value ) );
                wp_localize_script( 'bdb-front-js', 'booking_hours', array(
                    'monday'    => __( 'Monday','directorist-booking' ),
                    'tuesday'   => __('Tuesday', 'directorist-booking' ),
                    'wednesday' => __('Wednesday', 'directorist-booking' ),
                    'thursday'  => __('Thursday', 'directorist-booking' ),
                    'friday'    => __('Friday', 'directorist-booking' ),
                    'saturday'  => __('Saturday', 'directorist-booking' ),
                    'sunday'    => __('Sunday', 'directorist-booking' )
                ) );

        }

        public function load_needed_scripts_for_admin($screen) {
            global $typenow;
            if ( ATBDP_POST_TYPE == $typenow || 'bdb_commission' == $typenow ) {
                $booking_type = get_directorist_option('booking_type', 'all');
                $booking_type_default_value = get_directorist_option('booking_type_default_value', 'service');
                $booking_type_default_value = !empty( $booking_type_default_value ) ? $booking_type_default_value : 'service';
                $warning_value = array(
                    'ajax_url'                	=> admin_url( 'admin-ajax.php', 'relative' ),
                    'confirmation_text' => __('Are you sure', 'directorist-booking'),
                    'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove!', 'directorist-booking'),
                    'confirm_delete' => __('Yes, Delete it!', 'directorist-booking'),
                    'deleted' => __('Deleted!', 'directorist-booking'),
                    'booking_type' => $booking_type,
                    'booking_type_default_value' => $booking_type_default_value,
                    'time_from_text' => __( 'Time From', 'directorist-booking' ),
                    'time_to_text' => __( 'Time To', 'directorist-booking' ),
                    'slots_text' => __( 'Slots', 'directorist-booking' )
                );
                wp_enqueue_style('bdb-admin-css', plugin_dir_url(__FILE__) . 'admin/assets/css/bdb-main.css');
                wp_enqueue_script('bdb-admin-main', plugin_dir_url(__FILE__) . 'admin/assets/js/main.js', array( 'jquery'), false, true);
                wp_enqueue_script('bdb-admin-js', plugin_dir_url(__FILE__) . 'admin/assets/js/admin.js', array( 'jquery'), false, true);
                wp_localize_script('bdb-admin-js', 'bdb_add_booking', $warning_value);
                wp_localize_script('bdb-admin-main', 'bdb_booking', $warning_value);

            }
        }

        public function add_custom_page()
        {
            $create_permission = apply_filters('atbdp_create_required_pages', true);
            if ($create_permission) {
                bdb_create_required_pages();
            }
        }

        private function __construct()
        {
            /*making it private prevents constructing the object*/
        }

        /**
         * Throw error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', BDB_TEXTDOMAIN), '1.0');
        }

        /**
         * Disable unserializing of the class.
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __wakeup()
        {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', BDB_TEXTDOMAIN), '1.0');
        }

        /**
         * It register the text domain to the WordPress
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-booking', false, BDB_LANG_DIR);
        }

        /**
         *Save meta for admin
         */
        public function atbdp_bdb_admin_submission($metas) {

            $metas['_bdb']                        = !empty($_POST['bdb']) ? atbdp_sanitize_array($_POST['bdb']) : array(); // we are expecting array value
            $metas['_bdb_hide_booking']           = !empty($_POST['bdb_hide_booking'])? sanitize_text_field($_POST['bdb_hide_booking']) : '';
            $metas['_bdb_payment_booking']        = !empty($_POST['bdb_payment_booking'])? sanitize_text_field($_POST['bdb_payment_booking']) : '';
            $metas['_bdb_instant_booking']        = !empty($_POST['bdb_instant_booking'])? sanitize_text_field($_POST['bdb_instant_booking']) : '';
            $metas['_bdb_calender_unavailable']           = !empty($_POST['bdb_calender_unavailable'])? sanitize_text_field($_POST['bdb_calender_unavailable']) : '';
            $metas['_bdb_calender_price']                 = !empty($_POST['bdb_calender_price'])? sanitize_text_field($_POST['bdb_calender_price']) : '';
            $metas['_bdb_reservation_fee']        = !empty($_POST['bdb_reservation_fee'])? (int) $_POST['bdb_reservation_fee'] : '';
            $metas['_bdb_weekend_price']        = !empty($_POST['bdb_weekend_price'])? (int) $_POST['bdb_weekend_price'] : '';
            $metas['_bdb_reservation_guest']      = !empty($_POST['bdb_reservation_guest'])? (int) $_POST['bdb_reservation_guest'] : '';
            $metas['_bdb_slot_status']            = !empty($_POST['bdb_slot_status'])? sanitize_text_field($_POST['bdb_slot_status']) : '';
            $metas['_bdb_display_slot_available_text']    = !empty($_POST['bdb_display_slot_available_text'])? sanitize_text_field($_POST['bdb_display_slot_available_text']) : '';
            $metas['_bdb_display_available_time']    = !empty($_POST['bdb_display_available_time'])? sanitize_text_field($_POST['bdb_display_available_time']) : '';
            $metas['_bdb_slot_available_text']    = !empty($_POST['bdb_slot_available_text'])? sanitize_text_field($_POST['bdb_slot_available_text']) : '';
            $metas['_bdb_available_time_text']    = !empty($_POST['bdb_available_time_text'])? sanitize_text_field($_POST['bdb_available_time_text']) : '';
            $metas['_bdb_booking_type']    = !empty($_POST['bdb_booking_type'])? $_POST['bdb_booking_type'] : '';
            $metas['_bdb_display_available_ticket']       = !empty($_POST['bdb_display_available_ticket'])? $_POST['bdb_display_available_ticket'] : '';
            $metas['_bdb_available_ticket_text']       = !empty($_POST['bdb_available_ticket_text'])? $_POST['bdb_available_ticket_text'] : '';
            $metas['_bdb_event_ticket']    = !empty($_POST['bdb_event_ticket'])? $_POST['bdb_event_ticket'] : '';
            $metas['_bdb_maximum_ticket_allowed']    = !empty($_POST['bdb_maximum_ticket_allowed'])? $_POST['bdb_maximum_ticket_allowed'] : '';
            return $metas;

        }

        /**
         * Save meta for frontend
         */
        public function atbdp_listing_meta_user_submission( $metas ) {

            $metas['_bdb']                                = !empty($_POST['bdb']) ? atbdp_sanitize_array($_POST['bdb']) : array(); // we are expecting array value
            $metas['_bdb_hide_booking']                   = !empty($_POST['bdb_hide_booking'])? sanitize_text_field($_POST['bdb_hide_booking']) : '';
            $metas['_bdb_payment_booking']                = !empty($_POST['bdb_payment_booking'])? sanitize_text_field($_POST['bdb_payment_booking']) : '';
            $metas['_bdb_instant_booking']                = !empty($_POST['bdb_instant_booking'])? sanitize_text_field($_POST['bdb_instant_booking']) : '';
            $metas['_bdb_calender_unavailable']           = !empty($_POST['bdb_calender_unavailable'])? sanitize_text_field($_POST['bdb_calender_unavailable']) : '';
            $metas['_bdb_calender_price']                 = !empty($_POST['bdb_calender_price'])? sanitize_text_field($_POST['bdb_calender_price']) : '';
            $metas['_bdb_reservation_fee']                = !empty($_POST['bdb_reservation_fee'])? (int) $_POST['bdb_reservation_fee'] : '';
            $metas['_bdb_weekend_price']                = !empty($_POST['bdb_weekend_price'])? (int) $_POST['bdb_weekend_price'] : '';
            $metas['_bdb_reservation_guest']              = !empty($_POST['bdb_reservation_guest'])? (int) $_POST['bdb_reservation_guest'] : '';
            $metas['_bdb_slot_status']                    = !empty($_POST['bdb_slot_status'])? sanitize_text_field($_POST['bdb_slot_status']) : '';
            $metas['_bdb_display_slot_available_text']    = !empty($_POST['bdb_display_slot_available_text'])? sanitize_text_field($_POST['bdb_display_slot_available_text']) : '';
            $metas['_bdb_display_available_time']         = !empty($_POST['bdb_display_available_time'])? sanitize_text_field($_POST['bdb_display_available_time']) : '';
            $metas['_bdb_slot_available_text']            = !empty($_POST['bdb_slot_available_text'])? sanitize_text_field($_POST['bdb_slot_available_text']) : '';
            $metas['_bdb_available_time_text']            = !empty($_POST['bdb_available_time_text'])? sanitize_text_field($_POST['bdb_available_time_text']) : '';
            $metas['_bdb_booking_type']                   = !empty($_POST['bdb_booking_type'])? $_POST['bdb_booking_type'] : '';
            $metas['_bdb_event_ticket']                   = !empty($_POST['bdb_event_ticket'])? $_POST['bdb_event_ticket'] : '';
            $metas['_bdb_display_available_ticket']       = !empty($_POST['bdb_display_available_ticket'])? $_POST['bdb_display_available_ticket'] : '';
            $metas['_bdb_available_ticket_text']          = !empty($_POST['bdb_available_ticket_text'])? $_POST['bdb_available_ticket_text'] : '';
            $metas['_bdb_maximum_ticket_allowed']         = !empty($_POST['bdb_maximum_ticket_allowed'])? $_POST['bdb_maximum_ticket_allowed'] : '';
            return $metas;

        }

        /**
         * It Includes and requires necessary files.
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function includes()
        {
            require_once BDB_DIR . 'widget/class-booking-widget.php';
            require_once BDB_DIR . 'inc/helper.php';
            require_once BDB_DIR . 'inc/booking-database.php';
            require_once BDB_DIR . 'inc/booking-payment.php';
            require_once BDB_DIR . 'inc/booking-dashboard.php';
            require_once BDB_DIR . 'inc/booking-settings.php';
            require_once BDB_DIR . 'inc/commission/commission.php';
            require_once BDB_DIR . 'inc/commission/wallet.php';
            require_once BDB_DIR . 'inc/form-builder.php';
            require_once BDB_DIR . 'inc/booking-calender.php';
            // setup the updater
            if (!class_exists('EDD_SL_Plugin_Updater')) {
                // load our custom updater if it doesn't already exist
                include(dirname(__FILE__) . '/inc/EDD_SL_Plugin_Updater.php');
            }
            $license_key = trim(get_option('directorist_directorist_booking_license'));
            new EDD_SL_Plugin_Updater(ATBDP_AUTHOR_URL, __FILE__, array(
                'version' => BDB_VERSION,        // current version number
                'license' => $license_key,    // license key (used get_option above to retrieve from DB)
                'item_id' => ATBDP_BDB_POST_ID,    // id of this plugin
                'author' => 'AazzTech',    // author of this plugin
                'url' => home_url(),
                'beta' => false // set to true if you wish customers to receive update notifications of beta releases
            ));

        }

        /**
         * It  loads a template file from the Default template directory.
         * @param string $name Name of the file that should be loaded from the template directory.
         * @param array $args Additional arguments that should be passed to the template file for rendering dynamic  data.
         */
        public function load_template($name, $args = array())
        {
            if ( is_array( $args ) ) {
                extract( $args );
            }

            global $post;
            include(BDB_TEMPLATES_DIR . $name . '.php');

        }

        /**
         * Add new metabox for restaurant reservation
         */
        public function add_backend_metabox() {
            // Check if booking is enabled
            $booking_is_enabled  = get_directorist_option( 'enable_booking', 1 );
            if (  empty( $booking_is_enabled ) ) { return; }

            $bdb_section_label = get_directorist_option('bdb_section_label', __('Booking','directorist-booking'));
            add_meta_box('bdb_booking',
                $bdb_section_label,
                array($this, 'add_booking_fields'),
                ATBDP_POST_TYPE,
                'normal', 'high'
            );
        }

            /**
         * @since 1.0
         */
        public function booking_license_settings_controls($default)
        {
            $status = get_option('directorist_directorist_booking_license_status');
            if (!empty($status) && ($status !== false && $status == 'valid')) {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'directorist_booking_deactivated',
                    'label' => __('Action', 'directorist-booking'),
                    'validation' => 'numeric',
                );
            } else {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'directorist_booking_activated',
                    'label' => __('Action', 'directorist-booking'),
                    'validation' => 'numeric',
                );
            }
            $new = apply_filters('atbdp_directorist_booking_license_controls', array(
                'type' => 'section',
                'title' => __('Booking', 'directorist-booking'),
                'description' => __('You can active your Booking extension here.', 'directorist-booking'),
                'fields' => apply_filters('atbdp_directorist_booking_license_settings_field', array(
                    array(
                        'type' => 'textbox',
                        'name' => 'directorist_booking_license',
                        'label' => __('License', 'directorist-booking'),
                        'description' => __('Enter your Booking extension license', 'directorist-booking'),
                        'default' => '',
                    ),
                    $action
                )),
            ));
            $settings = apply_filters('atbdp_licence_menu_for_booking', true);
            if($settings){
                array_push($default, $new);
            }
            return $default;
        }


        /**
         * Add new page settings
         */
        public function booking_pages_settings_fields($fields)
        {

            $permission = array(
                'type' => 'select',
                'name' => 'booking_confirmation',
                'label' => __('Booking Confirmation Page', 'directorist-booking'),
                'items' => $this->get_pages_vl_arrays(), // eg. array( array('value'=> 123, 'label'=> 'page_name') );
                'description' => sprintf(__('Following shortcode must be in the selected page %s', 'directorist-pricing-plans'), '<strong style="color: #ff4500;">[directorist_booking_confirmation]</strong>'),
                'default' => atbdp_get_option('booking_confirmation', 'atbdp_general'),
                'validation' => 'numeric',
            );
            // lets push our settings to the end of the other settings field and return it.
            array_push($fields, $permission);
            return $fields;
        }

        function get_pages_vl_arrays()
        {
            $pages = get_pages();
            $pages_options = array();
            if ($pages) {
                foreach ($pages as $page) {
                    $pages_options[] = array('value' => $page->ID, 'label' => $page->post_title);
                }
            }

            return $pages_options;
        }


        /**
         * It adds the booking input fields to the add listing page
         * @since 1.0
         */
        public function add_booking_fields($post)
        {
            $booking = get_directorist_option( 'enable_booking', 1 );
            if( ! empty( $booking ) ) {
                $booking_type = get_directorist_option('booking_type', 'all');
                $booking_type_default_value = get_directorist_option('booking_type_default_value', 'service');
                $booking_type_default_value = !empty( $booking_type_default_value ) ? $booking_type_default_value : 'service';
                $listing_id = $post->ID;
                wp_enqueue_style('bdb-admin-css');
                wp_enqueue_script('bdb-admin-js');
                wp_enqueue_script('bdb-admin-main');
                $warning_value = array(
                    'confirmation_text' => __('Are you sure', 'directorist-booking'),
                    'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove!', 'directorist-booking'),
                    'confirm_delete' => __('Yes, Delete it!', 'directorist-booking'),
                    'deleted' => __('Deleted!', 'directorist-booking'),
                    'booking_type' => $booking_type,
                    'booking_type_default_value' => $booking_type_default_value,
                );
                wp_localize_script('bdb-admin-main', 'bdb_add_booking', $warning_value);
                wp_localize_script('bdb-admin-js', 'bdb_booking', $warning_value);
                global $pagenow;
                $slot_available_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                $available_ticket_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                $available_time_checked = (!empty($pagenow) && 'post-new.php' == $pagenow) ? 'checked': '';
                include BDB_TEMPLATES_DIR . 'booking-fields.php';
            }
        }

        public function add_widget_for_booking() {
            $booking = get_directorist_option('enable_booking',1);
            if(!empty($booking)) {
                register_widget('BDB_Widget_Template');
            }
        }

        public function atbdp_after_contact_info_section( $type, $listing_info, $p_id ) {
            // Check if booking is enabled
            $plan_allows_booking = true;
            $booking_is_enabled  = get_directorist_option( 'enable_booking', 1 );
            $listing_id          = get_query_var('atbdp_listing_id', 0);
            if ( is_fee_manager_active() ) {
                $plan_id             = get_post_meta( $listing_id, '_fm_plans', true );
                $plan_allows_booking = atbdp_plan_allows_booking( $plan_id  );
            }

            if (  empty( $booking_is_enabled ) || empty( $plan_allows_booking ) ) { return; }

            $booking_type = get_directorist_option('booking_type', 'all');
            $booking_type_default_value = get_directorist_option('booking_type_default_value', 'service');
            $booking_type_default_value = !empty( $booking_type_default_value ) ? $booking_type_default_value : 'service';

            wp_enqueue_style('bdb-front-css');
            wp_enqueue_style('bdb-style');
            wp_enqueue_script('bdb-front-js');
            wp_enqueue_script('bdb-front-main');

            $bdb_section_label = get_directorist_option('bdb_section_label', __('Booking','directorist-booking'));
            $warning_value = array(
                'confirmation_text' => __('Are you sure', 'directorist-booking'),
                'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove!', 'directorist-booking'),
                'confirm_delete' => __('Yes, Delete it!', 'directorist-booking'),
                'deleted' => __('Deleted!', 'directorist-booking'),
                'booking_type' => $booking_type,
                'booking_type_default_value' => $booking_type_default_value,
            );
            //wp_localize_script('bdb-front-main', 'bdb_add_booking', $warning_value);
           // wp_localize_script('bdb-front-js', 'bdb_booking', $warning_value);
            $p_id = get_query_var('atbdp_listing_id', 0);
            $slot_available_checked = empty($p_id) ? 'checked': '';
            $available_ticket_checked = empty($p_id) ? 'checked': '';
            $available_time_checked = empty($p_id) ? 'checked': '';
            ?>
            <div class="atbd_content_module atbd-booking-information">
                <div class="atbd_content_module__tittle_area">
                    <div class="atbd_area_title">
                        <h4><?php echo !empty($bdb_section_label) ? sanitize_text_field($bdb_section_label) : __('Booking','directorist-booking'); ?></h4>
                    </div>

                </div>

                <div class="atbdb_content_module_contents">

                    <?php
                    include BDB_TEMPLATES_DIR . 'booking-fields.php';
                    ?>

                </div>
            </div>
            <?php
        }

        public static function get_version_from_file_content( $file_path = '' ) {
            $version = '';

            if ( ! file_exists( $file_path ) ) {
                return $version;
            }

            $content = file_get_contents( $file_path );
            $version = self::get_version_from_content( $content );

            return $version;
        }

        public static function get_version_from_content( $content = '' ) {
            $version = '';

            if ( preg_match('/\*[\s\t]+?version:[\s\t]+?([0-9.]+)/i', $content, $v) ) {
                $version = $v[1];
            }

            return $version;
        }

        /**
         * Setup plugin constants.
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function setup_constants()
        {
            // Plugin version
            if (!defined('BDB_VERSION')) {
                define('BDB_VERSION', self::get_version_from_file_content( __FILE__ ));
            }

            // plugin author url
            if (!defined('ATBDP_AUTHOR_URL')) {
                define('ATBDP_AUTHOR_URL', 'https://directorist.com');
            }
            // post id from download post type (edd)
            if (!defined('ATBDP_BDB_POST_ID')) {
                define('ATBDP_BDB_POST_ID', 21718);
            }
            // Plugin Folder Path.
            if ( ! defined( 'BDB_DIR' ) ) { define( 'BDB_DIR', plugin_dir_path( __FILE__ ) ); }
            // Plugin Folder URL.
            if ( ! defined( 'BDB_URL' ) ) { define( 'BDB_URL', plugin_dir_url( __FILE__ ) ); }
            // Plugin Root File.
            if ( ! defined( 'BDB_FILE' ) ) { define( 'BDB_FILE', __FILE__ ); }
            if ( ! defined( 'BDB_BASE' ) ) { define( 'BDB_BASE', plugin_basename( __FILE__ ) ); }
            // Plugin Text domain File.
            if ( ! defined( 'BDB_TEXTDOMAIN' ) ) { define( 'BDB_TEXTDOMAIN', 'directorist-restaurant-reservation' ); }
            // Plugin Language File Path
            if ( !defined('BDB_LANG_DIR') ) { define('BDB_LANG_DIR', dirname(plugin_basename( __FILE__ ) ) . '/languages'); }
            // Plugin Template Path
            if ( !defined('BDB_TEMPLATES_DIR') ) { define('BDB_TEMPLATES_DIR', BDB_DIR.'templates/'); }
        }

    }

    /**
     * The main function for that returns BD_Booking
     *
     * The main function responsible for returning the one true BD_Booking
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     *
     * @since 1.0
     * @return object|BD_Booking The one true BD_Booking Instance.
     */
    function BD_Booking()
    {
        return BD_Booking::instance();
    }

    if ( ! function_exists( 'directorist_is_plugin_active' ) ) {
        function directorist_is_plugin_active( $plugin ) {
            return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || directorist_is_plugin_active_for_network( $plugin );
        }
    }

    if ( ! function_exists( 'directorist_is_plugin_active_for_network' ) ) {
        function directorist_is_plugin_active_for_network( $plugin ) {
            if ( ! is_multisite() ) {
                return false;
            }

            $plugins = get_site_option( 'active_sitewide_plugins' );
            if ( isset( $plugins[ $plugin ] ) ) {
                    return true;
            }

            return false;
        }
    }

    if (  directorist_is_plugin_active( 'directorist/directorist-base.php' ) ) {
        BD_Booking(); // get the plugin running
    }

    function bdb_booking_db() {
        global $wpdb;
        $collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty( $wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( ! empty( $wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "
	CREATE TABLE {$wpdb->prefix}directorist_booking (
		`ID` bigint(20) UNSIGNED  NOT NULL auto_increment,
		`bookings_author` bigint(20) UNSIGNED NOT NULL,
		`owner_id` bigint(20) UNSIGNED NOT NULL,
		`listing_id` bigint(20) UNSIGNED NOT NULL,
		`date_start` datetime DEFAULT NULL,
		`date_end` datetime DEFAULT NULL,
		`comment` text,
		`order_id` bigint(20) UNSIGNED DEFAULT NULL,
		`status` varchar(100) DEFAULT NULL,
		`type` text,
		`created` datetime DEFAULT NULL,
		`expiring` datetime DEFAULT NULL,
		`price` mediumint(8) UNSIGNED DEFAULT NULL,
		PRIMARY KEY  (ID)
	) $collate;
	";

        dbDelta( $sql );

    }

    register_activation_hook(__FILE__,'bdb_booking_db');

}