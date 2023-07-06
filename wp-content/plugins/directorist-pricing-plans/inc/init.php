<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('ATPP_Init')) :

    class ATPP_Init
    {
        public function __construct()
        {
            if (get_option('atbdp_plan_page_create') < 1) {
                add_action('wp_loaded', array($this, 'add_custom_page'));
            }
            add_action('plugins_loaded', array($this, 'load_textdomain'));
        }

        public function add_custom_page()
        {
            $create_permission = apply_filters('atbdp_create_required_pages', true);
            if ($create_permission) {
                atpp_create_required_pages();
            }
        }

        /**
         * It register the text domain to the WordPress
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-pricing-plans', false, ATPP_LANG_DIR);
        }
    }
endif;
