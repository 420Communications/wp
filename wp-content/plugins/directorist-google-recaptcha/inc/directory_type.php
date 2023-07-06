<?php
/*
 * Class: Business Directory Multiple Image = ATPP
 * */
if (!class_exists('Directorist_GR_Directory_Type_Manager')) :
    class Directorist_GR_Directory_Type_Manager
    {
        public function __construct()
        {
            add_filter('atbdp_listing_type_settings_field_list', array($this, 'atbdp_listing_type_settings_field_list'));
            add_filter('atbdp_submission_form_settings', array($this, 'atbdp_submission_form_settings'));
            add_filter( 'atbdp_add_listing_submission_template_args', array( $this, 'atbdp_add_listing_submission_template_args' ), 10, 2 );
        }


        public function atbdp_add_listing_submission_template_args( $args ) {
            $type = $args['form']->get_current_listing_type();
            $recaptcha = get_directorist_type_option( $type, 'enable_recaptcha', 1 );
            if( '1' === $recaptcha ) {
                $args['recaptcha'] = true;
            }
            return $args;
        }

        public function atbdp_listing_type_settings_field_list( $fields ){

            $fields['enable_recaptcha'] = [
                'label' => __('Enable reCAPTCHA verification', 'directorist'),
                'type'  => 'toggle',
                'name'  => 'g-recaptcha-response',
                'value' => false,
                
            ];
            return $fields;
        }

        public function atbdp_submission_form_settings( $settings ) {

            $settings['google_recaptcha_setting'] = [
                'title' => __('Google Recaptcha', 'directorist'),
                'container' => 'short-width',
                'fields' => [
                    'enable_recaptcha',
                ],
            ];

            return $settings;
        }

    }
endif;