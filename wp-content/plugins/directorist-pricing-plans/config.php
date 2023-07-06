<?php
// Plugin version.
if ( ! defined( 'ATPP_VERSION' ) ) {define( 'ATPP_VERSION', $version );}
// Plugin Folder Path.
if ( ! defined( 'ATPP_DIR' ) ) { define( 'ATPP_DIR', plugin_dir_path( ATPP_FILE ) ); }
// Plugin Folder URL.
if ( ! defined( 'ATPP_URL' ) ) { define( 'ATPP_URL', plugin_dir_url( ATPP_FILE ) ); }
// Plugin Root File.

if ( ! defined( 'ATPP_BASE' ) ) { define( 'ATPP_BASE', plugin_basename( ATPP_FILE ) ); }
// Plugin Includes Path
if ( !defined('ATPP_INC_DIR') ) { define('ATPP_INC_DIR', ATPP_DIR.'inc/'); }
// Plugin Classes Path
if ( !defined('ATPP_CLASSES_DIR') ) { define('ATPP_CLASSES_DIR', ATPP_DIR.'inc/classes/'); }
// Plugin Views Path
if ( !defined('ATPP_VIEWS_DIR') ) { define('ATPP_VIEWS_DIR', ATPP_DIR.'inc/views/'); }
// Plugin Assets Path
if ( !defined('ATPP_ASSETS') ) { define('ATPP_ASSETS', ATPP_URL.'assets/'); }
// Plugin Template Path
if ( !defined('ATPP_TEMPLATES_DIR') ) { define('ATPP_TEMPLATES_DIR', ATPP_DIR.'templates/'); }
// Plugin Language File Path
if ( !defined('ATPP_LANG_DIR') ) { define('ATPP_LANG_DIR', dirname(plugin_basename( ATPP_FILE ) ) . '/languages'); }
// Plugin Name
if ( !defined('ATPP_NAME') ) { define('ATPP_NAME', 'Directorist - Pricing Plans'); }
// Plugin Post Type
if ( !defined('ATBDP_POST_TYPE') ) { define('ATBDP_POST_TYPE', 'at_biz_dir'); }
if ( !defined('ATBDP_ORDER_POST_TYPE') ) { define('ATBDP_ORDER_POST_TYPE', 'atbdp_orders'); }
if ( !defined('ATBDP_PRICING_PLANS_POST_TYPE') ) { define('ATBDP_PRICING_PLANS_POST_TYPE', 'ATBDP_Pricing_Plans'); }
// Plugin Alert Message
if ( !defined('ATPP_ALERT_MSG') ) { define('ATPP_ALERT_MSG', __('You do not have the right to access this file directly', 'directorist-pricing-plans')); }
// plugin author url
if (!defined('ATBDP_AUTHOR_URL')) {
    define('ATBDP_AUTHOR_URL', 'https://directorist.com');
}
// post id from download post type (edd)
if (!defined('ATBDP_PRICING_PLAN_POST_ID')) {
    define('ATBDP_PRICING_PLAN_POST_ID', 13776 );
}
