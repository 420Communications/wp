<?php
/**
 * Plugin Name: Directorist - Live Chat
 * Plugin URI: https://directorist.com/product/directorist-live-chat/
 * Description: This is an extension that allows the visitors to contact business owners immediately and easily.
 * Version: 1.3.4
 * Author: wpWax
 * Author URI: http://wpwax.com/
 * License: GPLv2 or later
 * Text Domain: directorist-live-chat
 * Domain Path: /languages
 */
// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');
if (!class_exists('Directorist_Live_Chat')) {
    final class Directorist_Live_Chat
    {

        /** Singleton *************************************************************/

        /**
         * @var Directorist_Live_Chat The one true Directorist_Live_Chat
         * @since 1.0
         */
        private static $instance;

        /**
         * Main Directorist_Live_Chat Instance.
         *
         * Insures that only one instance of Directorist_Live_Chat exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @return object|Directorist_Live_Chat The one true Directorist_Live_Chat
         * @uses Directorist_Live_Chat::setup_constants() Setup the constants needed.
         * @uses Directorist_Live_Chat::includes() Include the required files.
         * @uses Directorist_Live_Chat::load_textdomain() load the language files.
         * @see  Directorist_Live_Chat()
         * @since 1.0
         * @static
         * @static_var array $instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof Directorist_Live_Chat)) {
                self::$instance = new Directorist_Live_Chat;
                self::$instance->setup_constants();
                self::$instance->includes();
                add_action('wp_enqueue_scripts', array(self::$instance, 'load_needed_scripts'));
                add_action('admin_enqueue_scripts', array(self::$instance, 'load_admin_needed_scripts'));
                add_action('plugins_loaded', array(self::$instance, 'load_textdomain'));
                add_filter('atbdp_license_settings_controls', array(self::$instance, 'mas_license_settings_controls'));

                add_action('init', array(self::$instance, 'register_new_post_types'));
                add_action('directorist_dashboard_tabs', array( self::$instance, 'directorist_dashboard_tabs'));
                // new Admin_Chat;
                add_action('atbdp_after_single_listing', array(self::$instance, 'atbdp_after_single_listing'));
                add_shortcode('directorist_live_chat', array(self::$instance, 'atbdp_live_chat_single'));
                // handling ajax chat
                add_action('wp_ajax_atbdp_live_chat', array(self::$instance, 'atbdp_live_chat'));
                add_action('wp_ajax_nopriv_atbdp_live_chat', array(self::$instance, 'atbdp_live_chat'));

                // handling ajax chat history
                add_action('wp_ajax_atbdp_get_chat_history', array(self::$instance, 'atbdp_get_chat_history'));
                add_action('wp_ajax_nopriv_atbdp_get_chat_history', array(self::$instance, 'atbdp_get_chat_history'));

                // handling ajax chat history
                add_action('wp_ajax_atbdp_get_admin_chat_history', array(self::$instance, 'atbdp_get_admin_chat_history'));
                add_action('wp_ajax_nopriv_atbdp_get_admin_chat_history', array(self::$instance, 'atbdp_get_admin_chat_history'));

                // handling ajax chat delete
                // add_action('wp_ajax_atbdp_delete_chat_history', array(self::$instance, 'atbdp_delete_chat_history'));
                //add_action('wp_ajax_nopriv_atbdp_delete_chat_history', array(self::$instance, 'atbdp_delete_chat_history'));

                // license and auto update handler
                add_action('wp_ajax_atbdp_live_chat_license_activation', array(self::$instance, 'atbdp_live_chat_license_activation'));
                // license deactivation
                add_action('wp_ajax_atbdp_live_chat_license_deactivation', array(self::$instance, 'atbdp_live_chat_license_deactivation'));
                // settings
                add_filter( 'atbdp_listing_type_settings_field_list', array( self::$instance, 'atbdp_listing_type_settings_field_list' ) );
                add_filter( 'atbdp_extension_fields', array( self::$instance, 'atbdp_extension_fields' ) );
                add_filter( 'atbdp_extension_settings_submenu', array( self::$instance, 'atbdp_extension_settings_submenus' ) );
            }

            return self::$instance;
        }

        public function directorist_dashboard_tabs( $tabs ){
            //$enable_chat = get_directorist_option('enable_live_chat', 1);
            //if (empty($enable_chat)) return $tabs;
            $chat_user_tab = get_directorist_option('chat_user_tab', __('Chat', 'directorist-live-chat'));
            $tabs['live_chat'] = array(
				'title'     => $chat_user_tab,
				'content'   => $this->directorist_live_chat_content(),
				'icon'		=> atbdp_icon_type() . '-comment',
			);
            return $tabs;
        }

        public function atbdp_extension_fields(  $fields ) {
            $fields[] = ['enable_live_chat'];
            return $fields;
        }

        public function atbdp_listing_type_settings_field_list( $live_chat_fields ) {
            $live_chat_fields['enable_live_chat'] = [
                'label'             => __('Live Chat', 'directorist-live-chat'),
                'type'              => 'toggle',
                'value'             => true,
            ];
            $live_chat_fields['start_chat_button'] = [
                'type'              => 'text',
                'label'             => __('"Start Chatting" Button Text', 'directorist-live-chat'),
                'value'             => __('Start Chatting', 'directorist-live-chat'),
            ];
            $live_chat_fields['show_chat_button'] = [
                'type'              => 'text',
                'label'             => __('"Show Chat" Button Text', 'directorist-live-chat'),
                'value'             => __('Show Chat', 'directorist-live-chat'),
            ];
            $live_chat_fields['hide_chat_button'] = [
                'type'              => 'text',
                'label'             =>  __('"Hide Chat" Button Text', 'directorist-live-chat'),
                'value'             => __('Hide Chat', 'directorist-live-chat'),
            ];
            $live_chat_fields['chat_user_tab'] = [
                'type'              => 'text',
                'label'             => __('"Chat" Tab Label (User Dashboard)', 'directorist-live-chat'),
                'value'             => __('Chat', 'directorist-live-chat'),
            ];

            return $live_chat_fields;
        }

        public function atbdp_extension_settings_submenus( $submenu ) {
            $submenu['live_chat'] = [
                'label' => __('Live Chat', 'directorist-live-chat'),
                'icon'       => '<i class="fa fa-comment"></i>',
                'sections'   => apply_filters( 'atbdp_live_chat_settings_controls', [
                    'general_section' => [
                        'title'       => __('Live Chat Settings', 'directorist-live-chat'),
                        'description' => __('You can Customize all the settings of Live Chat Extension here', 'directorist-live-chat'),
                        'fields'      =>  [ 'start_chat_button', 'show_chat_button', 'hide_chat_button', 'chat_user_tab' ],
                    ],
                ] ),
            ];

            return $submenu;
        }

        public function atbdp_live_chat_license_deactivation()
        {
            $license = !empty($_POST['live_chat_license']) ? trim($_POST['live_chat_license']) : '';
            $options = get_option('atbdp_option');
            $options['live_chat_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_live_chat_license', $license);
            $data = array();
            if (!empty($license)) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'deactivate_license',
                    'license' => $license,
                    'item_id' => ATBDP_DLC_POST_ID, // The ID of the item in EDD
                    'url' => home_url(),
                );
                // Call the custom API.
                $response = wp_remote_post(ATBDP_AUTHOR_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-live-chat');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (!$license_data) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-live-chat');
                        wp_send_json($data);
                        die();
                    }
                    update_option('directorist_live_chat_license_status', $license_data->license);
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired':
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-live-chat'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked':
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-live-chat');
                                break;

                            case 'missing':

                                $data['msg'] = __('Invalid license.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            case 'invalid':
                            case 'site_inactive':

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch':

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-live-chat'), 'Directorist - Listings with Map');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            default:
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License deactivated successfully!', 'directorist-live-chat');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-live-chat');
            }
            wp_send_json($data);
            die();
        }

        public function atbdp_live_chat_license_activation()
        {
            $license = !empty($_POST['live_chat_license']) ? trim($_POST['live_chat_license']) : '';
            $options = get_option('atbdp_option');
            $options['live_chat_license'] = $license;
            update_option('atbdp_option', $options);
            update_option('directorist_live_chat_license', $license);
            $data = array();
            if (!empty($license)) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license' => $license,
                    'item_id' => ATBDP_DLC_POST_ID, // The ID of the item in EDD
                    'url' => home_url(),
                );
                // Call the custom API.
                $response = wp_remote_post(ATBDP_AUTHOR_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));
                // make sure the response came back okay
                if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

                    $data['msg'] = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.', 'directorist-live-chat');
                    $data['status'] = false;

                } else {

                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    if (!$license_data) {
                        $data['status'] = false;
                        $data['msg'] = __('Response not found!', 'directorist-live-chat');
                        wp_send_json($data);
                        die();
                    }
                    update_option('directorist_live_chat_license_status', $license_data->license);
                    if (false === $license_data->success) {
                        switch ($license_data->error) {
                            case 'expired':
                                $data['msg'] = sprintf(
                                    __('Your license key expired on %s.', 'directorist-live-chat'),
                                    date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                                );
                                $data['status'] = false;
                                break;

                            case 'revoked':
                                $data['status'] = false;
                                $data['msg'] = __('Your license key has been disabled.', 'directorist-live-chat');
                                break;

                            case 'missing':

                                $data['msg'] = __('Invalid license.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            case 'invalid':
                            case 'site_inactive':

                                $data['msg'] = __('Your license is not active for this URL.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            case 'item_name_mismatch':

                                $data['msg'] = sprintf(__('This appears to be an invalid license key for %s.', 'directorist-live-chat'), 'Directorist - Listings with Map');
                                $data['status'] = false;
                                break;

                            case 'no_activations_left':

                                $data['msg'] = __('Your license key has reached its activation limit.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;

                            default:
                                $data['msg'] = __('An error occurred, please try again.', 'directorist-live-chat');
                                $data['status'] = false;
                                break;
                        }

                    } else {
                        $data['status'] = true;
                        $data['msg'] = __('License activated successfully!', 'directorist-live-chat');
                    }

                }
            } else {
                $data['status'] = false;
                $data['msg'] = __('License not found!', 'directorist-live-chat');
            }
            wp_send_json($data);
            die();
        }

        public function atbdp_delete_chat_history()
        {
            $chatListing_id = !empty($_POST['chatListing_id']) ? sanitize_text_field($_POST['chatListing_id']) : '';
            $chatAuthor_id = !empty($_POST['chatAuthor_id']) ? sanitize_text_field($_POST['chatAuthor_id']) : '';
            $chat_listing_author = get_post_field('post_author', $chatListing_id);
            $chats = get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)->get_posts();
            $data = array();
            if (!empty($chats)) {
                foreach ($chats as $chat) {
                    $chat_id = $chat->ID;
                    $delete = wp_delete_post($chat_id);
                    if ($delete) {
                        $data['message'] = __('Deleted Successfully.');
                    } else {
                        $data['message'] = __('Sorry! Action not completed.');
                    }
                }
                wp_send_json($data);
                die();
            }
        }

        public function atbdp_get_admin_chat_history()
        {
            $chatListing_id = !empty($_POST['chatListing_id']) ? sanitize_text_field($_POST['chatListing_id']) : '';
            $chatAuthor_id = !empty($_POST['chatAuthor_id']) ? sanitize_text_field($_POST['chatAuthor_id']) : '';
            $chat_listing_author = get_post_field('post_author', $chatListing_id);
            $chats = get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)->get_posts();
            if (!empty($chats)) {
                ob_start();
                foreach ($chats as $chat) {
                    $chat_id = $chat->ID;
                    $chat_author = get_post_field('post_author', $chat_id);
                    $image = get_avatar($chat_author, 32);
                    $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                    $admin_chat = get_current_user_id() == $chat_author ? 'directorist-admin-chat' : '';
                    $author = get_user_by('id', (int)$chat_author);
                    $author_name = $author->display_name;
                    $date = new DateTime($chat->post_date);
                    $chat_time = $date->format('h:i A');
                    echo sprintf('<li class="%s">%s <div class="directorist-chat-content-wrap"><div class="directorist-chat-un-time"><span class="directorist-chat-user-name">%s</span><span class="directorist-chat-time">%s</span></div><div class="directorist-listing-chat-content"><p>%s</p> <div class="dlc-dropdown"><a href="#" class="dlc-dropdown-toggle"></a><ul class="dlc-dropdown-items"><li><a href="#">Copy</a></li><li><a href="#">Quote</a></li><li><a href="#">Forward</a></li><li><a href="#">Delete</a></li></ul></div></div></div></li>', $admin_chat, $image, ucwords($author_name), $chat_time, $chat_msg);

                }
            } else {
                echo '<span class="directorist-atbdp-no-chat">' . __('Nothing found!') . '</span>';
            }
            $output = ob_get_clean();
            print $output;
            wp_die();
        }

        public function atbdp_get_chat_history()
        {
            $chatListing_id = !empty($_POST['chatListing_id']) ? sanitize_text_field($_POST['chatListing_id']) : '';
            $chatAuthor_id = !empty($_POST['chatAuthor_id']) ? sanitize_text_field($_POST['chatAuthor_id']) : '';
            $chat_listing_author = get_post_field('post_author', $chatListing_id);
            if (!atbdp_logged_in_user()) {
                die();
            }

            if (get_current_user_id() == $chat_listing_author) {
                die();
            }
            // var_dump(listing_chat_exists_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id));
            // lets check if user's chat available for the listing
            if (!get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)) {
                $chats = '';
            } else {
                $chats = get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)->get_posts();
            }
            //$chats = $this->get_chats(null, null, 2)->get_posts();
            if (!empty($chats)) {
                ob_start();
                foreach ($chats as $chat) {
                    $chat_id = $chat->ID;
                    $chat_author = get_post_field('post_author', $chat_id);
                    $image = get_avatar($chat_author, 32);
                    $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                    $admin_chat = get_current_user_id() === (int)$chat_author ? 'directorist-user-chat' : '';
                    $author = get_user_by('id', (int)$chat_author);
                    $author_name = $author->display_name;
                    $date = new DateTime($chat->post_date);
                    $chat_time = $date->format('h:i A'); ?>
                    <li class="<?php echo $admin_chat; ?>">
                        <?php echo $image; ?>
                        <div class="directorist-chat-content-wrap">
                            <div class="directorist-chat-un-time">
                                <span class="directorist-chat-user-name"><?php echo ucwords($author_name); ?></span><span
                                        class="directorist-chat-time"><?php echo $chat_time; ?></span>
                            </div>
                            <div class="directorist-listing-chat-content">
                                <p><?php echo $chat_msg; ?></p>

                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            $output = ob_get_clean();
            print $output;
            wp_die();
        }

        public function register_new_post_types()
        {
            $labels = array(
                'name' => _x('Chats', 'Plural Name of Chat', 'directorist'),
                'singular_name' => _x('Chat', 'Singular Name of Chat', 'directorist'),
                'menu_name' => __('Chats', 'directorist'),
                'name_admin_bar' => __('Chat', 'directorist'),
                'parent_item_colon' => __('Parent Chat:', 'directorist'),
                'all_items' => __('All Chats', 'directorist'),
                'add_new_item' => __('Add New chat', 'directorist'),
                'add_new' => __('Add New chat', 'directorist'),
                'new_item' => __('New chat', 'directorist'),
                'edit_item' => __('Edit chat', 'directorist'),
                'update_item' => __('Update chat', 'directorist'),
                'view_item' => __('View chat', 'directorist'),
                'search_items' => __('Search chat', 'directorist'),
                'not_found' => __('No Chats found', 'directorist'),
                'not_found_in_trash' => __('Not Chats found in Trash', 'directorist'),
            );
            $args = array(
                'label' => __('Chat', 'directorist'),
                'description' => __('Chats', 'directorist'),
                'labels' => $labels,
                'supports' => array('title', 'editor', 'author', 'thumbnail'),
                'show_in_rest' => true,
                'rest_base' => 'atbdp_chats',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true, // show the menu only to the admin
                'show_in_menu' => current_user_can('manage_atbdp_options') ? 'edit.php?post_type=' . ATBDP_POST_TYPE : false,
                'menu_position' => 21,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => false,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
            );
            register_post_type('atbdp_chat', $args);
        }

        public function atbdp_live_chat()
        {
            $chatListing_id = !empty($_POST['chatListing_id']) ? sanitize_text_field($_POST['chatListing_id']) : '';
            $chatAuthor_id = !empty($_POST['chatAuthor_id']) ? sanitize_text_field($_POST['chatAuthor_id']) : '';
            $chatMsg = !empty($_POST['chatMsg']) ? sanitize_text_field($_POST['chatMsg']) : '';
            $chat_listing_author = get_post_field('post_author', $chatListing_id);
            if (!get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)->get_posts()) {
                // fresh user so send email notification
                if ($chatAuthor_id != $chat_listing_author) {
                    send_email_notification($chatListing_id, $chat_listing_author);
                }
            }
            // process chat to database
            $chat_id = wp_insert_post(array(
                'post_content' => '',
                'post_title' => 'Chat for ' . get_the_title($chatListing_id),
                'post_status' => 'publish',
                'post_type' => 'atbdp_chat',
                'comment_status' => false,
            ));
            update_post_meta($chat_id, '_chatListing_id', $chatListing_id);
            update_post_meta($chat_id, '_chatAuthor_id', $chatAuthor_id);
            update_post_meta($chat_id, '_chatMsg', $chatMsg);
            update_post_meta($chat_id, '_chat_listing_author', $chat_listing_author);
            if ($chat_id) {
                $data['status'] = 'success';
                $data['listing_id'] = $chatListing_id;
                $data['chat_author_id'] = $chatAuthor_id;
                $data['image'] = get_avatar($chatAuthor_id, 32);
            } else {
                $data['status'] = 'fail';
            }
            wp_send_json($data);
            die();
        }

        /**
         * @since 1.0
         */

        public function atbdp_live_chat_single()
        {
            // Check Restriction
            $restricted = atbdp_check_live_chat_restriction( get_the_ID() );
            if ( $restricted ) { return; }

            ob_start();
            if (is_singular(ATBDP_POST_TYPE)) {
                $enable_chat = get_directorist_option('enable_live_chat', 1);
                $chat_listing_author = get_post_field('post_author', get_the_ID());
                $user = get_userdata($chat_listing_author);
                if ((get_current_user_id() == $chat_listing_author) || empty($enable_chat)) {
                    return;
                }
                // let's check user has chat in this listing
                if (!get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts()) {
                    $chats = '';
                } else {
                    $chats = get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts();
                }
            ?>
            <div class="directorist-chat-wrapper">
                <div class="directorist-start-chat">
                    <!-- if user is not logged in -->
                    <?php
                    if (!atbdp_logged_in_user()) {
                        $login = ATBDP_Permalink::get_login_page_link();
                        $registration = ATBDP_Permalink::get_registration_page_link();
                        ?>
                        <div>
                            <button type="submit" class="dcl_login_alert directorist-btn directorist-btn-primary"><i class="fa fa-comments"></i>
                                <?php
                                $start_chat_button = get_directorist_option('start_chat_button', __('Start Chatting', 'directorist-live-chat'));
                                echo esc_attr($start_chat_button); ?>
                            </button>
                            <div class="dcl_login_notice atbd_notice alert alert-info" role="alert">
                                <span class="fa fa-info-circle"
                                      aria-hidden="true"></span><?php echo esc_attr(__('You need to', 'directorist-live-chat')); ?>
                                <a href="<?php echo esc_url($login); ?>"><?php echo esc_attr(__('Login', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('or', 'directorist-live-chat')); ?>
                                <a
                                        href="<?php echo esc_url($registration); ?>"><?php echo esc_attr(__('Register', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('to chat with it\'s owner', 'directorist-live-chat')); ?>
                            </div>
                        </div>
                        <?php
                    } else { ?>
                        <button class="directorist-start-chat-btn" type="submit"><i class="fa fa-comments"></i>
                            <span><?php
                                $show_chat_button = get_directorist_option('show_chat_button', __('Show Chats', 'directorist-live-chat'));
                                echo esc_attr($show_chat_button); ?></span>
                        </button>
                        <?php
                    } ?>
                </div>
                <div class="directorist-client-chat-content-area">
                    <div class="directorist-manage-fees-wrapper">
                        <div id="directorist-user-message-container">
                            <div>
                                <div>
                                    <input type="hidden" name="userId" value="<?php echo $user->user_login; ?>">
                                    <ul id="directorist-user-message-box">
                                        <?php
                                        if (!empty($chats)) {
                                            foreach ($chats as $chat) {
                                                $chat_id = $chat->ID;
                                                $chat_author = get_post_field('post_author', $chat_id);
                                                $image = get_avatar($chat_author, 32);
                                                $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                                                $admin_chat = get_current_user_id() === (int)$chat_author ? 'directorist-user-chat' : '';
                                                $author = get_user_by('id', (int)$chat_author);
                                                $author_name = $author->display_name;
                                                $date = new DateTime($chat->post_date);
                                                $chat_time = $date->format('h:i A'); ?>
                                                <li class="<?php echo $admin_chat; ?>">
                                                    <?php echo $image; ?>
                                                    <div class="directorist-chat-content-wrap">
                                                        <div class="directorist-chat-un-time">
                                                            <span class="directorist-chat-user-name"><?php echo ucwords($author_name); ?></span><span
                                                                    class="directorist-chat-time"><?php echo $chat_time; ?></span>
                                                        </div>
                                                        <div class="directorist-listing-chat-content">
                                                            <p><?php echo $chat_msg; ?></p>

                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                        if(empty($chats)){
                                            echo '<span class="directorist-atbdp-no-chat">' . __('No record found!', 'directorist-live-chat') . '</span>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="/" method="POST" id="ChatForm">
                        <input type="text" id="txt" name="chatMsg"
                               placeholder="<?php echo esc_attr(__('type your message here...', 'directorist-live-chat')); ?>"
                               autocomplete="off" required>
                        <input type="hidden" name="chatAuthor_id" value="<?php echo get_current_user_id(); ?>">
                        <input type="hidden" name="chatListing_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" name="public_chat_form">
                        <button type="submit"><i class="la la-paper-plane"></i></button>
                    </form>
                </div>
            </div>
                <?php
            }
            return ob_get_clean();
        }

        public function atbdp_after_single_listing()
        {
            // Check Restriction
            $restricted = atbdp_check_live_chat_restriction( get_the_ID() );
            if ( $restricted ) { return; }

            // Prevent if custom single page template is selected
            $single_page_template_id    = get_directorist_option( 'single_listing_page', false );
            $using_single_page_template = ( empty( $single_page_template_id ) ) ? true : false;

            if ( ! $using_single_page_template ) { return; }

            $enable_chat         = get_directorist_option('enable_live_chat', 1);
            $chat_listing_author = get_post_field('post_author', get_the_ID());
            $user                = get_userdata($chat_listing_author);
            if ( ( get_current_user_id() == $chat_listing_author) || empty( $enable_chat ) ) {
                return;
            }

            // let's check user has chat in this listing
            if (!get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts()) {
                $chats = '';
            } else {
                $chats = get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts();
            }
            ?>
            <div class="directorist-chat-wrapper">
                <div class="directorist-start-chat">
                    <!-- if user is not logged in -->
                    <?php
                    if (!atbdp_logged_in_user()) {
                        $login = ATBDP_Permalink::get_login_page_link();
                        $registration = ATBDP_Permalink::get_registration_page_link();
                        ?>
                        <div>
                            <button type="submit" class="dcl_login_alert directorist-btn directorist-btn-primary"><i class="fa fa-comments"></i>
                                <?php
                                $start_chat_button = get_directorist_option('start_chat_button', __('Start Chatting', 'directorist-live-chat'));
                                echo esc_attr($start_chat_button); ?>
                            </button>
                            <div class="dcl_login_notice atbd_notice alert alert-info" role="alert">
                                <span class="fa fa-info-circle"
                                      aria-hidden="true"></span><?php echo esc_attr(__('You need to', 'directorist-live-chat')); ?>
                                <a href="<?php echo esc_url($login); ?>"><?php echo esc_attr(__('Login', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('or', 'directorist-live-chat')); ?>
                                <a
                                        href="<?php echo esc_url($registration); ?>"><?php echo esc_attr(__('Register', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('to chat with it\'s owner', 'directorist-live-chat')); ?>
                            </div>
                        </div>
                        <?php
                    } else { ?>
                        <button class="directorist-start-chat-btn" type="submit"><i class="fa fa-comments"></i>
                            <span><?php
                                $show_chat_button = get_directorist_option('show_chat_button', __('Show Chats', 'directorist-live-chat'));
                                echo esc_attr($show_chat_button); ?></span>
                        </button>
                        <?php
                    } ?>
                </div>
                <div class="directorist-client-chat-content-area">
                    <div class="directorist-manage-fees-wrapper">
                        <div id="directorist-user-message-container">
                            <div>
                                <div>
                                    <input type="hidden" name="userId" value="<?php echo $user->user_login; ?>">
                                    <ul id="directorist-user-message-box">
                                        <?php
                                        if (!empty($chats)) {
                                            foreach ($chats as $chat) {
                                                $chat_id = $chat->ID;
                                                $chat_author = get_post_field('post_author', $chat_id);
                                                $image = get_avatar($chat_author, 32);
                                                $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                                                $admin_chat = get_current_user_id() === (int)$chat_author ? 'directorist-user-chat' : '';
                                                $author = get_user_by('id', (int)$chat_author);
                                                $author_name = $author->display_name;
                                                $date = new DateTime($chat->post_date);
                                                $chat_time = $date->format('h:i A'); ?>
                                                <li class="<?php echo $admin_chat; ?>">
                                                    <?php echo $image; ?>
                                                    <div class="directorist-chat-content-wrap">
                                                        <div class="directorist-chat-un-time">
                                                            <span class="directorist-chat-user-name"><?php echo ucwords($author_name); ?></span><span
                                                                    class="directorist-chat-time"><?php echo $chat_time; ?></span>
                                                        </div>
                                                        <div class="directorist-listing-chat-content">
                                                            <p><?php echo $chat_msg; ?></p>

                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                        if(empty($chats)){
                                            echo '<span class="directorist-atbdp-no-chat">' . __('No record found!', 'directorist-live-chat') . '</span>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="/" method="POST" id="ChatForm">
                        <input type="text" id="txt" name="chatMsg"
                               placeholder="<?php echo esc_attr(__('type your message here...', 'directorist-live-chat')); ?>"
                               autocomplete="off" required>
                        <input type="hidden" name="chatAuthor_id" value="<?php echo get_current_user_id(); ?>">
                        <input type="hidden" name="chatListing_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" name="public_chat_form">
                        <button type="submit"><i class="la la-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <?php

        }


        public function directorist_live_chat_content()
        {
            $user_listings = get_chatted_listings();
            $chats         = get_chats(get_current_user_id(), get_current_user_id(), '')->get_posts();
            $authors       = all_chatted_user_by_listing($user_listings[0]);
            ob_start();
            ?>

            <div <?php echo apply_filters('dlc_dashboard_content_div_attributes', 'class="atbd_tab_inner" id="live_chat_inner"'); ?>>

                <?php do_action('dlc_dashboard_before_content'); ?>

                <!-- <div class="atbd_manage_conversation"> -->
                    <?php if (!empty($chats && $user_listings)) { ?>
                        <div id="directorist-admin-message-container">
                            <div class="directorist-admin-message-wrap">
                                <div class="directorist-a-m-sidebar">
                                    <h3><?php
                                        $chat_user_tab = get_directorist_option('chat_user_tab', __('Chat', 'direo-extension'));
                                        echo esc_attr($chat_user_tab); ?></h3>
                                    <div class="directorist-message-tabs">
                                        <!--<div class="atbdlc_tab_nav">
                                            <ul>
                                                <li><a href="" target="my-listings"
                                                       class="atbdlc_tn_link lc_tabItemActive"><?php /*_e('Listings', 'direo-extension'); */ ?></a>
                                                </li>
                                            </ul>
                                        </div>-->
                                        <div class="directorist-message-tabs__content">

                                            <div class="directorist-message-tabs__inner directorist-lc-tab-content-active" id="my-listings">
                                                <ul>
                                                    <?php
                                                    if ($user_listings) {
                                                        $chat_author = get_post_field('post_author', $user_listings[0]);
                                                        $chats = get_chats(get_current_user_id(), $chat_author, $user_listings[0])->get_posts();
                                                        $chatAuthorId = get_post_meta($chats[0]->ID, '_chatAuthor_id', true);
                                                        $chats = listing_chat_by_admin($chatAuthorId, $user_listings[0])->get_posts();
                                                        $chat_author = get_post_meta($chats[0]->ID, '_chatAuthor_id', true);
                                                        $gravatar = get_avatar($chat_author, 32);
                                                        $display_name = get_the_author_meta('display_name', $chat_author);
                                                        if ($user_listings > 1) {
                                                            foreach ($user_listings as $listing) {
                                                                $title = get_the_title($listing);
                                                                $listing_img = get_post_meta($listing, '_listing_img', true);
                                                                $listing_prv_img = get_post_meta($listing, '_listing_prv_img', true);
                                                                if (!empty($listing_prv_img)) {
                                                                    $prv_image = wp_get_attachment_image_src($listing_prv_img, 'large')[0];

                                                                }
                                                                if (!empty($listing_img[0])) {

                                                                    $gallery_img = atbdp_get_image_source($listing_img[0], 'medium');
                                                                }
                                                                $img_src = ATBDP_PUBLIC_ASSETS . 'images/grid.jpg';
                                                                if (!empty($listing_prv_img)) {

                                                                    $img_src = $prv_image;

                                                                }
                                                                if (!empty($listing_img[0]) && empty($listing_prv_img)) {

                                                                    $img_src = $gallery_img;

                                                                }
                                                                ?>
                                                                <li class="directorist-chatted-listing"
                                                                    data-listing-title="<?php echo esc_attr(get_the_title($listing)) ?>"
                                                                    data-listing-link="<?php echo esc_url(get_the_permalink($listing)) ?>"
                                                                    data-listing-image-src="<?php echo esc_url($img_src) ?>"
                                                                    data-chat-user-id="<?php echo esc_attr($chat_author) ?>"
                                                                    data-chat-user-name="<?php echo esc_attr($display_name) ?>"
                                                                    data-chat-user-img="<?php echo esc_attr($gravatar) ?>"
                                                                    data-listing-id="<?php echo esc_attr($listing) ?>">
                                                                    <a href="">
                                                                        <img src="<?php echo esc_url($img_src); ?>"
                                                                             alt=""/>
                                                                        <span><?php echo esc_attr($title); ?></span>
                                                                    </a>
                                                                </li>
                                                                <?php
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- ends: .directorist-a-m-sidebar -->
                                <div class="directorist-message-list">
                                    <div class="directorist-message-header">
                                        <div class="directorist-message-list-top">
                                            <div class="directorist-message-list-top__left">
                                                <div class="directorist-message-list-user">
                                                    <span class="directorist-message-list-user__img"> <?php echo $gravatar; ?> </span>
                                                    <span class="directorist-message-list-user__name"><?php echo esc_attr($display_name) ?></span>
                                                    <input type="hidden" class="directorist-mas-chat-user-id"
                                                        value="<?php echo $chat_author; ?>">
                                                </div>
                                                <div class="directorist-message-list-item">
                                                    <?php
                                                    $listing = $user_listings[0];
                                                    $title = get_the_title($listing);
                                                    $listing_img = get_post_meta($listing, '_listing_img', true);
                                                    $listing_prv_img = get_post_meta($listing, '_listing_prv_img', true);
                                                    if (!empty($listing_prv_img)) {
                                                        $prv_image = wp_get_attachment_image_src($listing_prv_img, 'large')[0];

                                                    }
                                                    if (!empty($listing_img[0])) {

                                                        $gallery_img = atbdp_get_image_source($listing_img[0], 'medium');
                                                    }
                                                    $img_src = ATBDP_PUBLIC_ASSETS . 'images/grid.jpg';
                                                    if (!empty($listing_prv_img)) {

                                                        $img_src = $prv_image;

                                                    }
                                                    if (!empty($listing_img[0]) && empty($listing_prv_img)) {

                                                        $img_src = $gallery_img;

                                                    }
                                                    ?>
                                                    <a class="directorist-message-list-item__link"
                                                    href="<?php echo get_the_permalink($listing) ?>">
                                                        <img class="directorist-message-list-item__img" src="<?php echo esc_url($img_src); ?>"
                                                            alt=""/>
                                                        <span class="directorist-message-list-item__title"><?php echo esc_attr($title); ?></span>
                                                    </a>
                                                </div>
                                            </div>

                                            <?php if ( ! empty( $authors ) ) { ?>
                                                <div class="directorist-message-list-top__right">
                                                    <div class="directorist-message-list-top__right--action">
                                                        <!-- <div class="lc-delete-message atbd-dropdown">
                                                            <a href="" class="atbd-dropdown-toggle"
                                                                data-drop-toggle="atbd-toggle"><span
                                                                        class="fa fa-ellipsis-h"></span></a>
                                                            <div class="atbd-dropdown-items">
                                                                <a href="" class="atbd-dropdown-item mas-delete-chat">Delete Conversation</a>
                                                            </div>
                                                        </div>-->
                                                        <div class="directorist-chat-all-user directorist-dropdown directorist-dropdown-js">
                                                            <a href="#" class="directorist-dropdown__toggle directorist-dropdown__toggle-js directorist-toggle-has-icon">
                                                                <span class="fa fa-user"></span> <?php echo esc_attr(__('Filter by User', 'direo-extension')); ?>
                                                            </a>
                                                            <div class="directorist-dropdown__links directorist-dropdown__links-js">
                                                                <?php
                                                                    foreach ($authors as $author) {
                                                                        if ( (int)$author !== get_current_user_id() ) {
                                                                            $display_name = get_the_author_meta( 'display_name', $author );
                                                                            $img          = get_avatar($author, 32);

                                                                            printf( '<a href="#" data-chatAuthor="%s" data-chatAuthorName="%s" class="directorist-dropdown__links--single directorist-all-chated-author">%s</a><span class="chatAuthorImg">%s</span>', $author, $display_name, $display_name, $img );
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div><!-- ends: .atbd-message-top-bar -->
                                    </div>

                                    <ul id="directorist-user-message-box">
                                        <div class="directorist-day-divider-line">
                                            <span class="directorist-message-day"><?php _e( 'Today', 'directorist-live-chat'); ?></span>
                                        </div>
                                        <?php
                                        if (!empty($chats)) {
                                            foreach ($chats as $chat) {
                                                $chat_id = $chat->ID;
                                                $chat_author = get_post_field('post_author', $chat_id);
                                                $image = get_avatar($chat_author, 32);
                                                $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                                                $admin_chat = get_current_user_id() == $chat_author ? 'directorist-admin-chat' : '';
                                                $author = get_user_by('id', (int)$chat_author);
                                                $author_name = $author->display_name;
                                                $date = new DateTime($chat->post_date);
                                                $chat_time = $date->format('h:i A');
                                                echo sprintf('<li class="%s">%s <div class="directorist-chat-content-wrap"><div class="directorist-chat-un-time"><span class="directorist-chat-user-name">%s</span><span class="directorist-chat-time">%s</span></div><div class="directorist-listing-chat-content"><p>%s</p> <div class="dlc-dropdown"><a href="#" class="dlc-dropdown-toggle"></a><ul class="dlc-dropdown-items"><li><a href="#">Copy</a></li><li><a href="#">Quote</a></li><li><a href="#">Forward</a></li><li><a href="#">Delete</a></li></ul></div></div></div></li>', $admin_chat, $image, ucwords($author_name), $chat_time, $chat_msg);
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <form action="/" method="POST" id="ChatForm" class="directorist-listing-chat-form">
                                        <input id="txt" type="text" name="chatMsg" autofocus="on"
                                               placeholder="<?php echo esc_attr(__('type your message here...', 'direo-extension')); ?>"
                                               autocomplete="off" required>
                                        <input type="hidden" name="chatAuthor_id"
                                               value="<?php echo get_current_user_id(); ?>">
                                        <input type="hidden" name="chatListing_id"
                                               value="<?php echo esc_attr($listing) ?>">
                                        <button class="directorist-chat-submit" type="submit"><i class="la la-paper-plane"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        if (empty($user_listings)) {
                            echo sprintf('<div class="no_chat_history as_author"><p>%s</p></div>', __('No chat history found as an author!', 'directorist-live-chat'));
                        } else {
                            echo sprintf('<div class="no_chat_history"><p>%s</p></div>', __('Nothing found!', 'direo-extension'));
                        }
                    } ?>
                <!-- </div> -->

                <?php do_action('dlc_dashboard_after_content'); ?>

            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * @since 1.0
         */
        public function load_admin_needed_scripts()
        {
            wp_register_script('dlc_main__admin_js', plugin_dir_url(__FILE__) . 'assets/admin/main.js');
            wp_enqueue_style('dlc_main__admin_css', plugin_dir_url(__FILE__) . 'assets/admin/main.css');
            wp_enqueue_script('dlc_main__admin_js');
            $data = array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            );
            wp_localize_script('dlc_main__admin_js', 'dlc_main__admin_js', $data);
        }

        /**
         * It displays settings for the
         * @param $screen  string  get the current screen
         * @since 1.0.0
         */
        public function load_needed_scripts($screen)
        {
            $is_single         = ( is_singular( ATBDP_POST_TYPE ) ) ? true : false;
            $dashboard_page_id = get_directorist_option( 'user_dashboard', '' );
            $is_dash           = ( get_the_ID() == $dashboard_page_id ) ? true : false;

            if ( ! $is_single && ! $is_dash ) { return; }

            wp_enqueue_script('wp-api');
            wp_register_script('dlc_js_api', plugin_dir_url(__FILE__) . '/assets/public/main.js', ['jquery'], true);
            wp_register_script('dlc_sockek_js_api', 'https://directorist-live-chat.herokuapp.com/socket.io/socket.io.js', ['jquery'], true);
            wp_register_style('atbdp_chat_style', plugin_dir_url(__FILE__) . '/assets/public/style.css');
            wp_enqueue_script('dlc_js_api');
            wp_enqueue_script('dlc_sockek_js_api');
            wp_enqueue_style('atbdp_chat_style');

            $reload_interval = get_directorist_option('reload_interval', 5000);
            $show_chat_button = get_directorist_option('show_chat_button', __('Show Chat', 'directorist-live-chat'));
            $hide_chat_button = get_directorist_option('hide_chat_button', __('Hide Chat', 'directorist-live-chat'));
            $image = get_avatar(wp_get_current_user()->ID, 32);
            $l10n = array(
                'ajaxurl' => admin_url() . 'admin-ajax.php',
                'nonce' => wp_create_nonce('wp_rest'),
                'reload_interval' => (int)$reload_interval,
                'show_chat_button' => $show_chat_button,
                'hide_chat_button' => $hide_chat_button,
                'user' => wp_get_current_user(),
                'admin_avatar' => $image,
            );
            wp_localize_script('dlc_js_api', 'dlc_js_api', $l10n);
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
         * It Includes and requires necessary files.
         *
         * @access private
         * @return void
         * @since 1.0
         */
        private function setup_constants()
        {
            // Plugin version
            if (!defined('DLC_VERSION')) {
                define('DLC_VERSION', self::get_version_from_file_content( __FILE__ ) );
            }
            // Plugin Folder Path.
            if ( ! defined( 'DLC_DIR' ) ) { define( 'DLC_DIR', plugin_dir_path( __FILE__ ) ); }
            // Plugin Template Path
            if ( !defined('DLC_TEMPLATES_DIR') ) { define('DLC_TEMPLATES_DIR', DLC_DIR.'templates/'); }

            // plugin author url
            if (!defined('ATBDP_AUTHOR_URL')) {
                define('ATBDP_AUTHOR_URL', 'https://directorist.com');
            }
            // post id from download post type (edd)
            if (!defined('ATBDP_DLC_POST_ID')) {
                define('ATBDP_DLC_POST_ID', 21274);
            }
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
            require_once plugin_dir_path(__FILE__) . 'includes/helper.php';
            include dirname(__FILE__) . '/includes/directory_type.php';

            // setup the updater
            if (!class_exists('EDD_SL_Plugin_Updater')) {
                // load our custom updater if it doesn't already exist
                include dirname(__FILE__) . '/includes/EDD_SL_Plugin_Updater.php';
            }
            new DLC_Directory_Type_Manager();
            $license_key = trim(get_option('directorist_live_chat_license'));
            new EDD_SL_Plugin_Updater(ATBDP_AUTHOR_URL, __FILE__, array(
                'version' => DLC_VERSION, // current version number
                'license' => $license_key, // license key (used get_option above to retrieve from DB)
                'item_id' => ATBDP_DLC_POST_ID, // id of this plugin
                'author' => 'AazzTech', // author of this plugin
                'url' => home_url(),
                'beta' => false, // set to true if you wish customers to receive update notifications of beta releases
            ));
        }

        /**
         * @since 1.0
         */
        public function mas_license_settings_controls($default)
        {
            $status = get_option('directorist_live_chat_license_status');
            if (!empty($status) && ($status !== false && $status == 'valid')) {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'live_chat_deactivated',
                    'label' => __('Action', 'directorist-mark-as-sold'),
                    'validation' => 'numeric',
                );
            } else {
                $action = array(
                    'type' => 'toggle',
                    'name' => 'live_chat_activated',
                    'label' => __('Action', 'directorist-mark-as-sold'),
                    'validation' => 'numeric',
                );
            }
            $new = apply_filters('atbdp_live_chat_license_controls', array(
                'type' => 'section',
                'title' => __('Live Chat', 'directorist-live-chat'),
                'description' => __('You can active your Live Chat extension here.', 'directorist-live-chat'),
                'fields' => apply_filters('atbdp_live_chat_license_settings_field', array(
                    array(
                        'type' => 'textbox',
                        'name' => 'live_chat_license',
                        'label' => __('License', 'directorist-mark-as-sold'),
                        'description' => __('Enter your Live Chat extension license', 'directorist-mark-as-sold'),
                        'default' => '',
                    ),
                    $action,
                )),
            ));
            $settings = apply_filters('atbdp_licence_menu_for_live_chat', true);
            if ($settings) {
                array_push($default, $new);
            }
            return $default;
        }

         /**
         * It  loads a template file from the Default template directory.
         * @param string $name Name of the file that should be loaded from the template directory.
         * @param array $args Additional arguments that should be passed to the template file for rendering dynamic  data.
         */
        public function load_template( $template, $args = array())
        {
            directorist_live_chat_get_template( $template, $args );
        }

        /**
         * It register the text domain to the WordPress
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-live-chat', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        private function __construct()
        {
            /*making it private prevents constructing the object*/
        }

        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0');
        }

        public function __wakeup()
        {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0');
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
     * The main function for that returns Directorist_Live_Chat
     *
     * The main function responsible for returning the one true Directorist_Live_Chat
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     *
     * @return object|Directorist_Live_Chat The one true Directorist_Live_Chat Instance.
     * @since 1.0
     */
    function Directorist_Live_Chat()
    {
        return Directorist_Live_Chat::instance();
    }

    if ( directorist_is_plugin_active( 'directorist/directorist-base.php' ) ) {
        Directorist_Live_Chat(); // get the plugin running
    }
}
