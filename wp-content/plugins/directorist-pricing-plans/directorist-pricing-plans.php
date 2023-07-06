<?php
/**
 * Plugin Name: Directorist - Pricing Plans
 * Plugin URI: https://directorist.com/product/directorist-pricing-plans
 * Description: This is an extension for Directorist plugin. It allows you to create & sell unlimited pricing plans to monetize your directory.
 * Version: 2.0.10
 * Author: wpWax
 * Author URI: https://wpwax.com
 * License: GPLv2 or later
 * Text Domain: directorist-pricing-plans
 * Domain Path: /languages
 */
// prevent direct access to the file
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'ATBDP_Pricing_Plans' ) && ! class_exists( 'DWPP_Pricing_Plans' ) ) {
    final class ATBDP_Pricing_Plans {
        /** Singleton */
        /**
         * @var ATBDP_Pricing_Plans The one true ATBDP_Pricing_Plans
         * @since 1.0
         */
        private static $instance;

        /**
         * Main ATBDP_Pricing_Plans Instance.
         *
         * Insures that only one instance of ATBDP_Pricing_Plans exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @return object|ATBDP_Pricing_Plans The one true ATBDP_Pricing_Plans
         * @uses ATBDP_Pricing_Plans::setup_constants() Setup the constants needed.
         * @uses ATBDP_Pricing_Plans::includes() Include the required files.
         * @uses ATBDP_Pricing_Plans::load_textdomain() load the language files.
         * @see  ATBDP_Pricing_Plans()
         * @since 1.0
         * @static
         * @static_var array $instance
         */
        public static function instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ATBDP_Pricing_Plans ) ) {
                self::$instance = new ATBDP_Pricing_Plans;
                self::$instance->setup_constants();
                self::$instance->includes();
                // enqueue required styles and scripts
                new ATPP_Enqueuer();
                // on activation
                new ATPP_Init();
                // Add Settings fields to the extension general fields
                new ATPP_Settings();
                // add and modify post types
                new ATPP_CPTs();
                // data handler
                new ATPP_Ajax_Handler();
                // main controller
                new ATPP_Data_Controller();
                //views
                new ATPP_Views();
                // payment
                new ATPP_Payments();
                // Post Type
                new ATPP_Post_Type_Manager();
                // direct purchase
                new Directorist_Direct_Purchase();
                // subcription
                new Directorist_Subscription();
                // email
                new Directorist_Plan_Email();

                new Directorist_Plan_Restrictions();


                add_filter( 'atbdp_reviewed_listing_status_controller_argument', [self::$instance, 'update_edited_listings_status'] );

                add_action( 'directorist_before_insert_notification', [self::$instance, 'directorist_before_insert_notification'] );

            }
            return self::$instance;
        }

        private function __construct() {
            /*making it private prevents constructing the object*/
        }

        public function directorist_before_insert_notification( $listing_id ) {
            $order_id     = get_post_meta( $listing_id, '_fm_plan_ordered', true );
            $order_status = get_post_meta( $order_id, '_payment_status', true );
            if ( 'completed' != $order_status ) {
                return false;
            }
        }

        // update_edited_listings_status
        public static function update_edited_listings_status( $args = [] ) {
            $plan_id = get_post_meta( get_the_ID(), '_fm_plans', true );

            if ( empty( $plan_id ) ) {
                return $args;
            }

            $total_price = atpp_total_price( $plan_id );
            $price       = ( is_numeric( $total_price ) ) ? (int) $total_price : 0;

            if ( $price <= 0 ) {
                $id = get_the_ID();

                $directory_type = get_post_meta( $id, '_directory_type', true );
                if ( ! is_numeric( $directory_type ) ) {
                    $term           = get_term_by( 'slug', $directory_type, ATBDP_TYPE );
                    $directory_type = ! empty( $term ) ? $term->term_id : '';

                    update_post_meta( $id, '_directory_type', $directory_type );
                    wp_set_object_terms( $id, (int) $directory_type, ATBDP_TYPE );
                }

                $new_l_status  = get_term_meta( $directory_type, 'new_listing_status', true );
                $edit_l_status = get_term_meta( $directory_type, 'edit_listing_status', true );

                $edited = isset( $_GET['edited'] ) ? esc_attr( $_GET['edited'] ) : '';
                $status = ( $edited ) ? $edit_l_status : $new_l_status;

                $args['post_status'] = $status;
            }

            return $args;
        }

        /**
         * Throw error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @return void
         * @since 1.0
         * @access protected
         */
        public function __clone() {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'directorist-pricing-plans' ), '1.0' );
        }

        /**
         * Disable unserializing of the class.
         *
         * @return void
         * @since 1.0
         * @access protected
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'directorist-pricing-plans' ), '1.0' );
        }

        /**
         * It Includes and requires necessary files.
         *
         * @access private
         * @return void
         * @since 1.0
         */
        private function includes() {
            require_once ATPP_INC_DIR . 'helper-functions.php';
            require_once ATPP_INC_DIR . 'validator.php';
            require_once ATPP_INC_DIR . 'init.php';
            require_once ATPP_CLASSES_DIR . 'class-enqueuer.php';
            require_once ATPP_CLASSES_DIR . 'class-ajax-handler.php';
            require_once ATPP_CLASSES_DIR . 'class-listing_type_manager.php';
            require_once ATPP_CLASSES_DIR . 'class-custom-post-types.php';
            require_once ATPP_CLASSES_DIR . 'class-data-controller.php';
            require_once ATPP_CLASSES_DIR . 'class-enqueuer.php';
            require_once ATPP_CLASSES_DIR . 'class-payments.php';
            require_once ATPP_CLASSES_DIR . 'class-settings.php';
            require_once ATPP_CLASSES_DIR . 'class-direct-purchase.php';
            require_once ATPP_CLASSES_DIR . 'class-subscription.php';
            require_once ATPP_CLASSES_DIR . 'class-email.php';
            require_once ATPP_CLASSES_DIR . 'class-restrictions.php';
            require_once ATPP_VIEWS_DIR . 'html-views.php';
            // setup the updater
            if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
                // load our custom updater if it doesn't already exist
                include dirname( __FILE__ ) . '/inc/EDD_SL_Plugin_Updater.php';
            }
            $license_key = trim( get_option( 'directorist_pricing_license' ) );
            new EDD_SL_Plugin_Updater( ATBDP_AUTHOR_URL, __FILE__, [
                'version' => ATPP_VERSION, // current version number
                'license' => $license_key, // license key (used get_option above to retrieve from DB)
                'item_id' => ATBDP_PRICING_PLAN_POST_ID, // id of this plugin
                'author'  => 'AazzTech', // author of this plugin
                'url'     => home_url(),
                'beta'    => false, // set to true if you wish customers to receive update notifications of beta releases
            ] );
        }

        /**
         * It  loads a template file from the Default template directory.
         * @param string $template Name of the file that should be loaded from the template directory.
         * @param array $args Additional arguments that should be passed to the template file for rendering dynamic  data.
         */
        public function load_template( $template, $args = [] ) {
            atpp_get_template( $template, $args );
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

            if ( preg_match( '/\*[\s\t]+?version:[\s\t]+?([0-9.]+)/i', $content, $v ) ) {
                $version = $v[1];
            }

            return $version;
        }

        /**
         * Setup plugin constants.
         *
         * @access private
         * @return void
         * @since 1.0
         */
        private function setup_constants() {
            if ( ! defined( 'ATPP_FILE' ) ) {define( 'ATPP_FILE', __FILE__ );}
            $version = self::get_version_from_file_content( ATPP_FILE );

            require_once plugin_dir_path( __FILE__ ) . '/config.php'; // loads constant from a file so that it can be available on all files.
        }
    }

    if ( ! function_exists( 'directorist_is_plugin_active' ) ) {
        function directorist_is_plugin_active( $plugin ) {
            return in_array( $plugin, (array) get_option( 'active_plugins', [] ), true ) || directorist_is_plugin_active_for_network( $plugin );
        }
    }

    if ( ! function_exists( 'directorist_is_plugin_active_for_network' ) ) {
        function directorist_is_plugin_active_for_network( $plugin ) {
            if ( ! is_multisite() ) {
                return false;
            }

            $plugins = get_site_option( 'active_sitewide_plugins' );
            if ( isset( $plugins[$plugin] ) ) {
                return true;
            }

            return false;
        }
    }

    /**
     * The main function for that returns ATBDP_Pricing_Plans
     *
     * The main function responsible for returning the one true ATBDP_Pricing_Plans
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     *
     * @return object|ATBDP_Pricing_Plans The one true ATBDP_Pricing_Plans Instance.
     * @since 1.0
     */
    function ATBDP_Pricing_Plans() {
        return ATBDP_Pricing_Plans::instance();
    }

    $is_active_directorist      = directorist_is_plugin_active( 'directorist/directorist-base.php' );
    $is_active_wc_pricing_plans = directorist_is_plugin_active( 'directorist-woocommerce-pricing-plans/directorist-woocommerce-pricing-plans.php' );

    if ( $is_active_directorist && ! $is_active_wc_pricing_plans ) {
        ATBDP_Pricing_Plans(); // get the plugin running
    }
}