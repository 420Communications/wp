<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.0.5.3
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class = $listing->single_page_enabled() ? 'single-listing-custom-template' : '';
?>

<section class="listing-details-wrapper bgimage <?php echo esc_html( $class ); ?>">

	<?php direo_single_listing_header_background(); ?>

	<div class="listing-info content_above">

		<div class="container">

			<div class="row">

				<div class="col-lg-8 col-md-12 d-lg-block d-none">

					<?php $listing->quick_info_template(); ?>

					<div class="diero_single_listing_title">

						<?php if ( $display_title ): ?>
							<h1><?php echo esc_html( $listing->get_title() ); ?></h1>
						<?php endif; ?>

						<?php do_action( 'directorist_single_listing_after_title', $listing->id ); ?>

					</div>

					<?php if ( $display_tagline && $listing->get_tagline() ): ?>
						<p class="atbd_sub_title subtitle"><?php echo esc_html( $listing->get_tagline() ); ?></p>
					<?php endif; ?>

				</div>

				<div class="col-lg-4 d-flex align-items-end justify-content-start justify-content-md-end">

					<div class="atbd_listing_action_area">

						<?php $listing->quick_actions_template(); ?>

					</div>

				</div>

			</div>

		</div>

	</div>

</section>


<div class="listing-info listing-info-left content_above d-lg-none d-block">

	<div class="container">

		<div class="row">

			<div class="col-lg-8 col-md-12">

				<?php $listing->quick_info_template(); ?>

				<div class="diero_single_listing_title">

					<?php if ( $display_title ): ?>
						<h1><?php echo esc_html( $listing->get_title() ); ?></h1>
					<?php endif; ?>

					<?php do_action( 'directorist_single_listing_after_title', $listing->id ); ?>

				</div>

				<?php if ( $display_tagline && $listing->get_tagline() ): ?>
					<p class="atbd_sub_title subtitle"><?php echo esc_html( $listing->get_tagline() ); ?></p>
				<?php endif; ?>

			</div>

		</div>

	</div>

</div>