<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

$i          = 1;
$order_by   = $settings['order_by'];
$order_list = $settings['order_list'];
$number_cat = $settings['number_cat'];
$slug       = 'slug' === $order_by ? $settings['slug'] : '';
$args       = array(
	'slug'          => $slug,
	'orderby'       => $order_by,
	'order'         => $order_list,
	'parent'        => 0,
	'number'        => $number_cat,
	'hide_empty'    => true,
	'taxonomy'      => ATBDP_CATEGORY,
	'no_found_rows' => true,
);
$categories = get_categories( $args );
?>

<?php if ( $categories ) { ?>
	<div class="category-slider owl-carousel">
		<?php
		foreach ( $categories as $cat ) {
			$link      = is_directorist() ? \ATBDP_Permalink::atbdp_get_category_page( (object) $cat ) : '';
			$icon      = get_cat_icon( $cat->term_id );
			$icon      = 'none' !== $icon ? $icon : 'la-tag';
			$icon_type = substr( $icon, 0, 2 );
			$icon      = ( 'la' === $icon_type ) ? $icon_type . ' ' . $icon : 'fa ' . $icon;
			$cat_count = atbdp_all_listings_meta_count( '', $cat );
			$bg_color  = ' color-' . $i;
			?>

			<div class="category-slider__single">
				<span class="category-icon <?php echo esc_attr( $icon . $bg_color ); ?>" aria-hidden="true"></span>
				<a href="<?php echo esc_url( $link ); ?>" class="stretched-link"><?php echo esc_attr( $cat->name ); ?></a>
				<?php echo wp_kses_post( $cat_count ); ?>
			</div>

			<?php
			$i++;
			$i = $i > 6 ? $i = 1 : $i;
		}
		?>
	</div>
	<?php
} else {
	?>
	<div class="alert alert-warning w-100" role="alert"><?php _e( 'No location found!', 'direo-core' ); ?></div>
	<?php
}
