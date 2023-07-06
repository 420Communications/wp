<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sidebar    = $listings->action_before_after_loop;
$grid_class = ( is_active_sidebar( 'all_listing' ) && $sidebar ) ? 'col-lg-8 col-md-12' : '';
$row        = ( is_active_sidebar( 'all_listing' ) && $sidebar ) ? 'row' : '';
?>

<div class="directorist-archive-list-view <?php echo $row; ?>">

	<?php if ( $sidebar ) { ?>
		<div class="col-lg-4 order-lg-0 order-1 mt-5 mt-lg-0 atbd_sidebar">
			<?php dynamic_sidebar( 'all_listing' ); ?>
		</div>
	<?php } ?>

	<div class="<?php echo $grid_class ? $grid_class : Helper::directorist_container_fluid(); ?>">

		<?php if ( $listings->have_posts() ) : ?>

			<?php foreach ( $listings->post_ids() as $listing_id ) : ?>

				<?php $listings->loop_template( 'list', $listing_id ); ?>

			<?php endforeach; ?>

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
