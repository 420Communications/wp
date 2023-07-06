<?php
/**
 * @author  WpWax
 * @since   1.4.1
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Direo_Migration {

	protected static $instance = null;

	public $migration_data;

	private function __construct() {
		$this->migration_data  = get_option( 'direo_migration' );
		
		$this->migration_v_2_8();
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function migration_v_2_8() {
		if ( !class_exists( 'Directorist_Base' ) ) {
			return;
		}

		if ( empty( $this->migration_data['v_2_8'] ) ) {
			add_action( 'admin_init', [ $this, 'swbd_sanitize_builder_data_structure' ] );
			$this->migration_data['v_2_8'] = true;
			update_option( 'direo_migration', $this->migration_data );
		}
	}

	public function swbd_sanitize_builder_data_structure() {
		$directory_types = get_terms([
			'taxonomy'   => ATBDP_DIRECTORY_TYPE,
			'hide_empty' => false,
		]);

		if ( empty( $directory_types ) ) { return; }

		foreach ( $directory_types as $directory_type ) {
			$this->swbd_sanitize_single_listings_contents_data_structure( $directory_type->term_id );
		}
	}

	public function swbd_sanitize_single_listings_contents_data_structure( $directory_type_id = 0 ) {
		$single_listings_contents = get_term_meta( $directory_type_id, 'single_listings_contents', true );
		
		if ( ! isset( $single_listings_contents['fields'] ) && ! isset( $single_listings_contents['groups'] ) ) return;
		if ( ! is_array( $single_listings_contents['groups'] ) ) return;
		
		$description = array(
			'type'			=>'section',
			'label'			=>'Listing Details',
			'fields'		=> array(),
			'icon'			=>'las la-align-justify',
			'widget_group'	=>'other_widgets',
			'widget_name'	=>'description'
		);

		array_unshift( $single_listings_contents[ 'groups'], $description );
		
		update_term_meta( $directory_type_id, 'single_listings_contents', $single_listings_contents );
	}
}

Direo_Migration::instance();