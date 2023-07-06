<?php
/**
 * Plugin Name: Directorist - Claim Listing
 * Plugin URI:  https://directorist.com/product/directorist-claim-listing
 * Description: This is an extension for Directorist plugin. It allows you to monetize your directory by verifying an author.
 * Version: 1.4.9
 * Author: wpWax
 * Author URI: https://wpwax.com
 * License: GPLv2 or later
 * Text Domain: directorist-claim-listing
 * Domain Path: /languages
 */
// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');
if (!class_exists('DCL_Base')){
    final class DCL_Base
    {

        /** Singleton *************************************************************/

        /**
         * @var DCL_Base The one true DCL_Base
         * @since 1.0
         */
        private static $instance;

        private static $plan_id;

        /**
         * Main DCL_Base Instance.
         *
         * Insures that only one instance of DCL_Base exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @return object|DCL_Base The one true DCL_Base
         * @uses DCL_Base::setup_constants() Setup the constants needed.
         * @uses DCL_Base::includes() Include the required files.
         * @uses DCL_Base::load_textdomain() load the language files.
         * @see  DCL_Base()
         * @since 1.0
         * @static
         * @static_var array $instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof DCL_Base)) {
                self::$instance = new DCL_Base;
                self::$instance->setup_constants();

                add_action('plugins_loaded', array(self::$instance, 'load_textdomain'));
                add_action('plugins_loaded', array(self::$instance, 'create_shortcodes'));

                self::$instance->includes();
                new DCL_Enqueuer();// enqueue required styles and scripts

                // Add Settings fields to the extension general fields
                // push license settings
                add_filter('atbdp_license_settings_controls', array(self::$instance, 'claim_license_settings_controls'));
                add_filter('atbdp_default_notifiable_events', array(self::$instance, 'dcl_claim_notification'));
                add_filter('atbdp_default_events_to_notify_admin', array(self::$instance, 'dcl_default_events_to_notify_admin'));
                add_filter('atbdp_default_events_to_notify_user', array(self::$instance, 'dcl_default_events_to_notify_user'));
                add_filter('atbdp_only_user_notifiable_events', array(self::$instance, 'dcl_claim_confirmation_notification'));
                //add_action('admin_notices', array(self::$instance, 'directorist_add_plan_pages'));
                add_action('admin_enqueue_scripts', array(self::$instance, 'register_necessary_scripts_front'));
                add_action('wp_enqueue_scripts', array(self::$instance, 'register_necessary_scripts_front'));
                add_action('init', array(self::$instance, 'register_claim_listing_post_type'));
                add_action('add_meta_boxes', array(self::$instance, 'dcl_add_meta_boxes'));
                add_action('save_post', array(self::$instance, 'dcl_save_meta_data'));
                add_filter('manage_dcl_claim_listing_posts_columns', array(self::$instance, 'atpp_add_new_plan_columns'));
                add_action('manage_dcl_claim_listing_posts_custom_column', array(self::$instance, 'atpp_custom_field_column_content'), 10, 2);
                add_filter('post_row_actions', array(self::$instance, 'atpp_remove_row_actions_for_quick_view'), 10, 2);
                add_action('atbdp_after_video_metabox_backend_add_listing', array(self::$instance, 'dcl_admin_metabox'));
                add_action('save_post', array(self::$instance, 'dcl_save_metabox'), 10, 2);
                add_action('atbdp_single_listing_after_title', array(self::$instance, 'verified_bedge_in_single_listing'));
                add_action('directorist_single_listing_after_title', array(self::$instance, 'verified_bedge_in_single_listing'));
                //register Claim widget
                add_action('widgets_init', array(self::$instance, 'register_widget'));

                // For shortcode
                add_action('atbdp_claim_listing', array(self::$instance, 'dcl_listing_claim_now_button'));
                // For page template
               // add_action('atbdp_after_contact_listing_owner_section', array(self::$instance, 'dcl_listing_claim_now_button'));

                /*Submit claim*/
                add_action('wp_ajax_dcl_submit_claim', array(self::$instance, 'dcl_submit_claim'));
                add_action('wp_ajax_nopriv_dcl_submit_claim', array(self::$instance, 'dcl_submit_claim'));
                add_action('wp_ajax_dcl_plan_allowances', array(self::$instance, 'dcl_plan_allowances'));

                //stuff for individual price set for claimer
                if (!class_exists('ATBDP_Pricing_Plans' || 'DWPP_Pricing_Plans')) {
                    add_filter('atbdp_checkout_form_data', array(self::$instance, 'dcl_checkout_form_data'), 10, 2);
                    add_filter('atbdp_payment_receipt_data', array(self::$instance, 'dcl_payment_receipt_data'), 11, 3);
                    add_filter('atbdp_order_details', array(self::$instance, 'dcl_order_details'), 10, 3);
                    add_filter('atbdp_order_items', array(self::$instance, 'dcl_order_items'), 10, 4);
                }

                // license and auto update handler
                add_action('wp_ajax_atbdp_claim_license_activation', array(self::$instance, 'claim_license_activation'));
                // license deactivation
                add_action('wp_ajax_atbdp_claim_license_deactivation', array(self::$instance, 'atbdp_claim_license_deactivation'));
                // settings
                add_filter( 'atbdp_listing_type_settings_field_list', array( self::$instance, 'atbdp_listing_type_settings_field_list' ) );
                add_filter( 'atbdp_extension_fields', array( self::$instance, 'atbdp_extension_fields' ) );
                add_filter( 'atbdp_extension_settings_submenu', array( self::$instance, 'atbdp_extension_settings_submenus' ) );
                add_filter( 'atbdp_email_templates_settings_sections', array( self::$instance, 'atbdp_email_templates_settings_sections' ) );
            }


            return self::$instance;
        }

        private function __construct()
        {
            /*making it private prevents constructing the object*/
        }

        public function atbdp_email_templates_settings_sections( $section ) {
            $section['new_claim'] = [
                'title'       => __('For New Claim', 'directorist-claim-listing'),
                'description' => '',
                'fields'      => [
                    'email_sub_new_claim', 'email_tmpl_new_claim'
                 ],
            ];
            $section['approved_claim_confirmation'] = [
                'title'       => __('Approved Claim Confirmation', 'directorist-claim-listing'),
                'description' => '',
                'fields'      => [
                    'email_sub_approved_claim', 'email_tmpl_approved_claim'
                 ],
            ];
            $section['declined_claim_confirmation'] = [
                'title'       => __('Declined Claim Confirmation', 'directorist-claim-listing'),
                'description' => '',
                'fields'      => [
                    'email_sub_declined_claim', 'email_tmpl_declined_claim'
                 ],
            ];

            return $section;
        }

        public function atbdp_listing_type_settings_field_list( $claim_fields ) {
            $claim_fields['enable_claim_listing'] = [
                'label'             => __('Claim Listing', 'directorist-claim-listing'),
                'type'              => 'toggle',
                'value'             => true,
                'description'       => __('You can disable it for users.', 'directorist-claim-listing'),
            ];
            $claim_fields['claim_widget_title'] = [
                'type'              => 'text',
                'label'             => __('Claim Widget Title', 'directorist-claim-listing'),
                'value'             => __('Is this your business?', 'directorist-claim-listing'),
            ];
            $claim_fields['claim_widget_description'] = [
                'type'              => 'textarea',
                'label'             => __('Description', 'directorist-claim-listing'),
                'value'             => __('Claim listing is the best way to manage and protect your business.', 'directorist-claim-listing'),
            ];
            $claim_fields['claim_charge_by'] = [
                'label' => __('Method of Charging', 'directorist-claim-listing'),
                'type'  => 'select',
                'value' => 'free_claim',
                'options' => [
                    [
                        'value' => 'free_claim',
                        'label' => __('Claim for Free', 'directorist-claim-listing'),
                    ],
                    [
                        'value' => 'static_fee',
                        'label' => __('Set a Claim Fee', 'directorist-claim-listing'),
                    ],
                ],
            ];
            $claim_fields['claim_listing_price'] = [
                'type'              => 'text',
                'label'             => __('Claim Fee in ', 'directorist-claim-listing') . atbdp_get_payment_currency(),
                'value'             => 19.99,
                'show-if' => [
                    'where' => "claim_charge_by",
                    'conditions' => [
                        ['key' => 'value', 'compare' => '=', 'value' => 'static_fee'],
                    ],
                ],
            ];
            $claim_fields['claim_now'] = [
                'type'              => 'text',
                'label'             => __('Claim Now Button', 'directorist-claim-listing'),
                'value'             => __('Claim Now!', 'directorist-claim-listing'),
            ];
            $claim_fields['verified_badge'] = [
                'label'             => __('Display Claimed Badge', 'directorist-claim-listing'),
                'type'              => 'toggle',
                'value'             => true,
            ];
            $claim_fields['verified_text'] = [
                'type'              => 'text',
                'label'             => __('Verified Text', 'directorist-claim-listing'),
                'value'             => __('Claimed', 'directorist-claim-listing'),
            ];
            $claim_fields['email_sub_new_claim']  = [
                'type'           => 'text',
                'label'          => __('Email Subject', 'directorist-claim-listing'),
                'description'    => __('Edit the subject for sending to the user when a claim is submitted.', 'directorist-claim-listing'),
                'value'          => __('[==SITE_NAME==] : Your Claim for (#==LISTING_TITLE==) Received.', 'directorist-claim-listing'),
            ];
            $claim_fields['email_tmpl_new_claim']  = [
                'type'           => 'textarea',
                'label'          => __('Email Body', 'directorist-claim-listing'),
                'description'    => __('Edit the email template for sending to the user when a claim is submitted.', 'directorist-claim-listing'),
                'value'          => sprintf(__("
                Dear ==NAME==,

                Thank you very much for your claim.
                This email is to notify you that your claim for (#==LISTING_TITLE==) has been received.

                %s

                Please wait for the confirmation by administrator.
                Listing Details Page: ==LISTING_URL==


                Thanks,
                The Administrator of ==SITE_NAME==
                ", 'directorist-claim-listing'), '')
            ];
            $claim_fields['email_sub_approved_claim']  = [
                'type'           => 'text',
                'label'          => __('Email Subject', 'directorist-claim-listing'),
                'description'    => __('Edit the subject of approved claim notification for user.', 'directorist-claim-listing'),
                'value'          => __('[==SITE_NAME==] : Claim Confirmation for Claim for (#==LISTING_TITLE==)', 'directorist-claim-listing'),
            ];
            $claim_fields['email_tmpl_approved_claim']  = [
                'type'           => 'textarea',
                'label'          => __('Email Body', 'directorist-claim-listing'),
                'description'    => __('Edit the email template of approved claim notification for user. HTML content is allowed too.', 'directorist-claim-listing'),
                'value'          => __("
                Dear ==NAME==,
                Congratulations! Your Claim for '==LISTING_TITLE==' has been confirmed. You can now edit it from your Dashboard ==LISTING_URL==

                Thanks,
                The Administrator of ==SITE_NAME==
                ", 'directorist-claim-listing')
            ];
            $claim_fields['email_sub_declined_claim']  = [
                'type'           => 'text',
                'label'          => __('Email Subject', 'directorist-claim-listing'),
                'description'    => __('Edit the subject of declined claim notification for user.', 'directorist-claim-listing'),
                'value'          => __('[==SITE_NAME==] : Claim Confirmation for Claim for (#==LISTING_TITLE==)', 'directorist-claim-listing'),
            ];
            $claim_fields['email_tmpl_declined_claim']  = [
                'type'           => 'textarea',
                'label'          => __('Email Body', 'directorist-claim-listing'),
                'description' => __('Edit the email template of declined claim notification for user. HTML content is allowed too.', 'directorist-claim-listing'),
                'value'          => __("
                Dear ==NAME==,
                Your Claim for '==LISTING_TITLE==' has been declined. Please contact with administrator.

                Thanks,
                The Administrator of ==SITE_NAME==
                ", 'directorist-claim-listing')
            ];

            if( directorist_is_claimable_with_plan() ) {
                $plan_options =  [
                    'value' => 'pricing_plan',
                    'label' => __('Pricing Plans', 'directorist-claim-listing'),
                ];
                array_push( $claim_fields['claim_charge_by']['options'], $plan_options );
            }
            return $claim_fields;
        }

        public function atbdp_extension_fields(  $fields ) {
            $fields[] = ['enable_claim_listing'];
            return $fields;
        }

        public function atbdp_extension_settings_submenus( $submenu ) {
            $submenu['claim_submenu'] = [
                'label' => __('Claim Listing', 'directorist-booking'),
                        'icon' => '<i class="fa fa-check"></i>',
                        'sections' => apply_filters( 'atbdp_claim_settings_controls', [
                            'claim_section' => [
                                'title'       => __('Claim Settings', 'directorist-claim-listing'),
                                'description' => __('You can Customize all the settings of Claim Listing Extension here', 'directorist-claim-listing'),
                                'fields'      =>  [ 'claim_widget_title', 'claim_widget_description', 'claim_charge_by', 'claim_listing_price', 'claim_now', 'non_widger_claim_button', 'verified_badge', 'verified_text' ],
                            ],
                        ] ),
            ];

            return $submenu;
        }

        // create_shortcodes
        public function create_shortcodes() {
            $shortcodes = [
                'directorist_claim_listing' => 'claim_listing_shortcode',
            ];

            foreach ( $shortcodes as $shortcode => $callback ) {
                if ( method_exists( $this, $callback ) ) {
                    add_shortcode( $shortcode, [ $this, $callback ] );
                }
            }
        }

        // claim_listing_shortcode
        public function claim_listing_shortcode() {
            ob_start();
            if (is_singular(ATBDP_POST_TYPE)) {
                global $post;
                $listing_id = $post->ID;

                do_action( 'atbdp_claim_listing',  $listing_id);
            }
            return ob_get_clean();
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
        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'directorist-claim-listing'), '1.0');
        }

        /**
         * Disable unserializing of the class.
         *
         * @return void
         * @since 1.0
         * @access protected
         */
        public function __wakeup()
        {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'directorist-claim-listing'), '1.0');
        }  

        /**
         * @since 1.0.5
         */
        public function dcl_listing_claim_now_button()
        {

            if (!get_directorist_option('non_widger_claim_button', 0)) return;
            $listing_id = get_the_ID();
            if (!get_directorist_option('enable_claim_listing', 1)) return; // vail if the business hour is not enabled
            $claim_header = get_directorist_option('claim_widget_title', esc_html__('Is this your business?', 'directorist-claim-listing'));
            $claim_description = get_directorist_option('claim_widget_description', esc_html__('Claim listing is the best way to manage and protect your business.', 'directorist-claim-listing'));
            $claim_now = get_directorist_option('claim_now', esc_html__('Claim Now!', 'directorist-claim-listing'));
            $claimed_by_admin = get_post_meta($listing_id, '_claimed_by_admin', true);
            $claim_fee = get_post_meta($listing_id, '_claim_fee', true);
            if ($claimed_by_admin || ('claim_approved' === $claim_fee)) return;
            ?>
            <div class="directorist-claim-listing-wrapper">
                <?php if (is_user_logged_in()) { ?>
                    <div class="directorist-card directorist-claim-listing">
                        <div class="directorist-card__header">
                            <h4 class="directorist-card__header--title">
                            <span class="<?php atbdp_icon_type(true); ?>-edit"></span><?php _e(' Claim', 'directorist-claim-listing') ?></h4>
                        </div>
                        <div class="directorist-card__body">
                            <h4 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing') ?></h4>
                            <p class="directorist-claim-listing__description"><?php _e("$claim_description", 'directorist-claim-listing') ?></p>
                            <a href="#" class=" directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js" data-directorist_target="directorist-claim-listing-modal"><?php _e("$claim_now", 'directorist-claim-listing') ?></a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="directorist-card directorist-claim-listing">
                        <div class="directorist-card__header">
                            <h4 class="directorist-card__header--title">
                                <span class="<?php atbdp_icon_type(true); ?>-edit"></span><?php _e(" $claim_now", 'directorist-claim-listing') ?>
                            </h4>
                        </div>
                        <div class="directorist-card__body">
                            <h4 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing') ?></h4>
                            <p class="directorist-claim-listing__description"><?php _e("$claim_description", 'directorist-claim-listing') ?></p>
                            <a href="#" class="directorist-claim-listing__login-alert directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js"><?php _e("$claim_now", 'directorist-claim-listing') ?></a>
                            <div class="directorist-claim-listing__login-notice directorist_notice directorist-alert directorist-alert-info" role="alert">
                                <span class="fa fa-info-circle" aria-hidden="true"></span>
                                <?php
                                // get the custom registration page id from the db and create a permalink
                                $reg_link_custom = ATBDP_Permalink::get_registration_page_link();
                                //if we have custom registration page, use it, else use the default registration url.
                                $reg_link = !empty($reg_link_custom) ? $reg_link_custom : wp_registration_url();

                                $login_url = '<a href="' . ATBDP_Permalink::get_login_page_link() . '">' . __('Login', 'directorist-claim-listing') . '</a>';
                                $register_url = '<a href="' . esc_url($reg_link) . '">' . __('Register', 'directorist-claim-listing') . '</a>';

                                printf(__('You need to %s or %s to claim this listing', 'directorist-claim-listing'), $login_url, $register_url);
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <input type="hidden" id="directorist__post-id" value="<?php echo get_the_ID(); ?>"/>
            </div>
            <div class="directorist-modal directorist-modal-js directorist-fade directorist-claim-listing-modal directorist-claimer">
                <div class="directorist-modal__dialog directorist-modal__dialog-lg">
                    <div class="directorist-modal__content">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <form id="directorist-claimer__form" class="directorist-claimer__form">
                                    <div class="directorist-modal__header">
                                        <h3 class="directorist-modal-title"
                                            id="directorist-claimer__claim-label"><?php _e('Claim This Listing', 'directorist-claim-listing'); ?>
                                        </h3>
                                        <a href="#" class="directorist-modal-close directorist-modal-close-js"><span aria-hidden="true">&times;</span></a>
                                    </div>
                                    <div class="directorist-modal__body">
                                        <div class="directorist-formgroup">
                                            <label for="directorist-claimer__name" class="directorist-claimer__name"><?php _e('Full Name', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <input type="text" class="directorist-form-element" id="directorist-claimer__name"
                                                   placeholder="<?php _e('Full Name', 'directorist-claim-listing'); ?>"
                                                   required>
                                        </div>
                                        <div class="directorist-form-group">
                                            <label for="directorist-claimer__phone" class="directorist-claimer__phone"><?php _e('Phone', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <input type="tel" class="directorist-form-element" id="directorist-claimer__phone"
                                                   placeholder="<?php _e('111-111-235', 'directorist-claim-listing'); ?>"
                                                   required>
                                        </div>
                                        <div class="directorist-form-group">
                                            <label for="directorist-claimer__details" class="directorist-claimer__details"><?php _e('Verification Details', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <textarea class="directorist-form-element" id="directorist-claimer__details"
                                                      rows="3"
                                                      placeholder="<?php _e('Details description about your business', 'directorist-claim-listing'); ?>..."
                                                      required></textarea>
                                        </div>
                                        <div class="directorist-form-group directorist-pricing-plan">
                                            <?php
                                            $claim_charge_by = get_directorist_option('claim_charge_by');
                                            $charged_by = get_post_meta($listing_id, '_claim_fee', true);
                                            $directory_type = get_post_meta($listing_id, '_directory_type', true);
                                            $charged_by = ($charged_by !== '') ? $charged_by : $claim_charge_by;
                                            $has_plans = is_pricing_plans_active();
                                            if (!empty($has_plans) && ('pricing_plan' === $charged_by)) {
                                                if (class_exists('ATBDP_Pricing_Plans')) {
                                                    $args = array(
                                                        'post_type' => 'atbdp_pricing_plans',
                                                        'posts_per_page' => -1,
                                                        'status' => 'publish',
                                                    );

                                                    $metas = [];
                                                    $metas['exclude'] = [
                                                        'relation' => 'OR',
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'compare'   => 'NOT EXISTS',
                                                            ),
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'value'     => 1,
                                                                'compare'   => '!=',
                                                            ),
                                                        ];
                                                    
                                                    if ( ! empty( $directory_type ) ) {
                                                        $metas['directory'] = [
                                                        'key'       => '_assign_to_directory',
                                                        'value'     => $directory_type,
                                                        'compare'   => '=',
                                                        ];
                                                    }

                                                    $args['meta_query'] = array_merge( array('relation' => 'AND'), $metas );

                                                    $atbdp_query = new WP_Query($args);

                                                    if ($atbdp_query->have_posts()) {
                                                        global $post;

                                                        $plans = $atbdp_query->posts;
                                                        printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                                        printf('<select name="claimer_plan" id="directorist-claimer_plan">');
                                                        printf('<option>%s</option>', __('Select Plan', 'directorist-claim-listing'));
                                                        foreach ($plans as $key => $value) {
                                                            $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                                                            $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                                            printf('<option %s value="%s">%s %s</option>', (!empty($active_plan) && ('package' === $plan_type)) ? 'class="directorist__active-plan"' : '', $value->ID, $value->post_title, !empty($active_plan) && ('package' === $plan_type) ? '<span class="atbd_badge">' . __('- Active', 'directorist-claim-listing') . '</span>' : '');
                                                        }
                                                        printf('</select>');

                                                        ?>
                                                        <div id="directorist__plan-allowances"
                                                             data-author_id="<?php echo get_current_user_id(); ?>">
                                                        </div>
                                                        <?php

                                                        printf('<a target="_blank" href="%s" class="directorist__plans">%s</a>', esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __('Show plan details', 'directorist-claim-listing'));
                                                    }
                                                } else {
                                                    global $product;
                                                    $query_args = array(
                                                        'post_type' => 'product',
                                                        'tax_query' => array(
                                                            array(
                                                                'taxonomy' => 'product_type',
                                                                'field' => 'slug',
                                                                'terms' => 'listing_pricing_plans',
                                                            ),
                                                        ),
                                                    );

                                                    $metas = [];
                                                    $metas['exclude'] = [
                                                        'relation' => 'OR',
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'compare'   => 'NOT EXISTS',
                                                            ),
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'value'     => 1,
                                                                'compare'   => '!=',
                                                            ),
                                                        ];
                                                    
                                                    if ( ! empty( $directory_type ) ) {
                                                        $metas['directory'] = [
                                                        'key'       => '_assign_to_directory',
                                                        'value'     => $directory_type,
                                                        'compare'   => '=',
                                                        ];
                                                    }

                                                    $query_args['meta_query'] = array_merge( array('relation' => 'AND'), $metas );


                                                    $atbdp_query = new WP_Query($query_args);

                                                    if ($atbdp_query->have_posts()) {
                                                        global $post;
                                                        $plans = $atbdp_query->posts;
                                                        printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                                        printf('<select name="claimer_plan" id="directorist-claimer_plan">');
                                                        printf('<option>%s</option>', __('Select Plan', 'directorist-claim-listing'));
                                                        foreach ($plans as $key => $value) {
                                                            $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                                                            $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                                            printf('<option %s value="%s">%s %s</option>', (!empty($active_plan) && ('package' === $plan_type)) ? 'class="directorist__active-plan"' : '', $value->ID, $value->post_title, !empty($active_plan) && ('package' === $plan_type) ? '<span class="atbd_badge">' . __('- Active', 'directorist-claim-listing') . '</span>' : '');
                                                        }
                                                        printf('</select>');
                                                        ?>
                                                        <div id="directorist__plan-allowances"
                                                             data-author_id="<?php echo get_current_user_id(); ?>">
                                                        </div>
                                                        <?php
                                                        printf('<a target="_blank" href="%s">%s</a>', esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __(' Show plan details', 'directorist-claim-listing'));
                                                    }
                                                }

                                            }
                                            ?>

                                        </div>
                                        <div id="directorist-claimer__submit-notification"></div>
                                        <div id="directorist-claimer__warning-notification"></div>
                                    </div>
                                    <div class="directorist-modal__footer">
                                        <button type="submit"
                                                class="btn btn-primary"><?php esc_html_e('Submit', 'directorist-claim-listing'); ?></button>
                                        <span><i class="<?php atbdp_icon_type(true); ?>-lock"></i><?php esc_html_e('Secure Claim Process', 'directorist-claim-listing'); ?></span>
                                    </div>
                                </form>
                            </div><!-- ends: .col-lg-125 -->
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


        /**
         * @since 1.2
         */
        public function dcl_plan_allowances()
        {
            $data       = [];
            $plan_id    = isset($_POST['plan_id']) ? $_POST['plan_id'] : '';
            $post_id    = isset($_POST['post_id']) ? $_POST['post_id'] : '';
            $order_id   = get_post_meta($post_id, '_plan_order_id', true);
            $order_plan_id = get_post_meta( $order_id, '_fm_plans', true );
            $active_orders = directorist_active_orders( $plan_id );
            $order_id   = ( $order_plan_id != $plan_id ) ? $active_orders[0] : $order_id;

            // wp_send_json([
            //     'plan_id' => $plan_id,
            //     'post_id' => $post_id,
            //     'order_id' => $order_id,
            // ]);

            if( 'package' !== package_or_PPL( $plan_id ) ){
                wp_send_json( $data );
            }

            $remaining  = plans_remaining( $plan_id, $order_id );
            $featured   = $remaining['featured'];
            $regular    = $remaining['regular'];

            if( count( $active_orders ) > 1 ){

                foreach( $active_orders as $order ){
                    $valid_order = directorist_valid_order( $order, $plan_id );
                    if( ! $valid_order ){

                        $active_orders = array_diff($active_orders, [$order]);
                    }
                }
            }


            if( count( $active_orders ) > 1 ){ ?>

                <div class="dpp-order-select-wrapper">
                    <form action="">
                        <div class="directorist-form-group">
                            <div class="directorist-form-label"><span><?php echo __( 'Active Orders', 'directorist-claim-listing' )?></span></div>

                            <div class="directorist-dropdown dpp-order-select-dropdown" data-label="<?php esc_attr_e( 'Select active order', 'directorist-claim-listing'); ?>" data-general_label="<?php esc_attr_e( 'General', 'directorist-claim-listing');  ?>" data-featured_label="<?php esc_attr_e( 'Featured', 'directorist-claim-listing'); ?>">
                                <a href="" class="directorist-dropdown-toggle"><span class="directorist-dropdown-toggle__text"><?php echo __( 'Select an order', 'directorist-claim-listing' )?></span></a>
                                <div class="directorist-dropdown-option">
                                    <ul>
                                        <?php
                                        foreach( $active_orders as $order ){
                                            $plan_id = get_post_meta( $order, '_fm_plan_ordered'. true );
                                            ?>
                                            <li><a href="" data-value="<?php echo $order; ?>"><?php echo '#' . esc_attr( $order ); ?></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>


                        </div>
                    </form>

                </div>


            <?php }else{
            ob_start();
            ?>
            <div class="directorist-listing-type">
                <?php $listing_type = !empty($listing_info['listing_type']) ? $listing_info['listing_type'] : ''; ?>

                <h4 class="directorist-option-title"><?php _e('Choose Listing Type', 'directorist-claim-listing') ?></h4>
                <div class="directorist-listing-type_list">
                    <?php
                    if ( 'Unlimited' === $regular ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-claim-listing') ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-claim-listing') ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo $featured == 0 ? 'disabled' : '' ?> <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-claim-listing') ?>
                                <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $regular . __('Remaining', 'directorist-claim-listing') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    if ( 'Unlimited' === $featured ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" <?php echo ($listing_type == 'featured') ? 'checked' : ''; ?> name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php _e(' Featured listing', 'directorist-claim-listing') ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-claim-listing') ?></span>
                            </label>
                        </div>
                    <?php
                    } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" <?php echo $featured == 0 ? 'disabled' : '' ?> <?php echo ($listing_type == 'featured') ? 'checked' : ''; ?> class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php _e(' Featured listing', 'directorist-claim-listing') ?>
                                <span class="<?php echo $featured > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $featured . __('Remaining', 'directorist-claim-listing') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
            }
            $data['html'] = ob_get_clean();

            wp_send_json( $data );
        }

        /**
         * @since 1.0.0
         *
         */
        public function dcl_submit_claim()
        {

            if( ! directorist_claim_verify_nonce() ) {
                wp_send_json( [ 'error_msg' => __('Sorry! Invalid nonce', 'directorist-claim-listing') ] );
            }

            $data               = [ 'error' => false ];
            $post_id            = ! empty( $_POST["post_id"] ) ? (int)$_POST["post_id"] : '';
            $plan_id            = ! empty( $_POST["plan_id"] ) ? (int)($_POST["plan_id"]) : '';
            $order_id           = ! empty( $_POST["order_id"] ) ? (int)($_POST["order_id"]) : '';
            $listing_type       = ! empty( $_POST["type"] ) ? sanitize_text_field( $_POST["type"] ) : '';
            $url                = ATBDP_Permalink::get_checkout_page_link($post_id);
            $already_claimed    = dcl_tract_duplicate_claim(get_current_user_id(), $post_id);


            if (!empty($already_claimed)) {
                $data['error'] = true;
                wp_send_json( [ 'error_msg' => __('Sorry! You have already requested for claim.', 'directorist-claim-listing') ] );
            } 

            if ( is_fee_manager_active() && dcl_need_to_charge_without_plan() ) {

                
                $response = directorist_validate_plan( $plan_id, $post_id, $order_id, $listing_type );
    
                // wp_send_json([
                //     'plan_id'       => $plan_id,
                //     'post_id'       => $post_id,
                //     'order_id'      => $order_id,
                //     'listing_type'  => $listing_type,
                //     'response'      => $response,
                // ]);

                if( !empty( $response['error'] ) ){
                    $data['error'] = true;
                    $data['error_msg'] = $response['error_msg'];
                    wp_send_json( $data );

                }

                if( !empty( $response['need_payment'] ) ){
                    update_post_meta( $post_id, '_claimed_by_admin', '' );
                    $data['checkout_url'] = add_query_arg( 'plan', $plan_id, $response['redirect_url'] );
                    $data['take_payment'] = true;
                }else{
                    do_action('atbdp_plan_assigned', $post_id);
                    $data['message'] = __( 'Your claim submitted successfully.', 'directorist-claim-listing' );
                }

                $this->directorist_do_claim( $post_id );
                wp_send_json($data);


            } else {
                
                if( non_paid_claim() ) {
                    $data['take_payment'] = false;
                    $data['message'] = __( 'Your claim submitted successfully.', 'directorist-claim-listing' );
                    $this->directorist_do_claim( $post_id );
                    wp_send_json($data);

                }else{
                    $data['checkout_url'] = $url;
                    $data['take_payment'] = true;
                    $this->directorist_do_claim( $post_id );
                    wp_send_json($data);

                }

            }
        }

        public function directorist_do_claim( $post_id ) {
            dcl_current_user();
            dcl_email_admin_listing_claim();
            dcl_new_claim( $post_id );
        }

        /**
         * @since 1.0.0
         */
        public function register_widget()
        {
            register_widget('DCL_Claim_Now');
        }

        /**
         * @since 1.0.0
         */
        public function verified_bedge_in_single_listing($listing_id)
        {
            if (!get_directorist_option('enable_claim_listing', 1)) return; // vail if the business hour is not enabled
            if (!get_directorist_option('verified_badge', 1)) return; // vail if the business hour is not enabled
            $verified_text = get_directorist_option('verified_text', esc_html__('Claimed', 'directorist-claim-listing'));
            $claimed_by_admin = get_post_meta($listing_id, '_claimed_by_admin', true);
            $class = directorist_legacy_mode() ? 'directorist-claimed atbdp_info_list' : 'directorist-claimed directorist-info-item';
            if (!empty($claimed_by_admin)) {
                printf('<div class="%s"><div class="directorist-claimed--badge"><span><i class="' . atbdp_icon_type() . '-check"></i></span> %s</div> <span class="directorist-claimed--tooltip">%s</span></div>', $class, $verified_text, __('Verified by it\'s Owner', 'directorist-claim-listing'));
            }
        }

        /**
         * @param $post
         * @param $post_id
         * @return $post_id
         * @since 1.0.0
         */
        public function dcl_save_metabox($post_id, $post)
        {

            if (!isset($_POST['post_type'])) {
                return $post_id;
            }

// If this is an autosave, our form has not been submitted, so we don't want to do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

// Check the logged in user has permission to edit this post
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
            if (isset($_POST['dcl_listing_claim_details_nonce'])) {

                // Verify that the nonce is valid
                if (wp_verify_nonce($_POST['dcl_listing_claim_details_nonce'], 'dcl_save_listing_claim_details')) {
                    $claim_fee = isset($_POST['claim_fee']) ? esc_attr($_POST['claim_fee']) : '';
                    $claimed_by_admin = isset($_POST['claimed_by_admin']) ? esc_attr($_POST['claimed_by_admin']) : '';
                    $claim_charge = isset($_POST['claim_charge']) ? (int)$_POST['claim_charge'] : '';
                    update_post_meta($post_id, '_claim_fee', $claim_fee);
                    update_post_meta($post_id, '_claim_charge', $claim_charge);
                    update_post_meta($post_id, '_claimed_by_admin', $claimed_by_admin);
                }
            }

        }

        public function register_necessary_scripts_front()
        {
            wp_enqueue_script('dcl-admin-script', DCL_ASSETS . '/js/main.js', array('jquery'), true);
            wp_enqueue_style('dcl_main_css', DCL_ASSETS . 'css/main.css', false, DCL_VERSION);
            $data = array(
                'ajaxurl'           => admin_url('admin-ajax.php'),
            );
            wp_localize_script( 'dcl-admin-script', 'dcl_admin', $data );
        }

        /*@todo later need to update the receipt content with the purchased packages dynamically e.g. remove Gold package*/
        /**
         * Add data to the customer receipt after completing an order.
         *
         * @param array $receipt_data An array of selected package.
         * @param integer $order_id Order ID.
         * @param integer $listing_id Listing ID.
         * @return     array      $receipt_data               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */
        public function dcl_payment_receipt_data($receipt_data, $order_id, $listing_id)
        {
            $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
            $admin_calim_charge = get_directorist_option('claim_charge_by');
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            if (class_exists('ATBDP_Pricing_Plans')) {
                return array();
            } else {
                if (('static_fee' === $charge_by)) {
                    $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
                    if ($claim_fee !== 'static_fee') return array();
                    $p_title = get_the_title($listing_id[0]);
                    $fm_price = get_post_meta($listing_id[0], '_claim_charge', true);
                    $admin_common_price = get_directorist_option('claim_listing_price');
                    $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                    $receipt_data = array(
                        'title' => $p_title,
                        'desc' => __('Claiming charge for this listing', 'directorist-claim-listing'),
                        'price' => $fm_price,
                    );
                    return $receipt_data;

                }
            }

        }

        /**
         * Add order details.
         *
         * @param array $order_details An array of containing order details.
         * @param integer $order_id Order ID.
         * @param integer $listing_id Listing ID.
         * @return     array      $order_details    Push additional package to the mail array.
         * @since     1.0.0
         * @access   public
         *
         */
        public function dcl_order_details($order_details, $order_id, $listing_id)
        {
            if ( isset($_POST['confirmed']) ) {
                return $order_details;
            }
            $claim_fee = get_post_meta($listing_id, '_claim_fee', true);
            $admin_calim_charge = get_directorist_option('claim_charge_by');
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            if (class_exists('ATBDP_Pricing_Plans')) {
                return array();
            } else {
                if (('static_fee' === $charge_by)) {
                    $p_title = get_the_title($listing_id);
                    $p_description = __('Claiming charge for this listing', 'directorist-claim-listing');
                    $fm_price = get_post_meta($listing_id, '_claim_charge', true);
                    $admin_common_price = get_directorist_option('claim_listing_price');
                    $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                    $order_details[] = array(
                        'active' => '1',
                        'label' => $p_title,
                        'desc' => $p_description,
                        'price' => $fm_price,
                        'show_ribbon' => '1',
                    );
                    return $order_details;
                }
            }


        }

        /**
         * Add data to the customer receipt after completing an order.
         *
         * @param array $order_items An array of selected package.
         * @param integer $listing_id Listing ID.
         * @return     array      $order_items               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */
        public function dcl_order_items($order_items = null, $order_id = null, $listing_id = null, $data = null)
        {
            if( class_exists('BD_Booking') ) {
                global $wpdb;
                $booking_data = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'directorist_booking` WHERE `order_id`=' . esc_sql($order_id), 'ARRAY_A');
                if(!empty($booking_data) && ($order_id == $booking_data['order_id'])) {
                    return $order_items;
                }
            }
            $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
            $admin_calim_charge = get_directorist_option('claim_charge_by');
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            if (class_exists('ATBDP_Pricing_Plans')) {
                return array();
            } else {
                if (('static_fee' === $charge_by)) {
                    $selected_plan_id = get_post_meta($listing_id[0], '_fm_plans', true);
                    $updated_plan_id = get_user_meta(get_current_user_id(), '_plan_to_active', true);
                    $plan_id = !empty($data['o_metas']['_fm_plan_ordered'][0]) ? $data['o_metas']['_fm_plan_ordered'][0] : '';
                    $_plan_ids = !empty($selected_plan_id) ? $selected_plan_id : $updated_plan_id;
                    $_plan_id = !empty($plan_id) ? $plan_id : $_plan_ids;
                    $p_title = get_the_title($listing_id[0]);
                    $p_description = get_post_meta($_plan_id, 'fm_description', true);
                    $fm_price = get_post_meta($listing_id[0], '_claim_charge', true);
                    $admin_common_price = get_directorist_option('claim_listing_price');
                    $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                    $order_items[] = array(
                        'title' => $p_title,
                        'desc' => $p_description,
                        'price' => $fm_price,
                    );
                    return $order_items;
                }
            }

        }

        /**
         * Add selected order to the checkout form.
         *
         * @param array $data An array of selected package.
         * @param integer $listing_id Listing ID.
         * @return     array      $data               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */

        public function dcl_checkout_form_data($data, $listing_id)
        {
            $claim_fee = get_post_meta($listing_id, '_claim_fee', true);
            $admin_calim_charge = get_directorist_option('claim_charge_by');
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            if (class_exists('ATBDP_Pricing_Plans')) {
                return array();
            } else {
                if (('static_fee' === $charge_by)) {
                    $selected_plan_id = get_post_meta($listing_id, '_fm_plans', true);
                    $claim_charge = get_post_meta($listing_id, '_claim_charge', true);
                    $admin_common_price = get_directorist_option('claim_listing_price');
                    $fm_price = !empty($claim_charge) ? $claim_charge : $admin_common_price;
                    $p_title = get_the_title($listing_id);
                    $data[] = array(
                        'type' => 'header',
                        'title' => __('Claim for ', 'directorist-claim-listing') . $p_title
                    );

                    $data[] = array(
                        'type' => 'checkbox',
                        'name' => $selected_plan_id,
                        'value' => 1,
                        'selected' => 1,
                        'title' => __('Claim for ', 'directorist-claim-listing') . $p_title,
                        'desc' => __('Claiming charge for this listing ', 'directorist-claim-listing'),
                        'price' => $fm_price
                    );
                    return $data;
                } else {
                    return array();
                }
            }

        }

        public function atpp_front_end_enqueue_scripts()
        {
            if (is_rtl()){
                wp_enqueue_style('dcl_main_css-rtl', DCL_ASSETS . 'css/main-rtl.css', false, DCL_VERSION);

            }else{
                wp_enqueue_style('dcl_main_css', DCL_ASSETS . 'css/main.css', false, DCL_VERSION);
            }
            wp_enqueue_style('atpp-bootstrap-style', DCL_ASSETS . 'css/atpp-bootstrap-grid.css', false, DCL_VERSION);

        }


        /**
         * Remove quick edit.
         *
         * @param array $actions An array of row action links.
         * @param WP_Post $post The post object.
         * @return     array      $actions    Updated array of row action links.
         * @since     1.0.0
         * @access   public
         *
         */
        public function atpp_remove_row_actions_for_quick_view($actions, $post)
        {

            global $current_screen;

            if ($current_screen->post_type != 'dcl_claim_listing') return $actions;

            unset($actions['view']);
            unset($actions['inline hide-if-no-js']);

            return $actions;

        }

        /**
         * Retrieve the table columns.
         *
         * @param array $column all the column
         * @param array $post_id post id
         * @since    1.0.0
         * @access   public
         */

        public function atpp_custom_field_column_content($column, $post_id)
        {
            echo '</select>';
            switch ($column) {
                case 'claim_for' :
                    $post_meta = get_post_meta($post_id);
                    $claimed_listing = isset($post_meta['_claimed_listing']) ? esc_attr($post_meta['_claimed_listing'][0]) : '';
                    echo __('Claimed for ') . get_the_title($claimed_listing);
                    break;

                case 'claimer' :
                    $post_meta = get_post_meta($post_id);
                    $current_author = isset($post_meta['_listing_claimer']) ? esc_attr($post_meta['_listing_claimer'][0]) : '';
                    $user = get_user_by('id', $current_author);
                    echo is_object($user) ? $user->display_name : '';
                    break;
                case 'status' :
                    $post_meta = get_post_meta($post_id);
                    $current_status = isset($post_meta['_claim_status']) ? esc_attr($post_meta['_claim_status'][0]) : '';
                    echo '<span class="atbdp-tick-cross2">' . ($current_status == 'approved' ? '<span style="color: #4caf50;">&#x2713;</span>' : '<span style="color: red;">&#x2717;</span>') . '</span>';
                    echo ucwords($current_status);
                    break;
                case 'details' :
                    $post_meta = get_post_meta($post_id);
                    $details = isset($post_meta['_claimer_details']) ? esc_textarea($post_meta['_claimer_details'][0]) : '';
                    echo $details;
                    break;

                case 'phone' :
                    $post_meta = get_post_meta($post_id);
                    echo !empty($post_meta['_claimer_phone'][0]) ? $post_meta['_claimer_phone'][0] : '';
                    break;
            }
        }


        /**
         * Retrieve the table columns.
         *
         * @param array $columns
         *
         * @return   array    $columns    Array of all the list table columns.
         * @since    1.0.0
         * @access   public
         */
        public function atpp_add_new_plan_columns($columns)
        {

            $columns = array(
                'cb' => '<input type="checkbox" />', // Render a checkbox instead of text
                'claim_for' => __('Title', 'directorist-claim-listing'),
                'claimer' => __('Author', 'directorist-claim-listing'),
                'status' => __('Status', 'directorist-claim-listing'),
                'details' => __('Claimer Details', 'directorist-claim-listing'),
                'phone' => __('Claimer Phone', 'directorist-claim-listing'),
                'date' => __('Date', 'directorist-claim-listing')

            );

            return $columns;

        }


        /**
         * Register meta boxes for Claim Listing.
         *
         * @since    1.0.0
         * @access   public
         */
        public function dcl_add_meta_boxes()
        {

            remove_meta_box('dcl-claim-details', 'dcl_claim_listing', 'normal');
            remove_meta_box('slugdiv', 'dcl_claim_listing', 'normal');


            add_meta_box('dcl-claim-details', __('Claim Details', 'directorist-claim-listing'), array($this, 'dcl_meta_box_plan_details'), 'dcl_claim_listing', 'normal', 'high');
        }

        public function dcl_meta_box_plan_details( $post )
        {

            // Add a nonce field so we can check for it later
            wp_nonce_field('dcl_save_claim_details', 'dcl_claim_details_nonce');
            /**
             * Display the "Field Details" meta box.
             */
            $this->load_template( 'admin-meta-fields', array( 'post' => $post ) );

        }


        /*
            * save data to database from the metaboxes
            */

        public function dcl_save_meta_data($post_id)
        {
            if (!isset($_POST['post_type'])) {
                return $post_id;
            }

// If this is an autosave, our form has not been submitted, so we don't want to do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

// Check the logged in user has permission to edit this post
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
            /*
             * save all the metadata to option table
             */
            // Check if "dcl_claim_details_nonce" nonce is set
            if (isset($_POST['dcl_claim_details_nonce'])) {
                // Verify that the nonce is valid
                if (wp_verify_nonce($_POST['dcl_claim_details_nonce'], 'dcl_save_claim_details')) {
                    require_once DCL_INC_DIR . 'class-db.php';
                }
            }

        }


        /**
         * Register a custom post type "dcl_claim_listing".
         *
         * @since    3.1.0
         * @access   public
         */
        public function register_claim_listing_post_type()
        {

            $labels = array(
                'name' => _x('Claim Listing', 'Post Type General Name', 'directorist-claim-listing'),
                'singular_name' => _x('Claim Listing', 'Post Type Singular Name', 'directorist-claim-listing'),
                'menu_name' => __('Claim Listing', 'directorist-claim-listing'),
                'name_admin_bar' => __('Claim', 'directorist-claim-listing'),
                'all_items' => __('Claim Listing', 'directorist-claim-listing'),
                'add_new_item' => __('Add New Claim', 'directorist-claim-listing'),
                'add_new' => __('Add New Claim', 'directorist-claim-listing'),
                'new_item' => __('New Claim', 'directorist-claim-listing'),
                'edit_item' => __('Edit Claim', 'directorist-claim-listing'),
                'update_item' => __('Update Claim', 'directorist-claim-listing'),
                'view_item' => __('View Claim', 'directorist-claim-listing'),
                'search_items' => __('Search Claim', 'directorist-claim-listing'),
                'not_found' => __('No Claim found', 'directorist-claim-listing'),
                'not_found_in_trash' => __('No Claim found in Trash', 'directorist-claim-listing'),
            );

            $args = array(
                'labels' => $labels,
                'description' => __('This order post type will keep track of admin fee plans', 'directorist-claim-listing'),
                'supports' => array('title'),
                'taxonomies' => array(''),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => current_user_can('manage_atbdp_options') ? true : false, // show the menu only to the admin
                'show_in_menu' => current_user_can('manage_atbdp_options') ? 'edit.php?post_type=' . ATBDP_POST_TYPE : false,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => true,
                'capabilities' => array(
                    'create_posts' => false,
                ),
                'exclude_from_search' => true,
                'publicly_queryable' => true,
                'capability_type' => 'at_biz_dir',
                'map_meta_cap' => true,
            );

            register_post_type('dcl_claim_listing', $args);

        }

        /**
         * @since 1.0.1
         */
        public function dcl_claim_confirmation_notification($events)
        {
            $claim_event2 = array(
                'value' => 'claim_confirmation',
                'label' => __('Claim Confirmation', 'directorist-claim-listing'),

            );

            // lets push our settings to the end of the other settings field and return it.
            array_push($events, $claim_event2);
            return $events;
        }


        /**
         * @since 1.0.2
         */
        public function dcl_default_events_to_notify_user($events)
        {
            $claim_event = 'claim_confirmation';
            // lets push our settings to the end of the other settings field and return it.
            array_push($events, $claim_event);
            return $events;
        }

        /**
         * @since 1.0.2
         */
        public function dcl_default_events_to_notify_admin($events)
        {
            $claim_event = 'new_claim_submitted';
            // lets push our settings to the end of the other settings field and return it.
            array_push($events, $claim_event);
            return $events;
        }

        /**
         * @since 1.0.1
         */
        public function dcl_claim_notification($events)
        {
            $claim_event = array(
                'value' => 'new_claim_submitted',
                'label' => __('New Claim Submitted', 'directorist-claim-listing'),

            );


            // lets push our settings to the end of the other settings field and return it.
            array_push($events, $claim_event);
            return $events;
        }


        public function claim_license_settings_controls($default)
        {
            $status = get_option('directorist_claim_license_status');
            if (!empty($status) && ($status !== false && $status == 'valid')) {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'claim_deactivated',
                    'label' => __('Action', 'directorist-claim-listing'),
                    'validation' => 'numeric',
                );
            } else {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'claim_activated',
                    'label' => __('Action', 'directorist-claim-listing'),
                    'validation' => 'numeric',
                );
            }
            $new = apply_filters('atbdp_claim_license_controls', array(
                'type' => 'section',
                'title' => __('Claim Listing', 'directorist-claim-listing'),
                'description' => __('You can active your Claim Listing extension here.', 'directorist-claim-listing'),
                'fields' => apply_filters('atbdp_claim_license_settings_field', array(
                    array(
                        'type' => 'textbox',
                        'name' => 'claim_license',
                        'label' => __('License', 'directorist-claim-listing'),
                        'description' => __('Enter your Claim Listing extension license', 'directorist-claim-listing'),
                        'default' => '',
                    ),
                    $action
                )),
            ));
            $settings = apply_filters('atbdp_licence_menu_for_claim_listing', true);
            if($settings){
                array_push($default, $new);
            }
            return $default;
        }

      /**
         * It  loads a template file from the Default template directory.
         * @param string $template Name of the file that should be loaded from the template directory.
         * @param array $args Additional arguments that should be passed to the template file for rendering dynamic  data.
         */
        public function load_template( $template, $args = array() )
        {
            dcl_get_template( $template,  $args );
        }

        /**
         * It register the text domain to the WordPress
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-claim-listing', false, DCL_LANG_DIR);
        }

        /**
         * It Includes and requires necessary files.
         *
         * @access private
         * @return void
         * @since 1.0
         */
        private function includes()
        {
            require_once DCL_INC_DIR . 'helper-functions.php';
            require_once DCL_INC_DIR . 'class-enqueuer.php';
            require_once DCL_INC_DIR . 'class-claim-now.php';

            include( dirname(__FILE__) . '/inc/directory_type.php' );
            new Claim_Post_Type_Manager();

        }


        /**
         * Setup plugin constants.
         *
         * @access private
         * @return void
         * @since 1.0
         */
        private function setup_constants()
        {
            if ( ! defined( 'DCL_FILE' ) ) { define( 'DCL_FILE', __FILE__ ); }

            require_once plugin_dir_path(__FILE__) . '/config-helper.php'; // loads constant from a file so that it can be available on all files.
            require_once plugin_dir_path(__FILE__) . '/config.php'; // loads constant from a file so that it can be available on all files.
        }
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

    /**
     * The main function for that returns DCL_Base
     *
     * The main function responsible for returning the one true DCL_Base
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     *
     * @return object|DCL_Base The one true DCL_Base Instance.
     * @since 1.0
     */
    function DCL_Base()
    {
        return DCL_Base::instance();
    }

    if (  directorist_is_plugin_active( 'directorist/directorist-base.php' ) ) {
        DCL_Base(); // get the plugin running
    }
}
