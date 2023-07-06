<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

require_once 'inc/migration.php';
require_once 'inc/customizer.php';
require_once 'inc/comment_form.php';
require_once 'inc/direo-helper.php';
require_once 'inc/directorist_support.php';
require_once 'lib/tgm/plugin_ac.php';

function direo_setup() {
	load_theme_textdomain( 'direo', get_theme_file_path( '/languages' ) );
	add_image_size( 'direo_blog', 730, 413, true );
	add_image_size( 'direo_blog_grid', 350, 224, true );
	add_image_size( 'direo_related_blog', 223, 136, true );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style-editor.css' );
	remove_theme_support( 'widgets-block-editor' );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'direo' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	add_theme_support(
		'custom-background',
		apply_filters(
			'direo_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 40,
			'width'       => 140,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Editor Color Palette.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Primary', 'direo' ),
				'slug'  => 'primary',
				'color' => '#FF367E',
			),
			array(
				'name'  => __( 'Secondary', 'direo' ),
				'slug'  => 'title',
				'color' => '#903af9',
			),
			array(
				'name'  => __( 'Heading', 'direo' ),
				'slug'  => 'subtitle',
				'color' => '#272b41',
			),
			array(
				'name'  => __( 'Text', 'direo' ),
				'slug'  => 'text',
				'color' => '#7a82a6',
			),
		)
	);

	$GLOBALS['content_width'] = apply_filters( 'direo_content_width', 640 );
}

add_action( 'after_setup_theme', 'direo_setup' );

/*Register widget area. */
function direo_sidebar_register() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'All Listing ', 'direo' ),
			'id'            => 'all_listing',
			'description'   => esc_html__( 'It will display on the left side of the All Listing element.', 'direo' ),
			'before_widget' => '<div class="widget atbd_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widget-header atbd_widget_title"><h6 class="widget-title">',
			'after_title'   => '</h6></div>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Page', 'direo' ),
			'id'            => 'shop_sidebar',
			'description'   => esc_html__( 'Appears in the shop page sidebar.', 'direo' ),
			'before_widget' => '<div class="widget widget-wrapper %2$s"><div class="widget-default">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<div class="widget-header"><h6 class="widget-title">',
			'after_title'   => '</h6> </div>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog', 'direo' ),
			'id'            => 'blog_sidebar',
			'description'   => esc_html__( 'Appears in the blog page sidebar.', 'direo' ),
			'before_widget' => '<div class="widget widget-wrapper %2$s"><div class="widget-default">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<div class="widget-header"><h6 class="widget-title">',
			'after_title'   => '</h6> </div>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'direo' ),
			'id'            => 'page_sidebar',
			'description'   => esc_html__( 'Appears in the page sidebar.', 'direo' ),
			'before_widget' => '<div class="widget widget-wrapper %2$s"><div class="widget-default">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<div class="widget-header"><h6 class="widget-title">',
			'after_title'   => '</h6></div>',
		)
	);

	$footer_widget_titles = array(
		'1' => esc_html__( 'Footer 1', 'direo' ),
		'2' => esc_html__( 'Footer 2', 'direo' ),
		'3' => esc_html__( 'Footer 3', 'direo' ),
		'4' => esc_html__( 'Footer 4', 'direo' ),
	);

	foreach ( $footer_widget_titles as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => 'footer_sidebar_' . $id,
				'before_widget' => '<div class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}

add_action( 'widgets_init', 'direo_sidebar_register' );

/* Register custom fonts. */
function direo_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'arabic';

	if ( 'off' !== _x( 'on', 'Muli font: on or off', 'direo' ) ) {
		$fonts[] = 'Muli:400,400i,600,700';
	}
	if ( $fonts ) {
		$fonts_url = add_query_arg(
			array(
				'family' => implode( '|', $fonts ),
				'subset' => $subsets,
			),
			'https://fonts.googleapis.com/css'
		);
	}

	return esc_url_raw( $fonts_url );
}

/* Enqueue scripts and styles. */
function direo_scripts() {

	wp_enqueue_style( 'direo-fonts', direo_fonts_url(), array(), null );
	wp_enqueue_style( 'owl-carousel', get_theme_file_uri( 'vendor_assets/css/owl.carousel.min.css' ), array(), null );
	wp_enqueue_style( 'magnific-popup', get_theme_file_uri( 'vendor_assets/css/magnific-popup.css' ), array(), null );

	if ( is_rtl() ) {
		wp_enqueue_style( 'elementor-rtl', get_theme_file_uri( 'assets/css/elementor-rtl.css' ) );
		wp_enqueue_style( 'directorist-rtl', get_theme_file_uri( 'assets/css/directorist-rtl.css' ) );
		wp_enqueue_style( 'bootstrap-rtl', get_theme_file_uri( 'vendor_assets/css/bootstrap/bootstrap-rtl.css' ) );
		wp_enqueue_style( 'direo-rtl-style', get_theme_file_uri( 'assets/css/theme-style-rtl.css' ) );
	} else {
		wp_enqueue_style( 'direo-elementor', get_theme_file_uri( 'assets/css/elementor.css' ) );
		wp_enqueue_style( 'direo-directorist', get_theme_file_uri( 'assets/css/directorist.css' ) );
		wp_enqueue_style( 'bootstrap', get_theme_file_uri( 'vendor_assets/css/bootstrap/bootstrap.css' ) );
		wp_enqueue_style( 'direo-style', get_theme_file_uri( 'assets/css/style.css' ) );
	}

	//Custom color selection.
	direo_dynamic_style();

	wp_enqueue_script( 'popper', get_theme_file_uri( 'vendor_assets/js/bootstrap/popper.js' ), array( 'jquery' ), null, false );
	wp_enqueue_script( 'bootstrap', get_theme_file_uri( 'vendor_assets/js/bootstrap/bootstrap.min.js' ), array( 'jquery', 'popper' ), null, false );
	wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
	wp_enqueue_script( 'waypoints', get_theme_file_uri( 'vendor_assets/js/jquery.waypoints.min.js' ), array( 'jquery' ), null, true );
	wp_enqueue_script( 'counterup', get_theme_file_uri( 'vendor_assets/js/jquery.counterup.min.js' ), array( 'jquery' ), null, true );
	wp_enqueue_script( 'magnific-popup', get_theme_file_uri( 'vendor_assets/js/jquery.magnific-popup.min.js' ), array( 'jquery' ), null, true );
	wp_enqueue_script( 'carousel', get_theme_file_uri( 'vendor_assets/js/owl.carousel.min.js' ), array( 'jquery' ), null, true );
	wp_enqueue_script( 'headroom', get_theme_file_uri( 'vendor_assets/js/headroom.min.js' ), array( 'jquery' ), null, true );
	wp_enqueue_script( 'direo-main', get_theme_file_uri( 'theme_assets/js/main.js' ), array( 'jquery' ), null, true );

	$data = array(
		'rtl'     => is_rtl() ? 'true' : 'false',
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	);

	wp_localize_script( 'direo-main', 'direo_rtl', $data );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}

add_action( 'wp_enqueue_scripts', 'direo_scripts', 15 );

/* Admin Enqueue scripts and styles. */
function direo_admin_css_js() {
	wp_enqueue_style( 'direo-admin-css', get_theme_file_uri( 'theme_assets/admin.css' ), array(), null );
	wp_enqueue_script( 'direo-listing-image', get_theme_file_uri( 'theme_assets/listing-image.js' ), array( 'jquery' ), null, false );
}

add_action( 'admin_enqueue_scripts', 'direo_admin_css_js' );

// Deactivate direo-extension plugin
function direo_plugin_basename( $slug ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = get_plugins();

	$keys = array_keys( $plugins );

	foreach ( $keys as $key ) {
		if ( preg_match( '|^' . $slug . '/|', $key ) ) {
			return $key;
		}
	}

	return $slug;
}

function direo_deactivate_ext_bundle() {
	if ( defined( 'DOING_AJAX' ) ) {
		return;
	}

	$slug = 'direo-extension';
	$path = direo_plugin_basename( $slug );
	if ( class_exists( 'Direo_Plugins' ) ) {
		deactivate_plugins( $path );
	}
}

add_action( 'admin_init', 'direo_deactivate_ext_bundle' );

// Removing the 'directorist-inline-style' to prevent CSS conflict
add_filter( 'directorist_load_inline_style', '__return_false' );