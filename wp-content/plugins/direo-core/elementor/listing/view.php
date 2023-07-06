<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

if ( ! class_exists( 'Directorist_Base' ) ) {
	return;
}

$default_types	 = $settings['default_types'];
$types           = $settings['types'] ? implode( ',', $settings['types'] ) : '';
$list_num = $settings['list_num'];
$featured = $settings['featured'];
$popular = $settings['popular'];
add_filter( 'all_listings_wrapper', 'all_listings_wrapper' );
add_filter( 'all_listings_column', function(){ return ''; } );
?>

<div id="listing-carousel-2" data-carousel-items="3" data-carousel-loop="false" data-carousel-autoplay="true" data-carousel-delay="2000">
	<?php echo do_shortcode( '[directorist_all_listing view="grid" header="no" columns="6" action_before_after_loop="no" show_pagination="no" display_preview_image="yes" listings_per_page="' . esc_attr( $list_num ) . '" directory_type="' . $types . '" featured_only="' . esc_attr( $featured ) . '" popular_only="' . esc_attr( $popular ) . '" default_directory_type="' . $default_types . '"]' ); ?>
</div>
