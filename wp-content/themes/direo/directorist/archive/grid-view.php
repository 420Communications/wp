<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.0
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$sidebar    = $listings->action_before_after_loop;
$grid_class = ( is_active_sidebar( 'all_listing' ) && $sidebar ) ? 'col-lg-8 col-md-12' : '';
$row        = ( is_active_sidebar( 'all_listing' ) && $sidebar ) ? 'row' : '';
ob_start(); Helper::directorist_column( $listings->columns );
$column = ob_get_clean();
?>

<div class="directorist-archive-items directorist-archive-grid-view <?php echo $row; ?>">

	<?php do_action( 'directorist_before_grid_listings_loop' ); ?>

	<?php if ( $sidebar ) { ?>
		<div class="col-lg-4 order-lg-0 order-1 mt-5 mt-lg-0 atbd_sidebar">
			<?php dynamic_sidebar( 'all_listing' ); ?>
		</div>
	<?php } ?>

	<div class="<?php echo $grid_class ? $grid_class : Helper::directorist_container_fluid(); ?>">

		<?php if ( $listings->have_posts() ) : ?>

			<div class="<?php echo $listings->has_masonry() ? 'directorist-masonry' : ''; ?> <?php echo apply_filters( 'all_listings_wrapper', Helper::directorist_row() ); ?>">

				<?php foreach ( $listings->post_ids() as $listing_id ) : ?>

					<div class="<?php echo apply_filters( 'all_listings_column', $column ); ?> directorist-all-listing-col">
						<?php $listings->loop_template( 'grid', $listing_id ); ?>
					</div>

				<?php endforeach; ?>

			</div>

			<?php
			if ( $listings->show_pagination ) {
				$listings->pagination();
			}
			?>

		<?php else : ?>

			<div class="directorist-archive-notfound"><?php esc_html_e( 'No listings found.', 'direo' ); ?></div>

		<?php endif; ?>
	</div>

</div>
