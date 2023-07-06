<?php
// Plugin version.
if ( ! defined( 'SOCIAL_LOGIN_VERSION' ) ) {define( 'SOCIAL_LOGIN_VERSION', directorist_social_login_get_version_from_file_content( DEB_FILE ) );}
// Plugin Folder Path.
if ( ! defined( 'DEB_DIR' ) ) { define( 'DEB_DIR', plugin_dir_path( DEB_FILE ) ); }
// Plugin Folder URL.
if ( ! defined( 'DEB_URL' ) ) { define( 'DEB_URL', plugin_dir_url( DEB_FILE ) ); }
// Plugin Root File.
if ( ! defined( 'DEB_BASE' ) ) { define( 'DEB_BASE', plugin_basename( DEB_FILE ) ); }
// Plugin Includes Path
if ( !defined('DEB_INC_DIR') ) { define('DEB_INC_DIR', DEB_DIR.'inc/'); }
// Plugin Assets Path
if ( !defined('DEB_ASSETS') ) { define('DEB_ASSETS', DEB_URL.'assets/'); }
if ( !defined('DEB_PUBLIC_ASSETS') ) { define('DEB_PUBLIC_ASSETS', DEB_URL.'assets/public'); }
if ( !defined('DEB_ADMIN_ASSETS') ) { define('DEB_ADMIN_ASSETS', DEB_URL.'assets/admin'); }
// Plugin Template Path
if ( !defined('DEB_TEMPLATES_DIR') ) { define('DEB_TEMPLATES_DIR', DEB_DIR.'templates/'); }
// Plugin Language File Path
if ( !defined('DEB_LANG_DIR') ) { define('DEB_LANG_DIR', dirname(plugin_basename( DEB_FILE ) ) . '/languages'); }
// Plugin Name
if ( !defined('DEB_NAME') ) { define('DEB_NAME', 'Directorist - Extension Base'); }

// Plugin Alert Message
if ( !defined('DEB_ALERT_MSG') ) { define('DEB_ALERT_MSG', __('You do not have the right to access this file directly', 'directorist-social-login')); }

// plugin author url
if (!defined('ATBDP_AUTHOR_URL')) {
    define('ATBDP_AUTHOR_URL', 'https://directorist.com');
}
// post id from download post type (edd)
if (!defined('ATBDP_SOCIAL_LOGIN_POST_ID')) {
    define('ATBDP_SOCIAL_LOGIN_POST_ID', 13795 );
}
