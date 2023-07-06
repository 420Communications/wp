<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\RestApi;

class PageController {

	private $assets;

	private $data_api;

	public function __construct( AssetsApi $assets, AssetDataApi $data_api ) {
		$this->assets   = $assets;
		$this->data_api = $data_api;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'admin_enqueue_scripts', [ $this, 'add_assets' ] );
		add_action( 'wc_ppcp_admin_section_main', [ $this, 'render_main_page' ] );
		add_action( 'wc_ppcp_admin_section_support', [ $this, 'render_support_page' ] );
	}

	public function add_assets() {
		if ( $this->is_main_page() ) {
			$this->assets->enqueue_style( 'wc-ppcp-main', 'build/css/admin-main.css' );
		}
	}

	private function is_main_page() {
		return isset( $_GET['page'] ) && $_GET['page'] === 'wc-ppcp-main';
	}

	private function is_initialize_install() {
		return $this->is_main_page() && isset( $_GET['wc_ppcp_init'] );
	}

	public function render_main_page() {
		$assets = $this->assets;
		if ( $this->is_initialize_install() ) {
			wp_enqueue_style( 'woocommerce_admin_styles' );
			$this->assets->enqueue_script( 'wc-ppcp-admin-install', 'build/js/admin-install.js', [ 'wc-backbone-modal' ] );
			include_once __DIR__ . '/Views/html-activation-tmpl.php';
		}
		include_once __DIR__ . '/Views/html-main-page.php';
	}

	public function render_support_page() {
		$user = wp_get_current_user();
		$this->data_api->print_data( 'wcPPCPSupportParams', [
			'report' => $this->get_status_report(),
			'name'   => $user->get( 'first_name' ) . ' ' . $user->get( 'last_name' ),
			'email'  => $user->get( 'user_email' )
		] );
		$this->assets->enqueue_script( 'wc-ppcp-admin-commons', 'build/js/admin-commons.js' );
		$this->assets->enqueue_script( 'wc-ppcp-help-widget', 'build/js/help-widget.js' );
		$assets = $this->assets;
		include_once __DIR__ . '/Views/html-support-page.php';
	}

	private function get_status_report() {
		$report = wc()->api->get_endpoint_data( '/wc/v3/system_status' );
		if ( ! is_wp_error( $report ) ) {
			unset( $report['subscriptions']['payment_gateway_feature_support'] );
		} else {
			$report = array();
		}

		return $report;
	}

}