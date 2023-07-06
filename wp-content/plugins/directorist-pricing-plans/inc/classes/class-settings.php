<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.8
 * @package Directorist
 */
if (!class_exists('ATPP_Settings')) :

    class ATPP_Settings
    {
        public function __construct()
        {
            add_filter('atbdp_license_settings_controls', array($this, 'atpp_license_settings_controls'));
            add_filter('atbdp_dashboard_field_setting', array($this, 'atpp_settings_to_ext_general_fields'));

            add_filter( 'atbdp_listing_type_settings_field_list', array( $this, 'atbdp_listing_type_settings_field_list' ) );
            add_filter( 'atbdp_extension_fields', array( $this, 'atbdp_extension_fields' ) );
            add_filter( 'atbdp_pages_settings_fields', array( $this, 'atbdp_pages_settings_fields' ) );
            add_filter( 'atbdp_extension_settings_submenu', array( $this, 'atbdp_extension_settings_submenus' ) );
            add_filter( 'atbdp_monetization_settings_submenu', array( $this, 'atbdp_listing_settings_monetization_general_sections' ) );
            add_filter( 'atbdp_events_to_notify_admin', array( $this, 'atbdp_events_to_notify' ) );
            add_filter( 'atbdp_events_to_notify_user', array( $this, 'atbdp_events_to_notify' ) );
        }

        public function atbdp_events_to_notify( $events )
        {

            $new_events = $events;
            $active_gateways = get_directorist_option('active_gateways', [ 'bank_transfer' ]);

            if( ! in_array( 'bank_transfer', $active_gateways ) ){
                array_splice( $new_events, 0, 1 );
            }
            return $new_events;
        }
        public function atbdp_listing_settings_monetization_general_sections( $sections )
        {
            if( get_directorist_option( 'enable_featured_listing' ) ){

                $options = get_option('atbdp_option');
                
                $options['enable_featured_listing'] = '';
                update_option('atbdp_option', $options);
                
            }

            unset($sections['featured_listings']);
            return $sections;
        }

        public function atbdp_extension_fields(  $fields ) {
            $fields[] = ['fee_manager_enable'];
            return $fields;
        }

        public function atbdp_pages_settings_fields(  $fields ) {
            $fields[] = ['pricing_plans'];
            return $fields;
        }

        public function atbdp_listing_type_settings_field_list( $pricing_fields ) {
            $pricing_fields['fee_manager_enable'] = [
                'label'             => __('Pricing Plans', 'directorist-pricing-plans'),
                'type'              => 'toggle',
                'value'             => true,
                'description'       => __('You can disable it for users.', 'directorist-pricing-plans'),
            ];
            $pricing_fields['skip_plan_page'] = [
                'label'             => __('Skip Plan Page for Paid Users', 'directorist-pricing-plans'),
                'type'              => 'toggle',
                'value'             => false,
            ];
            $pricing_fields['tax_placeholder'] = [
                'type'              => 'text',
                'label'             => __('Tax Placeholder', 'directorist-pricing-plans'),
                'value'             => __('tax', 'directorist-pricing-plans'),
            ];
            $pricing_fields['plan_direct_purchase'] = [
                'type'              => 'toggle',
                'label'             => __('Direct Plan Purchase', 'directorist-pricing-plans'),
                'value'             => false,
            ];
            $pricing_fields['restrict_listing_deletion'] = [
                'type'              => 'toggle',
                'label'             => __('Restrict Listing Deletion', 'directorist-pricing-plans'),
                'description'       => __('User can\'t delete their listing if the listing is assigned with a plan.', 'directorist-pricing-plans'),
                'value'             => true,
            ];
            $pricing_fields['pricing_plans'] = [
                'label'             => __('Pricing Plans Page', 'directorist-pricing-plans'),
                'type'              => 'select',
                'description'       => sprintf(__('Following shortcode must be in the selected page %s', 'directorist-pricing-plans'), '<strong style="color: #ff4500;">[directorist_pricing_plans]</strong>'),
                'value'             => atbdp_get_option('pricing_plans', 'atbdp_general'),
                'showDefaultOption' => true,
                'options'           => $this->get_pages_vl_arrays(),
            ];

            return $pricing_fields;
        }

        public function atbdp_extension_settings_submenus( $submenu ) {
            $submenu['pricing_plan_submenu'] = [
                'label' => __('Pricing Plans', 'directorist-pricing-plans'),
                'icon' => '<i class="fas fa-money-check-alt"></i>',
                'sections' => apply_filters( 'atbdp_pricing_plan_settings_controls', [
                    'general_section' => [
                        'title' => __('Pricing Plans Settings', 'directorist-pricing-plans'),
                        'description' => __('You can Customize all the settings of Pricing Plans Extension here', 'directorist-pricing-plans'),
                        'fields'      =>  [ 'skip_plan_page', 'tax_placeholder', 'plan_direct_purchase', 'restrict_listing_deletion' ],
                    ],
                ] ),
            ];

            return $submenu;
        }


        /**
         * @since 1.4.2
         */
        public function atpp_settings_to_ext_general_fields($settings_submenus)
        {
            /*lets add a submenu of our extension*/

            $setting1 = array(
                'type' => 'toggle',
                'name' => 'user_active_package',
                'label' => __('Display Packages Tab', 'directorist-pricing-plans'),
                'default' => 1,
            );
            $setting2 = array(
                'type' => 'toggle',
                'name' => 'user_order_history',
                'label' => __('Display Order History Tab', 'directorist-pricing-plans'),
                'default' => 1,
            );
            /*  $setting3 = array(
                  'type' => 'toggle',
                  'name' => 'user_subscription',
                  'label' => __('Display Subscription Tab', 'directorist-pricing-plans'),
                  'default' => 1,
              );*/
            $setting4 = array(
                'type' => 'toggle',
                'name' => 'change_plan',
                'label' => __('Display Plan Change Link', 'directorist-pricing-plans'),
                'default' => 1,
            );

            array_push($settings_submenus, $setting1, $setting2, $setting4);
            return $settings_submenus;
        }


        public function atpp_license_settings_controls($default)
        {
            $status = get_option('directorist_pricing_license_status');
            if (!empty($status) && ($status !== false && $status == 'valid')) {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'pricing_deactivated',
                    'label' => __('Action', 'directorist-pricing-plans'),
                    'validation' => 'numeric',
                );
            } else {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'pricing_activated',
                    'label' => __('Action', 'directorist-pricing-plans'),
                    'validation' => 'numeric',
                );
            }
            $new = apply_filters('atbdp_pricing_license_controls', array(
                'type' => 'section',
                'title' => __('Pricing Plans', 'directorist-pricing-plans'),
                'description' => __('You can active your Pricing Plans extension here.', 'directorist-pricing-plans'),
                'fields' => apply_filters('atbdp_pricing_license_settings_field', array(
                    array(
                        'type' => 'textbox',
                        'name' => 'pricing_license',
                        'label' => __('License', 'directorist-pricing-plans'),
                        'description' => __('Enter your Pricing Plans extension license', 'directorist-pricing-plans'),
                        'default' => '',
                    ),
                    $action
                )),
            ));
            $settings = apply_filters('atbdp_licence_menu_for_pricing_plan', true);
            if($settings){
                array_push($default, $new);
            }
            return $default;
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

    }
endif;