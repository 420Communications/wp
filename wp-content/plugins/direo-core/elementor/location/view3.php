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

$number_loc = $settings['number_loc'];
$order_by   = $settings['order_by'];
$order_list = $settings['order_list'];
$slug       = 'slug' === $order_by ? $settings['slug'] : '';

$args = array(
	'slug'          => $slug,
	'orderby'       => $order_by,
	'order'         => $order_list,
	'parent'        => 0,
	'number'        => $number_loc,
	'hide_empty'    => true,
	'taxonomy'      => ATBDP_LOCATION,
	'no_found_rows' => true,
);

$locations = get_categories( $args );
?>

<div class="locations_carousel owl-carousel">
	<?php
	if ( $locations ) {
		foreach ( $locations as $loc ) {
			$child_id   = get_term_children( $loc->term_id, $loc->taxonomy );
			$child_name = $child_id ? get_term( $child_id[0] )->name : '';
			$location   = $child_name ? $child_name : $loc->name;
			$link       = is_directorist() ? \ATBDP_Permalink::atbdp_get_location_page( (object) $loc ) : '';
			$image_id   = get_term_meta( $loc->term_id, 'image', true );
			$image      = $image_id ? wp_get_attachment_image_src( $image_id, 'findbiz-popular-cat' ) : '';
			$image      = ! empty( $image ) ? esc_url( $image[0] ) : '';
			$count      = atbdp_all_listings_meta_count( '', $loc );
			?>
			<div class="lm-single">
				<figure>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( direo_get_image_alt( $image_id ) ); ?>">
					<figcaption>
						<div class="lm-single_content">
							<?php if ( $child_name ) { ?>
								<span class="sub-location"><?php echo esc_attr( $loc->name ); ?></span>
							<?php } ?>
							<div>
								<a href="<?php echo esc_url( $link ); ?>" class="stretched-link"><?php echo esc_attr( $loc->name ); ?> <i class="la la-long-arrow-right"></i></a>
								<?php echo wp_kses_post( $count ); ?>
							</div>
						</div>
					</figcaption>
				</figure>
			</div>
			<?php
		}
	} else {
		?>
		<div class="alert alert-warning w-100" role="alert"><?php _e( 'No location found!', 'direo-core' ); ?></div>
		<?php
	}
	?>
</div>
