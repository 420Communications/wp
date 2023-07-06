<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DireoDemo_Importer {

	protected static $instance;

	public function __construct() {
		// Link from plugins page
		add_filter(
			'plugin_action_links_wpwax-demo-importer/wpwax-demo-importer.php',
			function ( $links ) {
				$mylinks = array(
					'<a href="' . esc_url( admin_url( 'tools.php?page=fw-backups-demo-content' ) ) . '">' . __( 'Install Demo Contents', 'direo-core' ) . '</a>',
				);
				return array_merge( $links, $mylinks );
			}
		);

		// Confirmation Text
		add_filter(
			'wpwax_demo_importer_confirmation',
			function () {
				$text = esc_html__( 'IMPORTANT: Installing this demo will delete all existing data and contents of your website, so use it only in fresh website. Do you want to continue?', 'direo-core' );
				return $text;
			}
		);

		// Warning Text
		add_filter(
			'wpwax_demo_importer_warning',
			function () {
				$html  = '<div style="margin-top:20px;color:#f00;font-size:20px;line-height:1.3;font-weight:600;margin-bottom:40px;border-color: #f00;border-style: dashed;border-width: 1px 0;padding:10px 0;">';
				$html .= __( 'Warning: All your old data will be lost if you install demo data from here, so please use it only in new website.', 'direo-core' );
				$html .= '</div>';
				return $html;
			}
		);

		add_filter( 'fw:ext:backups-demo:demos', array( $this, 'demo_importer_config' ) );
		add_action( 'fw:ext:backups:tasks:success:id:demo-content-install', array( $this, 'execute_after_importing_demo' ) );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function demo_importer_config( $demos ) {
		$demos_array = array(
			'demo1' => array(
				'title'        => __( 'Home Static', 'direo-core' ),
				'screenshot'   => 'http://demo.directorist.com/theme/demo-content/direo/direo.jpg',
				'preview_link' => 'http://demo.directorist.com/theme/direo/',
			),
		);

		$remote_server_url = 'http://demo.directorist.com/theme/demo-content/direo';

		foreach ( $demos_array as $id => $data ) {
			$demo = new FW_Ext_Backups_Demo(
				$id,
				'piecemeal',
				array(
					'url'     => $remote_server_url,
					'file_id' => $id,
				)
			);
			$demo->set_title( $data['title'] );
			$demo->set_screenshot( $data['screenshot'] );
			$demo->set_preview_link( $data['preview_link'] );

			$demos[ $demo->get_id() ] = $demo;

			unset( $demo );
		}

		return $demos;
	}

	public function execute_after_importing_demo( $collection ) {
		// Update front page id
		$demos = array(
			'demo1' => 2004,
		);

		$data = $collection->to_array();

		foreach ( $data['tasks'] as $task ) {
			if ( $task['id'] == 'demo:demo-download' ) {
				$demo_id = $task['args']['demo_id'];
				$page_id = $demos[ $demo_id ];
				update_option( 'page_on_front', $page_id );
				flush_rewrite_rules();
				break;
			}
		}
		// Update post author id
		global $wpdb;
		$id    = get_current_user_id();
		$query = "UPDATE $wpdb->posts SET post_author = $id";
		$wpdb->query( $query );
	}
}

DireoDemo_Importer::instance();

/*
=====================================================
	One click demo config
=================================================*/
function direo_import_files() {
	 return array(
		 array(
			 'import_file_name'           => 'Demo',
			 'import_file_url'            => 'http://demo.directorist.com/theme/demo-content/direo/content.xml',
			 'import_widget_file_url'     => 'http://demo.directorist.com/theme/demo-content/direo/widgets.wie',
			 'import_customizer_file_url' => 'http://demo.directorist.com/theme/demo-content/direo/customizer.dat',
			 'import_notice'              => __( 'After you import this demo, You can deactivate the "One Click Demo Import" plugin.', 'direo-core' ),
		 ),
	 );
}

add_filter( 'pt-ocdi/import_files', 'direo_import_files' );

// Assign menus to their locations.

function direo_after_import_setup() {
	$main_menu = get_term_by( 'name', 'primary menu', 'nav_menu' );
	if ( isset( $main_menu->term_id ) && $main_menu->term_id > 0 ) {
		set_theme_mod(
			'nav_menu_locations',
			array(
				'primary' => $main_menu->term_id,
			)
		);
	}

	update_option( 'show_on_front', 'page' );

	$front_page_id = get_page_by_title( 'Home' );
	$blog_page_id  = get_page_by_title( 'Blogs' );
	update_option( 'page_on_front', $front_page_id->ID );
	update_option( 'page_for_posts', $blog_page_id->ID );
}

add_action( 'pt-ocdi/after_import', 'direo_after_import_setup' );
