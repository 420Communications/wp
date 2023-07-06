<?php
/**
 * @author  wpWax
 * @since   1.0
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$counter         = 1;
$dashboard_url   = get_permalink( get_directorist_option( 'user_dashboard' ) );
$dashboard_links = direo_dashboard_tabs();
$add_listing_btn = get_theme_mod( 'add_listing_btn', 'Add Listing' );
?>

<ul class="list-unstyled">

	<?php foreach ( $dashboard_links as $key => $value ) { ?>

		<li>
			<a href="<?php echo esc_url( $dashboard_url ) . '#active_' . $key; ?>">
				<span class="directorist_menuItem-text">
					<span class="directorist_menuItem-icon">
						<?php directorist_icon( $value['icon'] ); ?>
					</span>
					<?php echo wp_kses_post( $value['title'] ); ?>
				</span>
			</a>
		</li>

		<?php $counter++; ?>

	<?php } ?>

	<?php if ( $dashboard->user_can_submit() ) : ?>

		<li>
			<a href="<?php echo esc_url( ATBDP_Permalink::get_add_listing_page_link() ); ?>">
				<span class="directorist_menuItem-text">
					<span class="directorist_menuItem-icon"><?php directorist_icon( 'las la-plus' ); ?></span>
					<?php echo ! empty( $add_listing_btn ) ? esc_html( $add_listing_btn ) : ''; ?>
				</span>
			</a>
		</li>

	<?php endif; ?>

	<li>
		<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
			<span class="directorist_menuItem-text">
				<span class="directorist_menuItem-icon"><?php directorist_icon( 'las la-sign-out-alt' ); ?></span>
				<?php esc_html_e( 'Log Out', 'direo' ); ?>
			</span>
		</a>
	</li>

</ul>