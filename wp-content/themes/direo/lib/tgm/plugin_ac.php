<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

require_once 'class-tgm-plugin-activation.php';

function direo_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => esc_html__( 'Directorist – Business Directory Plugin', 'direo' ),
			'slug'     => 'directorist',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Kirki', 'direo' ),
			'slug'     => 'kirki',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Elementor', 'direo' ),
			'slug'     => 'elementor',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Contact form - 7', 'direo' ),
			'slug'     => 'contact-form-7',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Direo Core', 'direo' ),
			'slug'     => 'direo-core',
			'source'   => 'direo-core.zip',
			'required' => true,
			'version'  => '2.12',
		),
		array(
			'name'     => esc_html__( 'WpWax Demo Importer', 'direo' ),
			'slug'     => 'wpwax-demo-importer',
			'source'   => 'http://demo.directorist.com/theme/demo-content/wpwax-demo-importer.zip',
			'required' => false,
		),

		// Directorist extensions
		array(
			'name'     => esc_html__( 'Directorist Business Hours', 'direo' ),
			'slug'     => 'directorist-business-hours',
			'source'   => 'directorist-business-hours.zip',
			'version'  => '2.7.3',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Listings with Map', 'direo' ),
			'slug'     => 'directorist-listings-with-map',
			'source'   => 'directorist-listings-with-map.zip',
			'version'  => '1.6.3',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Social Login', 'direo' ),
			'slug'     => 'directorist-social-login',
			'source'   => 'directorist-social-login.zip',
			'version'  => '1.2.1',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Pricing Plans', 'direo' ),
			'slug'     => 'directorist-pricing-plans',
			'source'   => 'directorist-pricing-plans.zip',
			'version'  => '2.0.10',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist PayPal Payment Gateway', 'direo' ),
			'slug'     => 'directorist-paypal',
			'source'   => 'directorist-paypal.zip',
			'version'  => '1.4.1',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Stripe Payment Gateway', 'direo' ),
			'slug'     => 'directorist-stripe',
			'source'   => 'directorist-stripe.zip',
			'version'  => '2.5.5',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Listing FAQs', 'direo' ),
			'slug'     => 'directorist-faqs',
			'source'   => 'directorist-faqs.zip',
			'version'  => '1.3.5',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Claim Listing', 'direo' ),
			'slug'     => 'directorist-claim-listing',
			'source'   => 'directorist-claim-listing.zip',
			'version'  => '1.4.9',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Google reCAPTCHA', 'direo' ),
			'slug'     => 'directorist-google-recaptcha',
			'source'   => 'directorist-google-recaptcha.zip',
			'version'  => '1.3.1',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist - Live Chat', 'direo' ),
			'slug'     => 'directorist-live-chat',
			'source'   => 'directorist-live-chat.zip',
			'version'  => '1.3.4',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Directorist Booking', 'direo' ),
			'slug'     => 'directorist-booking',
			'source'   => 'directorist-booking.zip',
			'version'  => '1.6.1',
			'required' => false,
		),
	);

	$config = array(
		'id'           => 'direo',
		'default_path' => get_template_directory() . '/lib/plugins/',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}

add_action( 'tgmpa_register', 'direo_register_required_plugins' );