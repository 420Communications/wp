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

$attr = '';
$default_types	 = $settings['default_types'];
$types           = $settings['types'] ? implode( ',', $settings['types'] ) : '';
$header          = $settings['header'];
$show_pagination = $settings['show_pagination'];
$title           = $settings['title'];
$section_title   = $settings['section_title'];
$view_more_label = $settings['view_more_label'];
$filter          = 'yes' == $settings['filter'] ? $settings['filter'] : 'no';
$sidebar         = $settings['sidebar'];
$layout          = $settings['layout'];
$number_cat      = $settings['number_cat'];
$row             = $settings['row'];
$cat             = $settings['cat'] ? implode( ',', $settings['cat'] ) : '';
$location        = $settings['location'] ? implode( ',', $settings['location'] ) : '';
$tag             = $settings['tag'] ? implode( ',', $settings['tag'] ) : '';
$featured        = $settings['featured'];
$popular         = $settings['popular'];
$order_by        = $settings['order_by'];
$order_list      = $settings['order_list'];
$map_height      = $settings['map_height'];
$zoom_level      = $settings['zoom_level'] ? $settings['zoom_level']['size'] : '';
$user            = $settings['user'];
$web             = 'yes' === $user ? $settings['link']['url'] : '';
$btn             = $settings['view_more_url'];

if ( ! empty( $btn['url'] ) ) {
	$attr  = 'href="' . $settings['view_more_url']['url'] . '"';
	$attr .= ! empty( $settings['view_more_url']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= ! empty( $settings['view_more_url']['nofollow'] ) ? ' rel="nofollow"' : '';
}

if ( 'carousel' === $layout ) {
	add_filter( 'all_listings_wrapper', 'all_listings_wrapper' );
	add_filter( 'all_listings_column', function(){ return ''; } );
	if ( $section_title || $view_more_label ) { ?>
		<div class="all_listing_header">
			<h1><?php echo esc_attr( $section_title ); ?></h1>
			<a <?php echo wp_kses_post( $attr ); ?>> <?php echo esc_attr( $view_more_label ); ?> </a>
		</div>
		<?php
	}
}
?>

<div id="<?php echo esc_attr( 'listing-' . $layout ); ?>">
	<?php echo do_shortcode( '[directorist_all_listing map_zoom_level="' . esc_attr( $zoom_level ) . '" view="' . esc_attr( $layout ) . '" orderby="' . esc_attr( $order_by ) . '" order="' . esc_attr( $order_list ) . '" listings_per_page="' . esc_attr( $number_cat ) . '" category="' . esc_attr( $cat ) . '" tag="' . esc_attr( $tag ) . '" location="' . esc_attr( $location ) . '" featured_only="' . esc_attr( $featured ) . '" popular_only="' . esc_attr( $popular ) . '" header="' . esc_attr( $header ) . '" header_title ="' . esc_attr( $title ) . '" columns="' . esc_attr( $row ) . '" action_before_after_loop="' . esc_attr( $sidebar ) . '" show_pagination="' . esc_attr( $show_pagination ) . '" advanced_filter="' . esc_attr( $filter ) . '" map_height="' . $map_height . '" display_preview_image="yes" logged_in_user_only="' . esc_attr( $user ) . '" redirect_page_url="' . esc_attr( $web ) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]' ); ?>
</div>
