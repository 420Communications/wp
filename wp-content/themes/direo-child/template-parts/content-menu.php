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

$sticky_class    = ( 'sticky' === $sticky ) || ! $sticky ? ' fixed-top headroom menu-area-sticky' : ' menu-top';

$template        = 'dashboard.php' === basename( get_page_template() ) ? false : true;

$menu_class      = is_single() || $template && ! changed_header_footer() ? $sticky_class : '';

$menu_style      = get_post_meta( direo_page_id(), 'style', true );

$menu_box        = 'style2' === $menu_style ? ' menu2' : '';

$button_url      = ! empty( $btn_custom_url ) ? esc_url( $btn_custom_url ) : esc_url( ATBDP_Permalink::get_add_listing_page_link() );



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

										<?php if ( has_nav_menu('primary') ) : ?>

											<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#direo-navbar-collapse" aria-controls="direo-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">

												<span class="navbar-toggler-icon icon-menu">

													<i class="la la-reorder"></i>

												</span>

											</button>

										<?php endif; ?>

									</div>

									<div class="collapse navbar-collapse" id="direo-navbar-collapse">

										<div class="mobile-close-icon">

											<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#direo-navbar-collapse" aria-controls="direo-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">

												<span class="navbar-toggler-icon icon-menu">

													<i class="la la-reorder"></i>

												</span>

											</button>

										</div>

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

												<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-xs btn-gradient btn-gradient-two text-capitalize py-2 rounded"><span class="la la-plus mr-1"></span><?php echo esc_attr( $add_listing_btn ); ?></a>

											</div>

										<?php } ?>

									</div>

									<div class="i-nav-overlay"></div>

								</nav>

							</div>

						</div>

						

						<div class="menu-right order-lg-2 order-sm-2">



							<?php if ( is_Directorist() ) { ?>



								<div class="author-area">

									<div class="author__access_area">

										<ul class="d-flex list-unstyled align-items-center author_access_list">

											<?php

											if ( is_user_logged_in() && $template ) {

												?>

												<li>

													<div class="author-info">

														<?php

														$avatar_img = get_avatar( get_current_user_id(), 40, null, null, array( 'class' => 'rounded-circle' ) );

														if ( empty( $author_img ) ) {

															echo wp_kses_post( $avatar_img );

														} else {

															echo sprintf( '<img width="40" src="%s" alt="%s" class="avatar rounded-circle"/>', esc_url( $author_img[0] ), direo_get_image_alt( $author_id ) );

														}

														?>



														<?php echo get_direo_dashboard_navigation(); ?>



													</div>

												</li>

												<?php

											}



											if ( ! is_user_logged_in() && ( ! atbdp_is_page( 'login' ) && ! atbdp_is_page( 'registration' ) ) && $quick_log_reg ) {

												?>

												<li class="d-lg-block d-none desktop-login">

													<?php

													$login_btn_class = ( 'style1' === $menu_style ) || ! $menu_style ? 'access-link' : 'btn btn-xs border';

													if ( $login_url ) {

														echo sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $login_url ), esc_attr( $login_btn_class ), esc_attr( $login ) );

													} else {

														echo sprintf( '<a href="#" class="%s" data-toggle="modal" data-target="#login_modal">%s</a>', esc_attr( $login_btn_class ), esc_attr( $login ) );

													}

													if ( ( 'style1' === $menu_style ) || ! $menu_style ) {

														echo sprintf( '<span>%s</span>', esc_html__( 'or', 'direo' ) );

														if ( $register_url ) {

															echo sprintf( '<a href="%s" class="access-link">%s</a>', esc_url( $register_url ), esc_attr( $register ) );

														} else {

															echo sprintf( '<a href="#" class="access-link" data-toggle="modal" data-target="#signup_modal">%s</a>', esc_attr( $register ) );

														}

													}

													?>

												</li>

												<?php

											}

											if ( $add_listing_btn ) {

												?>

												<li>

													<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-xs btn-gradient btn-gradient-two">

													<?php

													echo ( ( 'style1' === $menu_style ) || ! $menu_style ) ? '<span class="la la-plus mr-1"></span>' : '';

													echo esc_attr__( $add_listing_btn, 'direo' );

													?>

													</a>

												</li>

												<?php

											}

											?>

										</ul>

									</div>

								</div>



							<?php } ?>



							<?php if ( ! is_user_logged_in() && is_Directorist() && $quick_log_reg ) { ?>

								<div class="mobile-add-listing d-lg-none mx-2">

									<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-xs btn-gradient btn-gradient-two text-capitalize py-2 rounded"><span class="la la-plus mr-1"></span><?php echo esc_attr( $add_listing_btn ); ?></a>

								</div>

								<div class="mobile-login d-lg-none d-block ml-sm-4 ml-2">

									<a href="<?php echo site_url('login'); ?>" class="access-link"><span class="la la-user"></span></a>

								</div>

							<?php } ?>

						</div>

						

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

