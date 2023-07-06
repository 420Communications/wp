<?php
/**
 * Plugin Name: Directorist Google reCAPTCHA
 * Plugin URI: https://directorist.com/product/google-recaptcha
 * Description: This is an add-on for Directorist Plugin. You can add a feature to the directorist plugin to verify user spamming through Google reCAPTCHA .
 * Version: 1.3.1
 * Author: wpWax
 * Author URI: https://wjpwax.com
 * License: GPLv2 or later
 * Text Domain: directorist-google-recaptcha
 * Domain Path: /languages
 */

// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');
if (!class_exists('BD_Google_Recaptcha')){
    final class BD_Google_Recaptcha
    {


        /** Singleton *************************************************************/

        /**
         * @var BD_Google_Recaptcha The one true BD_Google_Recaptcha
         * @since 1.0
         */
        private static $instance;

        public $disable_for = [];

        /**
         * Main BD_Google_Recaptcha Instance.
         *
         * Insures that only one instance of BD_Google_Recaptcha exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @return object|BD_Google_Recaptcha The one true BD_Google_Recaptcha
         * @uses BD_Google_Recaptcha::setup_constants() Setup the constants needed.
         * @uses BD_Google_Recaptcha::includes() Include the required files.
         * @uses BD_Google_Recaptcha::load_textdomain() load the language files.
         * @see  BD_Google_Recaptcha()
         * @static
         * @static_var array $instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof BD_Google_Recaptcha)) {
                self::$instance = new BD_Google_Recaptcha;
                self::$instance->setup_constants();

                add_action('plugins_loaded', array(self::$instance, 'load_textdomain'));

                self::$instance->includes();

                // Add Settings fields to the extension general fields
                add_action('admin_enqueue_scripts', array(self::$instance, 'recaptch_load_needed_scripts'));
                // push license settings
                add_action('init', array(self::$instance, 'prepare_data') );
                //verify the submission
                add_action('atbdp_before_processing_submitted_listing_frontend', array(self::$instance, 'final_varification_add_listing'));
                add_action('atbdp_before_terms_and_conditions_font', array(self::$instance, 'atbdp_before_terms_and_conditions_font'));


                add_action('atbdp_before_processing_submitted_user_registration', array(self::$instance, 'varification_in_registration'));
                add_action('atbdp_before_user_registration_submit', array(self::$instance, 'show_recaptcha_in_registration_form'));


                // add_action('atbdp_before_processing_contact_to_owner', array(self::$instance, 'final_varification_form_inWidget'));
                // add_action('atbdp_before_submit_contact_form_inWidget', array(self::$instance, 'before_submit_contact_form_inWidget'));

                // license and auto update handler
                add_action('wp_ajax_atbdp_recaptcha_license_activation', array(self::$instance, 'atbdp_recaptcha_license_activation'));
                // license deactivation
                add_action('wp_ajax_atbdp_recaptcha_license_deactivation', array(self::$instance, 'atbdp_recaptcha_license_deactivation'));
                // settings 
                add_filter( 'atbdp_listing_type_settings_field_list', array( self::$instance, 'atbdp_listing_type_settings_field_list' ) );
                add_filter( 'atbdp_extension_fields', array( self::$instance, 'atbdp_extension_fields' ) );
                add_filter( 'atbdp_extension_settings_submenu', array( self::$instance, 'atbdp_extension_settings_submenus' ) );
                self::$instance->all_rules();
            }

            return self::$instance;
        }

        public function prepare_data(){
            $disable = get_directorist_option('disable_recaptcha_for', [] );
            $this->disable_for = $disable ? $disable : [];
        }

        public function atbdp_extension_fields(  $fields ) {
            $fields[] = ['google_recaptcha_enable'];
            return $fields;
        }
    
        public function atbdp_listing_type_settings_field_list( $google_recaptcha_fields ) {
            $google_recaptcha_fields['google_recaptcha_enable'] = [
                'label'             => __('Google reCAPTCHA', 'directorist-google-recaptcha'),
                'description'       => __('On refers to google reCAPTCHA is enable for frontend listing submission', 'directorist-google-recaptcha'),
                'type'              => 'toggle',
                'value'             => true,
            ];
            $google_recaptcha_fields['google_recaptcha_add_listing'] = [
                'label'             => __('reCAPTCHA in Listing Form', 'directorist-google-recaptcha'),
                'type'              => 'toggle',
                'value'             => true,
            ];
            $google_recaptcha_fields['google_recaptcha_registration'] = [
                'type'              => 'toggle',
                'label'             => __('reCAPTCHA in Registration Form', 'directorist-google-recaptcha'),
                'value'             => true
            ];
            $google_recaptcha_fields['google_recaptcha_settings'] = [
                'type'              => 'text',
                'label'             => __('Google reCAPTCHA Site Key', 'directorist-google-recaptcha'),
                'description'       => sprintf(__('You need to enter your google reCAPTCHA Site api key in order to display google reCAPTCHA. You can find your google reCAPTCHA Site api key and detailed information %s. or you can search in google', 'directorist-google-recaptcha'), '<a href="https://www.google.com/recaptcha/admin#list" target="_blank"> <strong style="color: red;">here</strong> </a>'),
                'value'             => '',
            ];
            $google_recaptcha_fields['google_recaptcha_settings_secret'] = [
                'type'              => 'text',
                'label'             => __('Google reCAPTCHA Secret Key', 'directorist-google-recaptcha'),
                'description' => __('You need to enter your google reCAPTCHA Secret key in order to display google reCAPTCHA.', 'directorist-google-recaptcha'),
                'value'             => '',
            ];
            $google_recaptcha_fields['disable_recaptcha_for'] = [
                'label'     => __('Disable reCAPTCHA for', 'directorist-google-recaptcha'),
                'type'      => 'checkbox',
                'value'     => [],
                'options'   => $this->all_rules(),
            ];
            $google_recaptcha_fields['google_recaptcha_title'] = [
                'type'              => 'text',
                'label'             => __('Google reCAPTCHA Level text', 'directorist-google-recaptcha'),
                'description'       => __('You need to enter level text to display before google reCAPTCHA.Leave blank to hide', 'directorist-google-recaptcha'),
                'value'             => '',
            ];
            $google_recaptcha_fields['google_recaptcha_theme'] = [
                'label'         => __('Google reCAPTCHA Theme', 'directorist-google-recaptcha'),
                'type'          => 'select',
                'value'         => 'light',
                'description'   => __('Select the theme you need.', 'directorist-google-recaptcha'),
                'options'       => [
                    [
                        'value' => 'light',
                        'label' => __('Light', 'directorist-google-recaptcha'),
                    ],
                    [
                        'value' => 'dark',
                        'label' => __('Dark', 'directorist-google-recaptcha'),
                    ],
                ],
            ];
            
            return $google_recaptcha_fields;
        }
    
        public function atbdp_extension_settings_submenus( $submenu ) {
            $submenu['google_recaptcha'] = [
                'label' => __('Google reCAPTCHA', 'directorist-google-recaptcha'),
                'icon'       => '<i class="fa fa-unlock"></i>',
                'sections'   => apply_filters( 'atbdp_google_recaptcha_settings_controls', [
                    'general_section' => [
                        'title'       => __('Google reCAPTCHA Settings', 'directorist-google-recaptcha'),
                        'description' => __('You can block boot or spamming', 'directorist-google-recaptcha'),
                        'fields'      =>  [ 'google_recaptcha_add_listing', 'google_recaptcha_registration', 'google_recaptcha_settings', 'google_recaptcha_settings_secret', 'disable_recaptcha_for', 'google_recaptcha_title', 'google_recaptcha_theme' ],
                    ],
                ] ),
            ];
    
            return $submenu;
        }

        public function atbdp_recaptcha_license_deactivation()
        {
            $license = !empty($_POST['recaptcha_license']) ? trim($_POST['recaptcha_license']) : '';
            $options = get_option('atbdp_option');
            $options['recaptcha_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_recaptcha_license', $license);
            $data = array();
            if (!empty($license)) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'deactivate_license',
                    'license' => $license,
                    'item_id' => ATBDP_RECAPTCHA_POST_ID, // The ID of the item in EDD
                    'url' => home_url()
                );
                // Call the custom API.
                $response = wp_remote_post(ATBDP_AUTHOR_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-google-recaptcha');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (!$license_data) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-google-recaptcha');
                        wp_send_json($data);
                        die();
                    }
                    update_option('directorist_recaptcha_license_status', $license_data->license);
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired' :
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-google-recaptcha'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked' :
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-google-recaptcha');
                                break;

                            case 'missing' :

                                $data['msg'] = __('Invalid license.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            case 'invalid' :
                            case 'site_inactive' :

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch' :

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-google-recaptcha'), 'Directorist - Image Gallery');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            default :
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License deactivated successfully!', 'directorist-google-recaptcha');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-google-recaptcha');
            }
            wp_send_json($data);
            die();
        }

        public function atbdp_recaptcha_license_activation()
        {
            $license = !empty($_POST['recaptcha_license']) ? trim($_POST['recaptcha_license']) : '';
            $options = get_option('atbdp_option');
            $options['recaptcha_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_recaptcha_license', $license);
            $data = array();
            if (!empty($license)) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license' => $license,
                    'item_id' => ATBDP_RECAPTCHA_POST_ID, // The ID of the item in EDD
                    'url' => home_url()
                );
                // Call the custom API.
                $response = wp_remote_post(ATBDP_AUTHOR_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-google-recaptcha');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (!$license_data) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-google-recaptcha');
                        wp_send_json($data);
                        die();
                    }
                    update_option('directorist_recaptcha_license_status', $license_data->license);
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired' :
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-google-recaptcha'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked' :
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-google-recaptcha');
                                break;

                            case 'missing' :

                                $data['msg'] = __('Invalid license.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            case 'invalid' :
                            case 'site_inactive' :

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch' :

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-google-recaptcha'), 'Directorist - Google reCAPTCHA');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;

                            default :
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-google-recaptcha');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License activated successfully!', 'directorist-google-recaptcha');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-google-recaptcha');
            }
            wp_send_json($data);
            die();
        }

        public function all_rules()
        {
            return apply_filters('dgrc_default_user_roles', array(
                array(
                    'value' => 'administrator',
                    'label' => __('Administrator', 'directorist-google-recaptcha'),
                ),
                array(
                    'value' => 'editor',
                    'label' => __('Editor', 'directorist-google-recaptcha'),
                ),
                array(
                    'value' => 'author',
                    'label' => __('Author', 'directorist-google-recaptcha'),
                ),
                array(
                    'value' => 'contributor',
                    'label' => __('Contributor', 'directorist-google-recaptcha'),
                ),
                array(
                    'value' => 'subscriber',
                    'label' => __('Subscriber', 'directorist-google-recaptcha'),
                )
            ));
        }

        public function final_varification_add_listing()
        {
            $error = [];
            if (get_directorist_option('google_recaptcha_enable', 1) == 1) {
                require_once plugin_dir_path(__FILE__) . 'recaptchalib.php';
                $secret_key = get_directorist_option('google_recaptcha_settings_secret');
                $is_configured = get_directorist_option('google_recaptcha_settings');
                if (!$is_configured) return;
                // your secret key
                $secret = $secret_key;

                // empty response
                $response = null;

                // check secret key
                $reCaptcha = new ReCaptcha($secret);
                // if submitted check response
                $current_uer_role = wp_get_current_user()->roles;
                $cur_role = '';
                if (is_user_logged_in()) {
                    $cur_role = $current_uer_role[0];
                }
                if ( !in_array( $cur_role, $this->disable_for ) ){
                    if ($_POST["g-recaptcha-response"]) {
                        
                        $response = $reCaptcha->verifyResponse(
                            $_SERVER["REMOTE_ADDR"],
                            $_POST["g-recaptcha-response"]
                        );

                        if( $response === null ){
                            $msg = __('Verification failed! Please try again', 'directorist-google-recaptcha');
                            array_push( $error, $msg );
                        }
                       
                    } else {
                        $msg = __('Verification failed! Please try again', 'directorist-google-recaptcha');
                        array_push( $error, $msg );
                    }
                    
                }
                if( $error ){
                    $data['error_msg'] = $error;
                    $data['error'] = true;
                    wp_send_json($data);
                }
               
            }
        }

        public function atbdp_before_terms_and_conditions_font( $args )
        {

            $site_key = get_directorist_option('google_recaptcha_settings');
            $title = get_directorist_option('google_recaptcha_title');
            $theme = get_directorist_option('google_recaptcha_theme');
            $recaptcha = get_directorist_option('google_recaptcha_enable', 1);

            $current_uer_role = wp_get_current_user()->roles;
            $cur_role = '';
            if (is_user_logged_in()) {
                $cur_role = $current_uer_role[0];
            }

            if ( $recaptcha ) {
                if (!$site_key) return false;
                if ($cur_role == 'administrator' && in_array('administrator', $this->disable_for)) return false;

                elseif ($cur_role == 'author' && in_array('author', $this->disable_for)) return false;
                elseif ($cur_role == 'editor' && in_array('editor', $this->disable_for)) return false;
                elseif ($cur_role == 'contributor' && in_array('contributor', $this->disable_for)) return false;
                elseif ($cur_role == 'subscriber' && in_array('subscriber', $this->disable_for)) return false;
                else {
                    echo "<style>
                        .directorist-g-recaptcha-wrap{
                        text-align: center;
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                            -webkit-box-orient: vertical;
                            -webkit-box-direction: normal;
                            -ms-flex-direction: column;
                                    flex-direction: column;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                                    align-items: center;
                            margin-bottom: 30px;
                        }
                        .directorist-g-recaptcha-wrap p{
                            font-size: 16px !important;
                            width: 100%;
                            text-align: center !important;
                            margin-bottom: 15px !important;
                            font-weight: 600;
                            color: #202428 !important;
                        }
                        .directorist-g-recaptcha-wrap .g-recaptcha{
                            width: 100%;
                        }
                        .directorist-g-recaptcha-wrap .g-recaptcha div{
                            margin: 0 auto;
                        }
                    </style>

                    <script src='https://www.google.com/recaptcha/api.js'></script>";
                    printf('<div class="directorist-g-recaptcha-wrap">
                                    <p>%s</p>
                                     <div data-theme="%s" class="g-recaptcha"  data-sitekey="%s"></div>
                               </div>', $title, $theme, $site_key);
                }


            }
        }

        /**
         * @since 2.0.0
         * It shows the recaptcha validation for widget
         */

        public function show_recaptcha_in_registration_form()
        {
            $site_key = get_directorist_option('google_recaptcha_settings');
            $title = get_directorist_option('google_recaptcha_title');
            $theme = get_directorist_option('google_recaptcha_theme');
            $current_uer_role = wp_get_current_user()->roles;
            $cur_role = '';
            if (is_user_logged_in()) {
                $cur_role = $current_uer_role[0];
            }

            if ((get_directorist_option('google_recaptcha_enable', 1) == 1) && (get_directorist_option('google_recaptcha_registration', 1) == 1)) {

                if ($cur_role == 'administrator' && in_array('administrator', $this->disable_for)) return false;

                elseif ($cur_role == 'author' && in_array('author', $this->disable_for)) return false;
                elseif ($cur_role == 'editor' && in_array('editor', $this->disable_for)) return false;
                elseif ($cur_role == 'contributor' && in_array('contributor', $this->disable_for)) return false;
                elseif ($cur_role == 'subscriber' && in_array('subscriber', $this->disable_for)) return false;
                else {
                    echo "<style>
                        .directorist-g-recaptcha-wrap{
                        text-align: center;
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                            -webkit-box-orient: vertical;
                            -webkit-box-direction: normal;
                            -ms-flex-direction: column;
                                    flex-direction: column;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                                    align-items: center;
                        }
                        .dgr_show_recaptcha{
                            margin-bottom: 0;
                            margin-top: 20px;
                        }
                        #directorist.atbd_wrapper .directory_regi_btn {
                            text-align: left;
                        }
                    </style>

                    <script src='https://www.google.com/recaptcha/api.js'></script>";
                    printf('<div class="dgr_show_recaptcha">
                                    <p>%s</p>
                                     <div data-theme="%s" class="g-recaptcha"  data-sitekey="%s"></div>
                               </div>', $title, $theme, $site_key);
                }


            }
        }

        public function varification_in_registration()
        {
            if ((get_directorist_option('google_recaptcha_enable', 1) == 1) && (get_directorist_option('google_recaptcha_registration', 1) == 1)) {
                require_once plugin_dir_path(__FILE__) . 'recaptchalib.php';
                $secret_key = get_directorist_option('google_recaptcha_settings_secret');

                // empty response
                $response = null;

                $error = '';

                // check secret key
                $reCaptcha = new ReCaptcha($secret_key);
                // if submitted check response
                $current_uer_role = wp_get_current_user()->roles;
                $cur_role = '';
                if (is_user_logged_in()) {
                    $cur_role = $current_uer_role[0];
                }
                if ( !in_array( $cur_role, $this->disable_for ) ){
                    if ($_POST["g-recaptcha-response"]) {
                        
                        $response = $reCaptcha->verifyResponse(
                            $_SERVER["REMOTE_ADDR"],
                            $_POST["g-recaptcha-response"]
                        );

                        if( $response === null ){
                            $error = __('Verification failed! Please try again', 'directorist-google-recaptcha');
                        }
                       
                    } else {
                        $error = __('Verification failed! Please try again', 'directorist-google-recaptcha');
                    }
                    
                }
                if( $error ){
                    $capcha_check_notice = '
                                    <style>
                                        .not_checked_notice {
                                            margin: 60px auto;
                                            width: 50%;
                                            padding: 17px 30px;
                                            background: #f5e3e3;
                                            border-radius: 6px;
                                            color: #333;
                                            text-align: center;

                                        }
                                    </style>
                                    <div class="not_checked_notice">
                                        <p><span style="font-weight: bold">Notice: </span>'. $error .'</p>
                                    </div>
                                    ';

                    die($capcha_check_notice);

                }

            }
        }

        /**
         * @since 2.0.0
         * It shows the recaptcha validation for widget
         */

        public function before_submit_contact_form_inWidget()
        {
            $site_key = get_directorist_option('google_recaptcha_settings');
            $title = get_directorist_option('google_recaptcha_title');
            $theme = get_directorist_option('google_recaptcha_theme');
            $current_uer_role = wp_get_current_user()->roles;
            $cur_role = '';
            if (is_user_logged_in()) {
                $cur_role = $current_uer_role[0];
            }

            if ((get_directorist_option('google_recaptcha_enable', 1) == 1) && (get_directorist_option('google_recaptcha_contact_owner', 1) == 1)) {

                if ($cur_role == 'administrator' && in_array('administrator', $this->disable_for)) return false;

                elseif ($cur_role == 'author' && in_array('author', $this->disable_for)) return false;
                elseif ($cur_role == 'editor' && in_array('editor', $this->disable_for)) return false;
                elseif ($cur_role == 'contributor' && in_array('contributor', $this->disable_for)) return false;
                elseif ($cur_role == 'subscriber' && in_array('subscriber', $this->disable_for)) return false;
                else {
                    echo "<style>
                        .directorist-g-recaptcha-wrap{
                        text-align: center;
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                            -webkit-box-orient: vertical;
                            -webkit-box-direction: normal;
                            -ms-flex-direction: column;
                                    flex-direction: column;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                                    align-items: center;
                            margin-bottom: 30px;
                        }
                    </style>

                    <script src='https://www.google.com/recaptcha/api.js'></script>";
                    printf('<div class="dgr_show_recaptcha">
                                    <p>%s</p>
                                     <div data-theme="%s" class="g-recaptcha"  data-sitekey="%s"></div>
                               </div>', $title, $theme, $site_key);
                }


            }
            return true;
        }

        public function final_varification_form_inWidget()
        {
            if ((get_directorist_option('google_recaptcha_enable', 1) == 1) && (get_directorist_option('google_recaptcha_contact_owner', 1) == 1)) {
                require_once plugin_dir_path(__FILE__) . 'recaptchalib.php';
                $secret_key = get_directorist_option('google_recaptcha_settings_secret');
                // your secret key
                $secret = $secret_key;

// empty response
                $response = null;

// check secret key
                $reCaptcha = new ReCaptcha($secret);

                $capcha_check_notice = '
<style>
    .not_checked_notice {
        margin: 60px auto;
        width: 50%;
        padding: 17px 30px;
        background: #f5e3e3;
        border-radius: 6px;
        color: #333;
        text-align: center;

    }
</style>
                    <div class="not_checked_notice">
                        <p><span style="font-weight: bold">Notice: </span>Something wrong with your submission, Please try again!</p>
                    </div>
            ';

// if submitted check response
                if ($_POST["g-recaptcha-response"]) {
                    $response = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["g-recaptcha-response"]
                    );


                }

                if ($response != null && $response->success) {

                } else {

                    die($capcha_check_notice);

                }

            }
        }


        public function recaptcha_license_settings_controls($default)
        {
            $status = get_option('directorist_recaptcha_license_status');
            if (!empty($status) && ($status !== false && $status == 'valid')) {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'recaptcha_deactivated',
                    'label' => __('Action', 'directorist-google-recaptcha'),
                    'validation' => 'numeric',
                );
            } else {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'recaptcha_activated',
                    'label' => __('Action', 'directorist-google-recaptcha'),
                    'validation' => 'numeric',
                );
            }
            $new = apply_filters('atbdp_recaptcha_extension_controls', array(
                'type' => 'section',
                'title' => __('Google reCAPTCHA', 'directorist-google-recaptcha'),
                'description' => __('You can active your Google reCAPTCHA extension here.', 'directorist-google-recaptcha'),
                'fields' => apply_filters('atbdp_recaptcha_license_settings_field', array(
                    array(
                        'type' => 'textbox',
                        'name' => 'recaptcha_license',
                        'label' => __('License', 'directorist-google-recaptcha'),
                        'description' => __('Enter your Google reCAPTCHA extension license', 'directorist-google-recaptcha'),
                        'default' => '',
                    ),
                    $action
                )),

            ));
            $settings = apply_filters('atbdp_licence_menu_for_recaptcha', true);
            if($settings){
                array_push($default, $new);
            }
            return $default;
        }

        public function recaptch_load_needed_scripts()
        {
            if (isset($_GET['page']) && ('aazztech_settings' === $_GET['page'])) {
                wp_enqueue_style('recaptcha_main_css', plugin_dir_url(__FILE__) . 'assets/css/main.css');
                wp_enqueue_script('recaptcha_main_js', plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'));
                wp_localize_script('recaptcha_main_js', 'recaptcha_js_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
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
         * @return void
         * @since 1.0
         * @access protected
         */
        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'directorist-google-recaptcha'), '1.0');
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
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'directorist-google-recaptcha'), '1.0');
        }


        /**
         * It register the text domain to the WordPress
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-google-recaptcha', false, DGR_LANG_DIR);
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
            require_once DGR_INC_DIR . 'helper-functions.php';
            include(dirname(__FILE__) . '/inc/directory_type.php');
            new Directorist_GR_Directory_Type_Manager();
            if (!class_exists('EDD_SL_Plugin_Updater')) {
                // load our custom updater if it doesn't already exist
                include(dirname(__FILE__) . '/inc/EDD_SL_Plugin_Updater.php');
            }
            // setup the updater
            $license_key = trim(get_option('directorist_recaptcha_license'));
            new EDD_SL_Plugin_Updater(ATBDP_AUTHOR_URL, __FILE__, array(
                'version' => DGR_VERSION,        // current version number
                'license' => $license_key,    // license key (used get_option above to retrieve from DB)
                'item_id' => ATBDP_RECAPTCHA_POST_ID,    // id of this plugin
                'author' => 'AazzTech',    // author of this plugin
                'url' => home_url(),
                'beta' => false // set to true if you wish customers to receive update notifications of beta releases
            ));
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
            require_once plugin_dir_path(__FILE__) . '/config.php'; // loads constant from a file so that it can be available on all files.
        }
    }

    /**
     * The main function for that returns BD_Google_Recaptcha
     *
     * The main function responsible for returning the one true BD_Google_Recaptcha
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     *
     * @return object|BD_Google_Recaptcha The one true BD_Google_Recaptcha Instance.
     * @since 1.0
     */
    function BD_Google_Recaptcha()
    {
        return BD_Google_Recaptcha::instance();
    }

// Instantiate Directorist Stripe gateway only if our directorist plugin is active
    if (in_array('directorist/directorist-base.php', (array)get_option('active_plugins'))) {
        BD_Google_Recaptcha(); // get the plugin running
    }
}
