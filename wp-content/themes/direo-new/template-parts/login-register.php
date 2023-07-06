<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

if ( is_user_logged_in() ) {
	return;
}
$display_password_reg = is_Directorist() ? get_directorist_option( 'display_password_reg', 1 ) : '';
$social_login         = is_Directorist() ? get_directorist_option( 'enable_social_login', 1 ) : '';
$register             = get_theme_mod( 'register_btn', 'Register' );
$login                = get_theme_mod( 'login_btn', 'Login' );
?>

<div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="login_modal_label" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<div class="modal-title" id="login_modal_label"><?php directorist_icon( 'la la-lock' ); ?> <?php echo esc_attr( $login ); ?></div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<form action="login" id="direo-login" method="post">
					<input type="text" class="form-control" id="direo-username" name="username" placeholder="<?php echo esc_attr_x( 'Username or Email', 'placeholder', 'direo' ); ?>" required>
					<input type="password" id="direo-password" autocomplete="false" name="password" class="form-control" placeholder="<?php echo esc_attr_x( 'Password', 'placeholder', 'direo' ); ?>" required>
					<button class="btn btn-block btn-lg btn-gradient btn-gradient-two" type="submit" name="submit"><?php echo esc_attr( $login ); ?></button>
					<p class="status"></p>

					<div class="form-excerpts">
						<div class="keep_signed">
							<label for="direo-keep_signed_in" class="not_empty">
								<input type="checkbox" id="direo-keep_signed_in" value="1" name="keep_signed_in" checked="">
								<?php esc_html_e( 'Remember Me', 'direo' ); ?>
								<span class="cf-select"></span>
							</label>
						</div>
						<a href="" class="recover-pass-link"><?php esc_html_e( 'Forgot your password?', 'direo' ); ?></a>
					</div>

					<?php wp_nonce_field( 'ajax-login-nonce', 'direo-security' ); ?>

				</form>

				<form method="post" id="direo_recovery_password" class="recover-pass-form">
					<fieldset>
						<p> <?php esc_html_e( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'direo' ); ?> </p>
						<label for="user_login"><?php esc_html_e( 'E-mail:', 'direo' ); ?></label>
						<?php $user_login = isset( $_POST['user_login'] ) ? $_POST['user_login'] : ''; ?>
						<input type="text" name="direo_recovery_user" class="direo_recovery_user" id="user_login" value="<?php echo esc_attr( $user_login ); ?>" />
						<input type="hidden" name="action" value="reset" />
						<p class="recovery_status"></p>
						<button type="submit" class="btn btn-primary direo_recovery_password" id="direo-submit"><?php echo __( 'Get New Password', 'direo' ); ?></button>
					</fieldset>
				</form>

				<?php if ( $social_login ) { ?>
					<p class="social-connector text-center">
						<span><?php esc_html_e( 'Or connect with', 'direo' ); ?></span>
					</p>
					<div class="social-login">
						<?php do_action( 'atbdp_before_login_form_end' ); ?>
					</div>
					<?php
				}
				?>

				<div class="form-excerpts">
					<ul class="list-unstyled">
						<li>
							<?php esc_html_e( 'Not a member? ', 'direo' ); ?>
							<a href="<?php echo ATBDP_Permalink::get_registration_page_link(); ?>" class="access-link" data-toggle="modal" data-target="#signup_modal" data-dismiss="modal">
								<?php echo esc_attr( $register ); ?>
							</a>
						</li>
					</ul>
				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="signup_modal" tabindex="-1" role="dialog" aria-labelledby="signup_modal_label" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<div class="modal-title" id="signup_modal_label"> <?php directorist_icon( 'la la-lock' ); ?> <?php echo esc_attr( $register ); ?> </div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">

				<div class="vb-registration-form">
					<form class="form-horizontal registraion-form" role="form">
						<div class="form-group">
							<input type="email" name="vb_email" id="vb_email" value="" placeholder="<?php echo esc_html_x( 'Your Email', 'placeholder', 'direo' ); ?>" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="text" name="vb_username" id="vb_username" value="" placeholder="<?php echo esc_html_x( 'Choose Username', 'placeholder', 'direo' ); ?>" class="form-control" />
						</div>
						<?php if ( ! empty( $display_password_reg ) ) { ?>
							<div class="form-group">
								<input type="password" name="vb_password" id="vb_password" value="" placeholder="<?php echo esc_html_x( 'Password', 'placeholder', 'direo' ); ?>" class="form-control" />
							</div>
						<?php } ?>

						<?php
						wp_nonce_field( 'vb_new_user', 'vb_new_user_nonce', true, true );

						$t_C_page_link     = ATBDP_Permalink::get_terms_and_conditions_page_url();
						$privacy_page_link = ATBDP_Permalink::get_privacy_policy_page_url();
						$policy            = get_directorist_option( 'registration_privacy', 1 );
						$terms             = get_directorist_option( 'regi_terms_condition', 1 );
						$and               = $policy && $terms ? ' & ' : '';
						if ( $policy || $terms ) { ?>
							<div class="directory_regi_btn">
								<span class="atbdp_make_str_red"> *</span>
								<input id="privacy_policy" type="checkbox" name="privacy_policy">
								<label for="privacy_policy"> <?php _e( 'I agree to the', 'direo' ); ?>
									<?php if ( $policy ) { ?>
										<a style="color: red" target="_blank" href="<?php echo esc_url( $privacy_page_link ); ?>" id=""> <?php _e( 'Privacy', 'direo' ); ?> </a>
										<?php
									}
									
									echo esc_attr( $and );

									if ( $terms ) { ?>
										<a style="color: red" target="_blank" href="<?php echo esc_url( $t_C_page_link ); ?>" id="atbdp_reg_terms" <?php do_action( 'atbdp_reg_terms_a_attr' ); ?>>
										<?php _e( 'Terms', 'direo' ); ?>
										</a>
									<?php } ?>
								</label>
							</div>
						<?php } ?>

						<?php do_action('atbdp_before_user_registration_submit');?>

						<button type="submit" class="btn btn-block btn-lg btn-gradient btn-gradient-two" id="btn-new-user">
							<?php echo esc_attr( $register ); ?>
						</button>
					</form>
					<div class="indicator"><?php esc_html_e( 'Please wait...', 'direo' ); ?></div>
					<div class="alert result-message"></div>
				</div>
				<?php if ( $social_login ) { ?>
					<p class="social-connector text-center"><span><?php esc_html_e( 'Or connect with', 'direo' ); ?></span></p>
					<div class="social-login">
						<?php do_action( 'atbdp_before_login_form_end' ); ?>
					</div>
				<?php } ?>
				<div class="form-excerpts">
					<ul class="list-unstyled">
						<li><?php esc_html_e( 'Already a member? ', 'direo' ); ?>
							<a href="<?php echo ATBDP_Permalink::get_login_page_link(); ?>" class="access-link" data-toggle="modal" data-target="#login_modal" data-dismiss="modal"><?php echo esc_attr( $login ); ?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
