<?php
/**
 * Directorist Support.
 *
 * @author  WpWax
 * @since   1.5
 * @version 1.0
 */

use Directorist\Directorist_Listing_Dashboard;

function directorist_single_listing_widget( $other_widgets ) {
	$other_widgets['description'] = [
		'type' => 'section',
		'label' => 'Description',
		'icon' => 'las la-paragraph',
		'options' => [
			'icon' => [
				'type'  => 'icon',
				'label' => 'Icon',
				'value' => 'las la-align-justify',
			],
			'label' => [
				'type'  => 'text',
				'label' => 'Label',
				'value' => 'Description',
			],
		],
	];

	$other_widgets['gallery'] = [ 
		'type' => 'section',
		'label' => 'Gallery',
		'icon' => 'la la-image',
		'options' => [
			'icon' => [
				'type'  => 'icon',
				'label' => 'Icon',
				'value' => 'la la-image',
			],
			'label' => [
				'type'  => 'text',
				'label' => 'Label',
				'value' => 'Gallery',
			],
		],
	];
	
	return $other_widgets;

}

add_filter( 'atbdp_single_listing_other_fields_widget', 'directorist_single_listing_widget' );
add_filter( 'directorist_disable_shortcode_restriction_on_scripts', '__return_true' );


// Hide settings
add_filter( 'directorist_search_setting_sections', 'theme_modify_search_settings' );

function theme_modify_search_settings( $value ) {
	$data = $value['search_form']['fields'];
	$settings = [ 'search_border', 'search_more_filters', 'home_display_filter', 'popular_cat_title', 'search_home_bg' ];

	foreach ( $settings as $setting ) {
		$key = array_search( $setting, $data );
		if ( $key !== false ) {
			unset( $data[$key] );
		}
	}

	$value['search_form']['fields'] = array_values( $data );

	return $value;
}

function theme_modify_legacy_settings( $value ) {
	$key = array_search( 'atbdp_legacy_template', $value['legacy']['fields'] );

	if ( $key !== false ) {
		unset( $value['legacy']['fields'][$key] );
	}
	return $value;
}

add_filter( 'atbdp_legacy_sections', 'theme_modify_legacy_settings' );

add_filter( 'atbdp_style_settings_submenu', 'theme_hide_color_submenu' );
function theme_hide_color_submenu( $value ) {
	unset( $value['color_settings'] );
	return $value;
}

// Override settings value
add_filter( 'directorist_option', 'theme_directorist_option', 10, 2 );

function theme_directorist_option( $value, $name ) {

	switch ( $name ) {
		case 'popular_cat_title':
		case 'search_more_filters':
		case 'search_home_bg':
		$value = '';
		break;

		case 'search_border':
		$value = false;
		break;

		case 'home_display_filter':
		$value = 'overlapping';
		break;
	}

	return $value;
}

function direo_get_template_part( $template, $args = array() ) {

	if ( ! class_exists( 'Directorist_Base' ) ) {
		$is_dir_file = strpos( $template, 'directorist/custom/' );

		if ( false !== $is_dir_file ) {
			return;
		}
	}

	if ( is_array( $args ) ) {
		extract( $args );
	}

	$template = '/' . $template . '.php';

	if ( file_exists( get_stylesheet_directory() . $template ) ) {
		$file = get_stylesheet_directory() . $template;
	} else {
		$file = get_template_directory() . $template;
	}

	require $file;
}

function get_direo_dashboard_navigation() {
	$data['dashboard'] = Directorist_Listing_Dashboard::instance();

	return direo_get_template_part( '/directorist/custom/author-navigation', $data );
}

// Dashboard Navigation items
function direo_dashboard_tabs() {

	// Tabs
	$dashboard_tabs   = array();
	$my_listing_tab   = get_directorist_option( 'my_listing_tab', 1 );
	$my_profile_tab   = get_directorist_option( 'my_profile_tab', 1 );
	$fav_listings_tab = get_directorist_option( 'fav_listings_tab', 1 );
	$announcement_tab = get_directorist_option( 'announcement_tab', 1 );

	if ( $my_listing_tab ) {
		$dashboard_tabs['dashboard_my_listings'] = array(
			'title' => get_directorist_option( 'my_listing_tab_text', __( 'My Listing', 'direo' ) ),
			'icon'  => 'las la-list',
		);
	}

	if ( $my_profile_tab ) {
		$dashboard_tabs['dashboard_profile'] = array(
			'title' => get_directorist_option( 'my_profile_tab_text', __( 'My Profile', 'direo' ) ),
			'icon'  => 'las la-user',
		);
	}

	if ( $fav_listings_tab ) {
		$dashboard_tabs['dashboard_fav_listings'] = array(
			'title' => get_directorist_option( 'fav_listings_tab_text', __( 'Favorite Listings', 'direo' ) ),
			'icon'  => 'lab la-heart',
		);
	}

	if ( $announcement_tab ) {
		$dashboard_tabs['dashboard_announcement'] = array(
			'title' => get_directorist_option( 'announcement_tab_text', __( 'Announcements', 'direo' ) ),
			'icon'  => 'las la-bullhorn',
		);
	}

	return $dashboard_tabs;
}

// Added single listing header compatibility
function direo_listing_header_layout( $fields ) {

	unset( $fields['card-options']['general']['back'] );
	unset( $fields['card-options']['general']['section_title'] );
	unset( $fields['card-options']['content_settings']['listing_description'] );
	unset( $fields['widgets']['listing_slider']);

	return $fields;
}

add_filter( 'directorist_listing_header_layout', 'direo_listing_header_layout' );


/**
 * This method add login modal to theme header
 */
function add_login_modal_to_header () {
	if ( class_exists( 'Directorist_Base' ) && ! is_user_logged_in() && ( ! atbdp_is_page( 'login' ) && ! atbdp_is_page( 'registration' ) ) ) {
		get_template_part( 'directorist/custom/login-register' );
	}
}
add_action( 'wp_body_open', 'add_login_modal_to_header' );

// Check whether a field has in Basic or Advanced area
function direo_has_field_in_search_form( $field_name, $searchform, $form_type = "basic" ) {
	$basic_or_advance = 'basic' === $form_type ? '0' : '1';

	return isset( $searchform->form_data[$basic_or_advance]['fields'][$field_name] ) ? true : false;
}

function direo_hex2rgb( $hex ) {
	$hex = str_replace( '#', '', $hex );

	if ( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = "$r, $g, $b";

	return $rgb;
}

function direo_get_file_path( $filename, $dir = false ) {

	if ( $dir ) {
		$child_file = get_stylesheet_directory() . '/' . $dir . '/' . $filename;

		if ( file_exists( $child_file ) ) {
			$file = $child_file;
		} else {
			$file = get_template_directory() . '/' . $dir . '/' . $filename;
		}
	} else {
		$child_file = get_stylesheet_directory() . '/inc/' . $filename;

		if ( file_exists( $child_file ) ) {
			$file = $child_file;
		} else {
			$file = get_template_directory() . '/inc/' . $filename;
		}
	}

	return $file;
}

function direo_requires( $filename, $dir = false ) {
	require_once direo_get_file_path( $filename, $dir );
}

function direo_optimized_css( $css ) {
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
	$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), ' ', $css );

	return $css;
}

function direo_dynamic_style() {

	$dynamic_css = '';
	ob_start();
	direo_requires( 'dynamic-styles/frontend.php' );
	$dynamic_css .= ob_get_clean();
	$dynamic_css = direo_optimized_css( $dynamic_css );

	wp_add_inline_style( 'direo-style', $dynamic_css );
}


// Map Header Title by JS
add_action( 'wp_ajax_direo_map_header_title', 'wp_ajax_direo_map_header_title' );
add_action( 'wp_ajax_nopriv_direo_map_header_title', 'wp_ajax_direo_map_header_title' );

function wp_ajax_direo_map_header_title() {
	$post_id = isset( $_POST['post_id'] ) ? esc_attr( $_POST['post_id'] ) : get_the_ID();
	$js_data = isset( $_POST['form'] ) ? $_POST['form'] : '';
	echo direo_lwm_get_the_title( $post_id, $js_data );
	wp_die();
}

// Removing the 'directorist-inline-style' to prevent CSS conflict
add_filter( 'directorist_load_inline_style', '__return_false' );