<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.2
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		?>
		<tr data-id="<?php the_ID(); ?>">
			<?php do_action( 'directorist_dashboard_listing_td_start', $dashboard ); ?>
			<td>
				<div class="directorist-listing-table-listing-info">
					<?php if( 1 == 2) { ?>
						<div class="directorist-listing-table-listing-info__img">
							<a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $dashboard->get_listing_thumbnail() ); ?></a>
						</div>
					<?php } ?>
					<div class="directorist-listing-table-listing-info__content">
						<h4 class="directorist-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<?php Helper::listing_price(); ?>
					</div>
				</div>
				<div class="directorist-actions" style="margin-top: 10px;">
					<a href="<?php echo esc_url(ATBDP_Permalink::get_edit_listing_page_link(get_the_ID())); ?>" class="directorist-link-btn"><i class="la la-edit"></i><?php esc_html_e( 'Edit', 'directorist' ); ?></a>
				</div>
			</td>
			<?php do_action( 'directorist_dashboard_listing_td_end', $dashboard ); ?>
		</tr>
		<?php
	}
	wp_reset_postdata();
}

else {
	?>
	<tr><td colspan="5"><?php esc_html_e( 'Location details not added.', 'directorist' ); ?></td></tr>
	<?php

}