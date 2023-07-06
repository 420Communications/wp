<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) {
	return;
}

/*Direo menu area*/
$author_id       = get_user_meta( get_current_user_id(), 'pro_pic', true );
$author_img      = wp_get_attachment_image_src( $author_id );
$add_listing_btn = get_theme_mod( 'add_listing_btn', 'Add Listing' );
$btn_custom_url  = get_theme_mod( 'add_btn_url' );
$quick_log_reg   = get_theme_mod( 'quick_log_reg', true );
$login           = get_theme_mod( 'login_btn', 'Login' );
$login_url       = get_theme_mod( 'login_btn_url', false );
$register        = get_theme_mod( 'register_btn', 'Register' );
$register_url    = get_theme_mod( 'register_btn_url', false );
$sticky          = get_post_meta( direo_page_id(), 'menu_type', true );
$sticky_class    = ( 'sticky' === $sticky ) || ! $sticky ? ' fixed-top headroom menu-area-sticky' : ' fixed-top';
$template        = 'dashboard.php' === basename( get_page_template() ) ? false : true;
$menu_class      = is_single() || $template && ! changed_header_footer() ? $sticky_class : '';
$menu_style      = get_post_meta( direo_page_id(), 'style', true );
$menu_box        = 'style2' === $menu_style ? ' menu2' : '';
$add_listing_url = class_exists( 'Directorist_Base' ) ? ATBDP_Permalink::get_add_listing_page_link() : '';
$button_url      = ! empty( $btn_custom_url ) ? esc_url( $btn_custom_url ) : esc_url( $add_listing_url );

if ( ! direo_menu_style() || ( 'menu1' === direo_menu_style() ) ) {
	$menu_type = 'menu--light menu--transparent';
} elseif ( 'menu3' === direo_menu_style() ) {
	$menu_type = 'menu--light bg-dark';
} else {
	$menu_type = 'menu--dark';
}
?>

<div class="menu-area menu1 <?php echo esc_html( $menu_type . $menu_class . $menu_box ); ?>">

	<div class="top-menu-area">

		<div class="container<?php echo 'style2' !== $menu_style ? esc_attr( '-fluid' ) : ''; ?>" >

			<div class="row">

				<div class="col-lg-12">

					<div class="menu-fullwidth">

						<?php direo_site_identity(); ?>

						<div class="menu-container order-lg-1 order-sm-0">

							<div class="d_menu">

								<nav class="navbar navbar-expand-lg mainmenu__menu">

									<div class="desktop-close-icon">

										<?php if ( has_nav_menu('primary') && class_exists( 'Directorist_Base' ) ) : ?>
											<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#direo-navbar-collapse" aria-controls="direo-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
												<span class="navbar-toggler-icon icon-menu"><?php directorist_icon( 'las la-bars' ); ?></span>
											</button>
										<?php endif; ?>

									</div>

									<div class="collapse navbar-collapse" id="direo-navbar-collapse">

										<?php if ( class_exists( 'Directorist_Base' ) ) : ?>
											<div class="mobile-close-icon">
												<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#direo-navbar-collapse" aria-controls="direo-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
													<span class="navbar-toggler-icon icon-menu">
													<?php directorist_icon( 'las la-bars' ); ?>
													</span>
												</button>
											</div>
										<?php endif; ?>

										<?php
										wp_nav_menu(
											array(
												'theme_location' => 'primary',
												'container' => false,
												'fallback_cb' => false,
												'menu_class' => 'navbar-nav',
												'depth' => 3,
											)
										);
										?>

										<?php  if ( is_Directorist() && $add_listing_btn ) { ?>
											<div class="mobile-add-listing d-lg-none d-block mx-2">
												<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-xs btn-gradient btn-gradient-two text-capitalize py-2 rounded">
													<?php directorist_icon( 'la la-plus mr-1' ); ?><?php echo esc_attr( $add_listing_btn ); ?>
												</a>
											</div>
										<?php } ?>

									</div>
									<div class="i-nav-overlay"></div>
								</nav>
							</div>
						</div>

						<?php echo direo_get_template_part( 'directorist/custom/directorist-header-options' ); ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>