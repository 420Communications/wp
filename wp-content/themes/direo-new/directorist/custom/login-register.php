<?php
/**
 * @author  wpWax
 * @since   1.0
 * @version 1.0
 */

use wpWax\direo\Helper;

if ( atbdp_is_page( 'login' ) || atbdp_is_page( 'registration' ) ) {
	return;
}

$login_button = get_directorist_option( 'log_button', __( 'Sign In', 'direo' ) );
?>

<div class="theme-authentication-modal">
	<!-- Modal -->
	<div class="modal fade" id="theme-login-modal" tabindex="-1" role="dialog" aria-hidden="true">

		<div class="modal-dialog modal-dialog-centered" role="document">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title" id="theme-login-modal_label"><?php echo esc_attr( $login_button );?></h5>

					<button type="button" class="close theme-close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span></button>
				
				</div>

				<div class="modal-body">
					<?php echo direo_get_template_part( 'directorist/custom/login-modal-fields' ); ?>
				</div>

			</div>

		</div>

	</div>

	<div class="modal fade" id="theme-register-modal" role="dialog" aria-hidden="true">

		<div class="modal-dialog modal-dialog-centered">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title"><?php esc_attr_e( 'Registration', 'direo' ); ?></h5>

					<button type="button" class="theme-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span> </button>

				</div>

				<div class="modal-body">

					<?php 

		
					$user_type = ! empty( $atts['user_type'] ) ? $atts['user_type'] : '';
					$user_type = ! empty( $_REQUEST['user_type'] ) ? $_REQUEST['user_type'] : $user_type;
					
					$args = array(
						'parent'               => 0,
						'container_fluid'      => is_directoria_active() ? 'container' : 'container-fluid',
						'username'             => get_directorist_option( 'reg_username', __( 'Username', 'direo' ) ),
						'password'             => get_directorist_option( 'reg_password', __( 'Password', 'direo' ) ),
						'display_password_reg' => get_directorist_option( 'display_password_reg', 1 ),
						'require_password'     => get_directorist_option( 'require_password_reg', 1 ),
						'email'                => get_directorist_option( 'reg_email', __( 'Email', 'direo' ) ),
						'display_website'      => get_directorist_option( 'display_website_reg', 0 ),
						'website'              => get_directorist_option( 'reg_website', __( 'Website', 'direo' ) ),
						'require_website'      => get_directorist_option( 'require_website_reg', 0 ),
						'display_fname'        => get_directorist_option( 'display_fname_reg', 0 ),
						'first_name'           => get_directorist_option( 'reg_fname', __( 'First Name', 'direo' ) ),
						'require_fname'        => get_directorist_option( 'require_fname_reg', 0 ),
						'display_lname'        => get_directorist_option( 'display_lname_reg', 0 ),
						'last_name'            => get_directorist_option( 'reg_lname', __( 'Last Name', 'direo' ) ),
						'require_lname'        => get_directorist_option( 'require_lname_reg', 0 ),
						'display_bio'          => get_directorist_option( 'display_bio_reg', 0 ),
						'bio'                  => get_directorist_option( 'reg_bio', __( 'About/bio', 'direo' ) ),
						'require_bio'          => get_directorist_option( 'require_bio_reg', 0 ),
						'reg_signup'           => get_directorist_option( 'reg_signup', __( 'Sign Up', 'direo' ) ),
						'display_login'        => get_directorist_option( 'display_login', 1 ),
						'login_text'           => get_directorist_option( 'login_text', __( 'Already have an account? Please login', 'direo' ) ),
						'login_url'            => ATBDP_Permalink::get_login_page_link(),
						'log_linkingmsg'       => get_directorist_option( 'log_linkingmsg', __( 'Here', 'direo' ) ),
						'terms_label'          => get_directorist_option( 'regi_terms_label', __( 'I agree with all', 'direo' ) ),
						'terms_label_link'     => get_directorist_option( 'regi_terms_label_link', __( 'terms & conditions', 'direo' ) ),
						't_C_page_link'        => ATBDP_Permalink::get_terms_and_conditions_page_url(),
						'privacy_page_link'    => ATBDP_Permalink::get_privacy_policy_page_url(),
						'privacy_label'        => get_directorist_option( 'registration_privacy_label', __( 'I agree to the', 'direo' ) ),
						'privacy_label_link'   => get_directorist_option( 'registration_privacy_label_link', __( 'Privacy & Policy', 'direo' ) ),
						'user_type'			   => $user_type,
						'author_checked'	   => ( 'general' != $user_type ) ? 'checked' : '',
						'general_checked'	   => ( 'general' == $user_type ) ? 'checked' : ''
					);

					echo direo_get_template_part( 'directorist/custom/registration-modal-fields', $args );?>

				</div>

			</div>

		</div>

	</div>

</div>