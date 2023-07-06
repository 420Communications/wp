<?php
use Elementor\Plugin as Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


final class DireoWidgets {
	const VERSION                   = '1.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION       = '5.6';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		// Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'init_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'direo_widget_category' ) );
	}

	public function init_widgets() {
		require_once __DIR__ . '/widgets.php';

		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Accordion() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_AddListing_Form() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Blogs() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Logos() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_ContactForm() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Counter() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_FeatureBox() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Testimonial() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Heading() );
		Plugin::instance()->widgets_manager->register_widget_type( new Direo_Subscribe() );
		Plugin::instance()->widgets_manager->register_widget_type( new CTA() );

		if ( class_exists( 'Directorist_Base' ) ) {
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Categories() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Checkout() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Profile() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_ContactItems() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Dashboard() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Listings() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_ListingsCarousel() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_ListingsMap() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Locations() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Login() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Registration() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Booking_Confirmation() );
			if ( class_exists( 'Post_Your_Need' ) ) {
				Plugin::instance()->widgets_manager->register_widget_type( new Direo_NeedCategories() );
				Plugin::instance()->widgets_manager->register_widget_type( new Direo_NeedLocations() );
				Plugin::instance()->widgets_manager->register_widget_type( new Direo_NeedSingleCat() );
				Plugin::instance()->widgets_manager->register_widget_type( new Direo_NeedSingleLoc() );
				Plugin::instance()->widgets_manager->register_widget_type( new Direo_Needs() );
			}
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Payment() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_PricingPlan() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SearchForm() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SearchResult() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SearchResultMap() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleCat() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleCatMap() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleLoc() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleLocMap() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleTag() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_SingleTagMap() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Transaction() );
			Plugin::instance()->widgets_manager->register_widget_type( new Direo_Compare() );
		}
	}

	public function direo_widget_category( $manager ) {
		$manager->add_category(
			'direo_category',
			array(
				'title' => __( 'Direo', 'direo-core' ),
			)
		);
	}
}

DireoWidgets::instance();
