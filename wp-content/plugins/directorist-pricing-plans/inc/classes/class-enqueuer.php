<?php
/*
 * Class: Business Directory Multiple Image = ATPP
 * */
if (!class_exists('ATPP_Enqueuer')):
    class ATPP_Enqueuer
    {
        public function __construct()
        {
            // best hook to enqueue scripts for front-end is 'template_redirect'
            // 'Professional WordPress Plugin Development' by Brad Williams
            add_action('template_redirect', array($this, 'front_end_enqueue_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'register_necessary_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'register_necessary_scripts_front'));
            add_action('template_redirect', array($this, 'atpp_front_end_enqueue_scripts'));

        }


        
        public function atpp_front_end_enqueue_scripts()
        {
            if (is_rtl()) {
                wp_enqueue_style('atpp_main_css_rtl', ATPP_ASSETS . 'css/main-rtl.css', false, ATPP_VERSION);

            } else {
                wp_enqueue_style('atpp_main_css', ATPP_ASSETS . 'css/main.css', false, ATPP_VERSION);
            }
        }

        public function register_necessary_scripts_front()
        {
            wp_register_script('atpp-plan-validator', ATPP_ASSETS . '/js/plan-validator.js', array('jquery'), true);
            wp_enqueue_script('atpp-plan-validator');
            //get listing is if the screen in edit listing
            global $wp;
            global $pagenow;
            $guest_listings = get_directorist_option( 'guest_listings' );
            $current_url = home_url(add_query_arg(array(), $wp->request));
            $planID = '';
            if ((strpos($current_url, '/edit/') !== false) && ($pagenow = 'at_biz_dir')) {
                $listing_id = substr($current_url, strpos($current_url, "/edit/") + 6);
                $fm_plans = get_post_meta($listing_id, '_fm_plans', true);
                $selected_plan = selected_plan_id();
                $planID = !empty($selected_plan) ? $selected_plan : $fm_plans;
            }
            $price_limit = '99999999999999999999';
            $allow_price = is_plan_allowed_price($planID);
            $price_range_unl = is_plan_price_unlimited($planID);
            if ($allow_price && empty($price_range_unl)) {
                $price_limit = is_plan_price_limit($planID);
            }

            $allow_tag = is_plan_allowed_tag($planID);
            $unl_tag = is_plan_tag_unlimited($planID);
            $tag_limit = '99999999999999999999';
            if ($allow_tag && empty($unl_tag)) {
                $tag_limit = is_plan_tag_limit($planID);
            }

            $validator = array(
                'price_limit'       => $price_limit,
                'guest_customer'    => directorist_direct_purchase() && ! atbdp_logged_in_user() && $guest_listings,
                'is_admin'          => is_admin(),
                'tag_limit'         => $tag_limit,
                'ajaxurl'           => admin_url('admin-ajax.php'),
                'remaining_text'    => __( 'Remaining Character:', 'directorist-pricing-plans'),
                'max_exit'          => __( 'Max character limit reached!', 'directorist-pricing-plans'),
                'crossLimit'        => __('You have crossed the limit!', 'directorist-pricing-plans'),
                'email_placeholder' => __( 'Email', 'directorist-pricing-plans' ),
                'email_required_msg'=> __( 'Continue with email', 'directorist-pricing-plans' ),

            );

            wp_localize_script('atpp-plan-validator', 'plan_validator', $validator);
        }

        public function register_necessary_scripts($current_screen)
        {

            global $post;
            //Only need to enque script on the post.php page
            if ( ( ('post.php' == $current_screen) || ('post-new.php' == $current_screen) )  && 'at_biz_dir' == $post->post_type ) {     
                wp_enqueue_script('atpp-admin-validator-script', ATPP_ASSETS . '/js/plan-validitor-admin.js', array('jquery'), true);
                $data = array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                );                
                wp_localize_script( 'atpp-admin-validator-script', 'validator_admin_js', $data );
            }

            if ( ('post-new.php' == $current_screen)  && 'at_biz_dir' == $post->post_type ) {     
                wp_enqueue_script('admin-new-validator', ATPP_ASSETS . '/js/new-validitor-admin.js', array('jquery'), true);
                $data = array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                );                
                wp_localize_script( 'admin-new-validator', 'new_validator_admin', $data );
            }
            if ( ('post-new.php' == $current_screen)  && 'atbdp_pricing_plans' == $post->post_type ) { 
                //assets for add new plan page
            }
            wp_enqueue_script('atpp-admin-script', ATPP_ASSETS . '/js/main.js', array('jquery'), true);
            wp_enqueue_style('directorist-admin-style');
            wp_enqueue_style('bdmi_main_css', ATPP_ASSETS . 'css/main.css', false, ATPP_VERSION);
            wp_enqueue_style('plan_custom_css');
            $data = array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            );
            wp_localize_script('atpp-admin-script', 'pricing_admin_js', $data);
        }

        /**
         * It loads all scripts for front end if the current post type is our custom post type
         * @param bool $force [optional] whether to load the style in the front end forcibly(even if the post type is not our custom post). It is needed for enqueueing file from a inside the short code call
         */
        public function front_end_enqueue_scripts($force = false)
        {
            // enqueue the style and the scripts on the page when the post type is our registered post type.
            // add scripts for adding the gallery if the user is not using our directoria theme.

            if (!is_directoria_active()) {
                wp_register_style('bdmi_main_css', ATPP_ASSETS . 'css/main.css', false, ATPP_VERSION);
                wp_register_script('main', ATPP_ASSETS . 'js/main.js', array('jquery'), ATPP_VERSION, true);

                wp_enqueue_script('main');
                wp_enqueue_style('bdmi_main_css');
            }
        }
    }
endif;