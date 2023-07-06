<?php
/**
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

/*Direo menu area*/
$author_id       = get_user_meta( get_current_user_id(), 'pro_pic', true );
$author_img      = wp_get_attachment_image_src( $author_id );

$add_listing_btn = get_theme_mod( 'add_listing_btn', 'Add Listing' );
$btn_custom_url  = get_theme_mod( 'add_btn_url' );
$quick_log_reg   = get_theme_mod( 'quick_log_reg', true );
$login           = get_theme_mod( 'login_btn', 'Login' );
$login           = $login ? $login : get_directorist_option( 'log_button', __( 'Sign In', 'direo' ) );
$login_url       = get_theme_mod( 'login_btn_url', false );
$register        = get_theme_mod( 'register_btn', 'Register' );
$register_url    = get_theme_mod( 'register_btn_url', false );

$template        = 'dashboard.php' === basename( get_page_template() ) ? false : true;
$menu_style      = get_post_meta( direo_page_id(), 'style', true );
$button_url      = ! empty( $btn_custom_url ) ? esc_url( $btn_custom_url ) : esc_url( ATBDP_Permalink::get_add_listing_page_link() );
?>

<div class="menu-right order-lg-2 order-sm-2">

	<?php if ( is_Directorist() ) { ?>

		<div class="author-area">

			<div class="author__access_area">

				<ul class="d-flex list-unstyled align-items-center author_access_list">

					<?php
					if ( class_exists( 'woocommerce' ) && $template ) {
						echo direo_tiny_cart();
					}

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
								echo sprintf( '<a href="#" class="%s" data-toggle="modal" data-target="#theme-login-modal">%s</a>', esc_attr( $login_btn_class ), esc_attr( $login ) );
							}
							if ( ( 'style1' === $menu_style ) || ! $menu_style ) {
								echo sprintf( '<span>%s</span>', esc_html__( 'or', 'direo' ) );
								if ( $register_url ) {
									echo sprintf( '<a href="%s" class="access-link">%s</a>', esc_url( $register_url ), esc_attr( $register ) );
								} else {
									echo sprintf( '<a href="#" class="access-link" data-toggle="modal" data-target="#theme-register-modal" data-bs-dismiss="modal">%s</a>', esc_attr( $register ) );
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
							echo ( ( 'style1' === $menu_style ) || ! $menu_style ) ? directorist_icon( 'la la-plus mr-1' ) : '';
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
			<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-xs btn-gradient btn-gradient-two text-capitalize py-2 rounded">
				<?php directorist_icon( 'la la-plus mr-1' ); ?>
				<?php echo esc_attr( $add_listing_btn ); ?>
			</a>
		</div>
		<div class="mobile-login d-lg-none d-block ml-sm-4 ml-2">
			<a href="#" class="access-link" data-toggle="modal" data-target="#theme-login-modal">
				<?php directorist_icon( 'la la-user' ); ?>
			</a>
		</div>
	<?php } ?>

</div>