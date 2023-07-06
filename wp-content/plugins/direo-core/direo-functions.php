<?php
/*
Plugin Name: Direo Core
Plugin URI: https://directorist.com/
Description: Core plugin of direo.
Author: wpWax
Author URI: https://wpwax.com
Domain Path: /languages
Text Domain: direo-core
Version: 2.12
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function deactive_direo_core() {
    $my_theme = wp_get_theme();
    if ( ( 'Direo' !== $my_theme->name ) || ( 'Direo' !== $my_theme->parent_theme  ) ) {
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

add_action( 'after_setup_theme', 'deactive_direo_core');

function deactivate_direo_core_plugin() {

	if ( ! function_exists( 'dservice_core_textdomain' ) ) {
		deactivate_plugins( 'dservice-core/dservice-functions.php');
	}
}

//add_action( 'after_setup_theme', 'deactivate_direo_core_plugin' );

function direo_core_textdomain() {
	$plugin_rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'direo-core', false, $plugin_rel_path );
}

add_action( 'plugins_loaded', 'direo_core_textdomain' );

require_once plugin_dir_path( __FILE__ ) . 'inc/custom-widgets.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/directorist-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/demo-importer.php';
require_once plugin_dir_path( __FILE__ ) . 'elementor/direo-elementor.php';

/* Plugin scripts */
function direo_core_scripts() {
	wp_enqueue_script( 'typed', plugin_dir_url( __FILE__ ). 'assets/js/typed.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'direo-core-main', plugin_dir_url( __FILE__ ). 'assets/js/main.js', array( 'jquery' ), null, true );

	wp_localize_script(
		'vb_reg_script',
		'vb_reg_vars',
		array(
			'vb_ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'direo_core_scripts' );

/* Single listing header image */
function direo_image_uploader_field( $name, $value = '' ) {
	$image     = ' button">Upload image';
	$image_size = 'full';
	$display    = 'none';

	if ( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
		$image   = '"><img src="' . $image_attributes[0] . '" style="max-width:100%;display:block;" />';
		$display = 'inline-block';
	}

	return '<div> <a href="#" class="direo_upload_image_button' . $image . '</a>
				<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
				<a href="#" class="direo_remove_image_button" style="display:inline-block;display:' . $display . '">' . esc_html__( 'Remove image', 'direo-core' ) . '</a>
				<p>' . __( 'upload listing header image <i style="color: #fa8b0c;">[1920*500]</i>', 'direo-core' ) . '</p>
			</div>';
}

function direo_meta_box_add() {
	add_meta_box( 'direodiv', esc_html__( 'Header Image', 'direo-core' ), 'direo_print_box', array( 'at_biz_dir', 'product' ), 'side', 'default' );
}

add_action( 'admin_menu', 'direo_meta_box_add' );

function direo_print_box( $post ) {
	$meta_key = 'second_featured_img';
	echo direo_image_uploader_field( $meta_key, get_post_meta( $post->ID, $meta_key, true ) );
}

add_action( 'save_post', 'direo_save' );

function direo_save( $post_id ) {
	$new_meta_value = ( isset( $_POST['second_featured_img'] ) ? sanitize_html_class( $_POST['second_featured_img'] ) : '' );
	update_post_meta( $post_id, 'second_featured_img', $new_meta_value );
	return $post_id;
}

/* Page header control header option */
function wdm_add_meta_box() {
	add_meta_box( 'direo_menu', esc_html__( 'Header Options', 'direo-core' ), 'direo_meta_box_callback', 'page', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'wdm_add_meta_box' );

function direo_meta_box_callback( $post ) {
	wp_nonce_field( 'direo_meta_box', 'direo_meta_box_nonce' );
	$value          = get_post_meta( $post->ID, 'menu_style', true );
	$value_checked  = empty( $value ) ? 'checked' : '';
	$banner         = get_post_meta( $post->ID, 'banner_style', true );
	$banner_checked = empty( $banner ) ? 'checked' : '';
	$style          = get_post_meta( $post->ID, 'style', true );
	$style_checked  = empty( $style ) ? 'checked' : '';
	$type           = get_post_meta( $post->ID, 'menu_type', true );
	$type_checked   = empty( $type ) ? 'checked' : ''; ?>

	<p><label for="wdm_new_field"><b><?php _e( 'Menu Type', 'direo-core' ); ?></b></label></p>
	<input id="wdm_new_field" type="radio" name="type" value="sticky"
	<?php checked( $type, 'sticky' ); echo esc_attr( $type_checked ); ?> >
	<?php _e( 'Sticky Menu', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="type" value="fixed" <?php checked( $type, 'fixed' ); ?>>
	<?php _e( 'Fixed Menu', 'direo-core' ); ?> <br>

	<p><label for="wdm_new_field"> <b><?php esc_html_e( 'Menu Area', 'direo-core' ); ?></b> </label></p>
	<input id="wdm_new_field" type="radio" name="menu_styles" value="menu1"
	<?php checked( $value, 'menu1' ); echo esc_attr( $value_checked ); ?> >
	<?php esc_html_e( 'Default', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="menu_styles" value="menu2" <?php checked( $value, 'menu2' ); ?>>
	<?php esc_html_e( 'Light Background', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="menu_styles" value="menu3" <?php checked( $value, 'menu3' ); ?>>
	<?php esc_html_e( 'Dark Background', 'direo-core' ); ?> <br><br>

	<p><label for="wdm_new_field"><b><?php esc_html_e( 'Menu Style', 'direo-core' ); ?></b></label></p>
	<input id="wdm_new_field" type="radio" name="style" value="style1"
	<?php checked( $style, 'style1' ); echo esc_attr( $style_checked ); ?> >
	<?php esc_html_e( 'Full wide 1', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="style" value="style3" <?php checked( $style, 'style3' ); ?>>
	<?php esc_html_e( 'Full wide 2', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="style" value="style2" <?php checked( $style, 'style2' ); ?>>
	<?php esc_html_e( 'Menu box', 'direo-core' ); ?> <br><br>

	<p><label for="wdm_new_field2"><b><?php esc_html_e( 'Banner Area', 'direo-core' ); ?></b></label></p>
	<input id="wdm_new_field2" type="radio" name="banner_option" value="search" <?php checked( $banner, 'search' ); ?>>
	<?php esc_html_e( 'Search Form', 'direo-core' ); ?><br>
	<input id="wdm_new_field2" type="radio" name="banner_option" value="breadcrumb"
	<?php checked( $banner, 'breadcrumb' ); echo esc_attr( $banner_checked ); ?> >
	<?php esc_html_e( 'Breadcrumb', 'direo-core' ); ?><br>
	<input id="wdm_new_field2" type="radio" name="banner_option" value="banner_off" <?php checked( $banner, 'banner_off' ); ?>>
	<?php
	esc_html_e( 'Hide', 'direo-core' );
}

function direo_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['direo_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['direo_meta_box_nonce'], 'direo_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$new_meta_value    = ( isset( $_POST['menu_styles'] ) ? sanitize_html_class( $_POST['menu_styles'] ) : '' );
	$style             = ( isset( $_POST['style'] ) ? sanitize_html_class( $_POST['style'] ) : '' );
	$type              = ( isset( $_POST['type'] ) ? sanitize_html_class( $_POST['type'] ) : '' );
	$banner_meta_value = ( isset( $_POST['banner_option'] ) ? sanitize_html_class( $_POST['banner_option'] ) : '' );

	update_post_meta( $post_id, 'menu_style', $new_meta_value );
	update_post_meta( $post_id, 'style', $style );
	update_post_meta( $post_id, 'menu_type', $type );
	update_post_meta( $post_id, 'banner_style', $banner_meta_value );
}

add_action( 'save_post', 'direo_save_meta_box_data' );

/* Single listing header control option */
function direo_single_add_meta_box() {
	add_meta_box( 'direo_single_menu', esc_html__( 'Header Options', 'direo-core' ), 'direo_single_meta_box_callback', array( 'at_biz_dir', 'product', 'post' ), 'side', 'default' );
}

add_action( 'add_meta_boxes', 'direo_single_add_meta_box' );

function direo_single_meta_box_callback( $post ) {
	wp_nonce_field( 'direo_single_meta_box', 'direo_single_meta_box_nonce' );
	$post_id       = isset( $_GET['post'] ) ? (int) $_GET['post'] : '';
	$value         = get_post_meta( $post_id, 'menu_style', true );
	$type          = get_post_meta( $post->ID, 'menu_type', true );
	$type_checked  = empty( $type ) ? 'checked' : '';
	$style         = get_post_meta( $post->ID, 'style', true );
	$style_checked = empty( $style ) ? 'checked' : '';
	?>

	<p><label for="wdm_new_field"><b><?php _e( 'Menu Type', 'direo-core' ); ?></b></label></p>
	<input id="wdm_new_field" type="radio" name="type" value="sticky"
	<?php checked( $type, 'sticky' ); echo esc_attr( $type_checked ); ?>>
	<?php _e( 'Sticky Menu', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="type" value="fixed" <?php checked( $type, 'fixed' ); ?>>
	<?php _e( 'Fixed Menu', 'direo-core' ); ?> <br>

	<p><label for="direo_new_field"><b><?php _e( 'Menu Area', 'direo-core' ); ?></b></label></p>
	<input id="direo_new_field" type="radio" name="menu_styles" value="menu1" <?php checked( $value, 'menu1' ); ?> checked>
	<?php _e( 'Default', 'direo-core' ); ?><br>
	<input id="direo_new_field" type="radio" name="menu_styles" value="menu2" <?php checked( $value, 'menu2' ); ?>>
	<?php _e( 'Light Background', 'direo-core' ); ?><br>
	<input id="direo_new_field" type="radio" name="menu_styles" value="menu3" <?php checked( $value, 'menu3' ); ?>>
	<?php _e( 'Dark Background', 'direo-core' ); ?><br>

	<p><label for="wdm_new_field"><b><?php esc_html_e( 'Menu Style', 'direo-core' ); ?></b></label></p>
	<input id="wdm_new_field" type="radio" name="style" value="style1"
	<?php checked( $style, 'style1' ); echo esc_attr( $style_checked ); ?>>
	<?php esc_html_e( 'Full wide 1', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="style" value="style3" <?php checked( $style, 'style3' ); ?>>
	<?php esc_html_e( 'Full wide 2', 'direo-core' ); ?> <br>
	<input id="wdm_new_field" type="radio" name="style" value="style2" <?php checked( $style, 'style2' ); ?>>
	<?php esc_html_e( 'Menu box', 'direo-core' ); ?> <br><br>

	<?php
}

function direo_single_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['direo_single_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['direo_single_meta_box_nonce'], 'direo_single_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$new_meta_value = ( isset( $_POST['menu_styles'] ) ? sanitize_html_class( $_POST['menu_styles'] ) : '' );
	$type           = ( isset( $_POST['type'] ) ? sanitize_html_class( $_POST['type'] ) : '' );
	$style          = ( isset( $_POST['style'] ) ? sanitize_html_class( $_POST['style'] ) : '' );

	update_post_meta( $post_id, 'menu_style', $new_meta_value );
	update_post_meta( $post_id, 'menu_type', $type );
	update_post_meta( $post_id, 'style', $style );
}

add_action( 'save_post', 'direo_single_save_meta_box_data' );

/*Footer style*/
function direo_footer_style() {
	add_meta_box( 'direo_footer_style', esc_html__( 'Footer Options', 'direo-core' ), 'direo_footer_style_callback', array( 'at_biz_dir', 'product', 'post', 'page' ), 'side', 'default' );
}

add_action( 'add_meta_boxes', 'direo_footer_style' );

/*Footer style*/
function direo_footer_style_callback( $post ) {
	wp_nonce_field( 'direo_footer_meta_box', 'direo_footer_meta_box_nonce' );
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : '';
	$value   = get_post_meta( $post_id, 'footer_style', true );
	?>

	<p><label for="direo_new_field"><b><?php esc_html_e( 'Footer Area', 'direo-core' ); ?></b></label></p>
	<input id="direo_new_field" type="radio" name="footer_styles" value="light" <?php checked( $value, 'light' ); ?> checked><?php esc_html_e( 'Light Background', 'direo-core' ); ?><br>
	<input id="direo_new_field" type="radio" name="footer_styles" value="dark" <?php checked( $value, 'dark' ); ?>>
	<?php esc_html_e( 'Dark Background', 'direo-core' ); ?><br>
	<input type="radio" name="footer_styles" value="footer-hide" <?php checked( $value, 'footer-hide' ); ?>>
	<?php esc_html_e( 'Hide', 'direo-core' ); ?>

	<?php
}

function direo_footer_style_control( $post_id ) {
	if ( ! isset( $_POST['direo_footer_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['direo_footer_meta_box_nonce'], 'direo_footer_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$new_meta_value = ( isset( $_POST['footer_styles'] ) ? sanitize_html_class( $_POST['footer_styles'] ) : '' );

	update_post_meta( $post_id, 'footer_style', $new_meta_value );
}

add_action( 'save_post', 'direo_footer_style_control' );

/* New User registration */
function vb_reg_new_user() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'vb_new_user' ) ) {
		die( 'Ooops, something went wrong, please try again later.' );
	}

	$redirection_after_reg = get_directorist_option( 'redirection_after_reg' );
	$auto_login = get_directorist_option( 'auto_login' );
	$redirect_url = 'previous_page' == $redirection_after_reg ? wp_get_referer() : get_the_permalink( $redirection_after_reg );

	// Post values
	$username         = isset( $_POST['user'] ) ? $_POST['user'] : '';
	$email            = isset( $_POST['mail'] ) ? $_POST['mail'] : '';
	$pass             = isset( $_POST['pass'] ) ? $_POST['pass'] : '';
	$privacy_policy   = isset( $_POST['privacy_policy'] ) ? esc_attr( $_POST['privacy_policy'] ) : '';
	$require_password = class_exists( 'Directorist_Base' ) ? get_directorist_option( 'require_password_reg', 1 ) : '';
	$policy           = get_directorist_option( 'registration_privacy', 1 );
	$terms            = get_directorist_option( 'regi_terms_condition', 1 );
	$data = [];
	$data['state'] = true;
	if ( $require_password && ! $pass ) {
		$data['state'] = false;
		$data['message'] = __( 'Password field is required', 'direo-core' );
	}

	if ( ! $email ) {
		$data['state'] = false;
		$data['message'] = __( 'Email field is required', 'direo-core' );
	}

	if ( ! $username ) {
		$data['state'] = false;
		$data['message'] = __( 'Username field is required', 'direo-core' );
	}

	if ( $policy || $terms ) {
		if ( ! $privacy_policy ) {
			$data['state'] = false;
			$data['message'] = __( 'Make sure all the required fields are not empty!', 'direo-core' );
		}
	}

	/**
	 * IMPORTANT: You should make server side validation here!
	 */
	if( !empty( $data['state'] ) ){
		$generated_pass = wp_generate_password( 12, false );
		$password       = ! empty( $pass ) ? $pass : $generated_pass;
		$userdata       = array(
			'user_login' => $username,
			'user_email' => $email,
			'user_pass'  => $password,
		);
		$user_id        = wp_insert_user( $userdata );
		if ( ! is_wp_error( $user_id ) ) {
			update_user_meta( $user_id, '_atbdp_generated_password', $password );
			wp_new_user_notification($user_id, null, 'admin');
			ATBDP()->email->custom_wp_new_user_notification_email($user_id);
			
			$data['state'] 			= true;
			$data['message'] 		= __( 'Registration completed, redirecting..', 'direo-core' );
			$data['redirect_url'] 	= $redirect_url;

			if( ! empty( $auto_login ) ) {
				wp_clear_auth_cookie();
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id, true);
			}
		} else {
			$data['state'] = false;
			$data['message'] = $user_id->get_error_message();
		}
	}

	wp_send_json( $data );
}

add_action( 'wp_ajax_register_user', 'vb_reg_new_user' );
add_action( 'wp_ajax_nopriv_register_user', 'vb_reg_new_user' );

/* Login & Register Configuration */
if ( ! function_exists( 'direo_post_navigation' ) ) {
	function direo_post_navigation() {
		$categories_list = get_the_category_list( esc_html__( ', ', 'direo-core' ) );
		?>
		<div class="post-pagination">
			<div class="prev-post">
				<span><?php esc_html_e( 'Next Post:', 'direo-core' ); ?></span>
				<?php echo sprintf( '<a href="%s" class="title">%s</a>', esc_url( get_the_permalink( get_next_post() ) ), get_the_title( get_next_post() ) ); ?>
				<p>
					<span><?php echo direo_time_link(); ?></span>
					<?php
					esc_html_e( '- In', 'direo-core' );
					echo $categories_list ? $categories_list : '';
					?>
				</p>
			</div>

			<div class="next-post">
				<span><?php esc_html_e( 'Previous Post:', 'direo-core' ); ?></span>
				<?php echo sprintf( '<a href="%s" class="title">%s</a>', esc_url( get_the_permalink( get_previous_post() ) ), get_the_title( get_previous_post() ) ); ?>
				<p><span><?php echo direo_time_link(); ?></span>
					<?php 
					esc_html_e( '- In', 'direo-core' );
					echo $categories_list ? $categories_list : '';
					?>
				</p>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'direo_related_post' ) ) {
	function direo_related_post() {
		$categories = array();
		foreach ( get_the_category( get_the_ID() ) as $category ) {
			$categories[] = $category->term_id;
		};
		wp_reset_postdata();

		$args	= array(
			'post__not_in'        => array( get_the_ID() ),
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => 1,
			'tax_query'           => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $categories,
					'operator' => 'IN',
				),
			),
		);
		$related_posts = new WP_Query( $args );

		if ( count( $related_posts->posts ) != 0 ) {
			?>
			<div class="related-post m-top-60">
				<div class="related-post--title text-center">
					<h3><?php esc_html_e( 'Related Posts', 'direo-core' ); ?></h3>
				</div>
				<div class="row">
					<?php
					if ( $related_posts->have_posts() ) {
						while ( $related_posts->have_posts() ) {
							$related_posts->the_post();
							?>
							<div class="col-lg-4 col-sm-6">
								<div class="single-post">
									<?php the_post_thumbnail( 'direo_related_blog' ); ?>
									<?php the_title( sprintf( '<h6><a href="%s">', get_the_permalink() ), '</a></h6>' ); ?>
									<p>
										<span><?php echo direo_time_link(); ?></span>
										<?php
										esc_html_e( 'in ', 'direo-core' );
										echo get_the_category_list( esc_html__( ', ', 'direo-core' ) );
										?>
									</p>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'direo_share_post' ) ) {
	function direo_share_post() {
		?>
		<div class="social-share d-flex align-items-center">
			<span class="m-right-15"> <?php esc_html_e( 'Share Post:', 'direo-core' ); ?> </span>
			<ul class="social-share list-unstyled">
				<li>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" title="<?php esc_html_e( 'Facebook', 'direo-core' ); ?>">
						<i class="fab fa-facebook"></i>
					</a>
				</li>
				<li>
					<a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php echo htmlspecialchars( urlencode( html_entity_decode( get_the_title(), ENT_COMPAT, 'UTF-8' ) ), ENT_COMPAT, 'UTF-8' ); ?>" target="_blank" title="<?php esc_html_e( 'Tweet', 'direo-core' ); ?>">
						<i class="fab fa-twitter"></i>
					</a>
				</li>
				<li>
					<a href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>" target="_blank" title="<?php esc_html_e( 'LinkedIn', 'direo-core' ); ?>">
						<i class="fab fa-linkedin"></i>
					</a>
				</li>
			</ul>
		</div>
		<?php
	}
}

/* Author Social Profile */
if ( ! function_exists( 'direo_author_social_icon' ) ) {
	function direo_author_social_icon( $social ) {
		$social['twitter']     = esc_html__( 'Twitter Username', 'direo-core' );
		$social['google_plus'] = esc_html__( 'Google plus profile', 'direo-core' );
		$social['facebook']    = esc_html__( 'Facebook Profile', 'direo-core' );
		$social['linkedin']    = esc_html__( 'Linkedin Profile', 'direo-core' );

		return $social;
	}
	add_filter( 'user_contactmethods', 'direo_author_social_icon' );
}

if ( ! function_exists( 'direo_author_social' ) ) {
	function direo_author_social() {
		global $post;
		$facebook    = get_user_meta( $post->post_author, 'facebook', true );
		$twitter     = get_user_meta( $post->post_author, 'twitter', true );
		$linkedin    = get_user_meta( $post->post_author, 'linkedin', true );
		$google_plus = get_user_meta( $post->post_author, 'google_plus', true );

		if ( $facebook || $twitter || $linkedin || $google_plus ) {
			?>
			<ul class="list-unstyled social-basic">
				<?php
				if ( $facebook != '' ) {
					?>
					<li>
						<a href="<?php echo esc_url( $facebook ); ?>">
							<i class="fab fa-facebook"></i>
						</a>
					</li>
					<?php
				}
				if ( $twitter != '' ) {
					?>
					<li>
						<a href="<?php echo esc_url( $twitter ); ?>">
							<i class="fab fa-twitter"></i>
						</a>
					</li>
					<?php
				}
				if ( $linkedin !== '' ) {
					?>
					<li>
						<a href="<?php echo esc_url( $linkedin ); ?>">
							<i class="fab fa-linkedin-in"></i>
						</a>
					</li>
					<?php
				}
				if ( $google_plus !== '' ) {
					?>
					<li>
						<a href="<?php echo esc_url( $google_plus ); ?>">
							<i class="fab fa-google-plus-g"></i>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
	}
}

/* Blog post Tag and Category */
function direo_post_cats() {
	$categories_list = get_the_category_list( esc_html__( ', ', 'direo-core' ) );
	if ( $categories_list ) {
		echo ( '<li>' . esc_html__( 'in', 'direo-core' ) . ' ' . $categories_list . '</li>' );
	}
}

function direo_post_tags() {
	if ( get_the_tags() ) {
		?>
		<div class="tags"><?php the_tags( '<ul class="d-flex list-unstyled"><li>', '</li><li>', '</li></ul>' ); ?></div>
		<?php
	}
}

if ( ! function_exists( 'mail_desc' ) ) {
	function mail_desc() {
		$desc = __( '<strong>Login <a href="https://mailchimp.com" target="_blank">Mailchimp</a> > Profile > Audience > Create  Audience / select existing audience</strong><br> Then go to <strong>Signup forms > Embedded forms </strong> and scroll down then you will found <strong>Copy/paste onto your site</strong> textarea including some text. Copy the form action URL and paste it here. <b style="color: green;">[For more details follow theme docs: <a href="https://demo.directorist.com/documentation/theme/direo/subscribe/" target="_blank">Page Builder</a>]</b>', 'direo' );
		return $desc;
	}
}

function az_template( $template, $settings ) {
	$az_dir = plugin_dir_path( __FILE__ );
	$file   = $az_dir . $template . '.php';
	ob_start();
	include $file;
	echo ob_get_clean();
}

// blog post estimated reading time
function reading_time( $content = '' ) {
	$clean_content = strip_shortcodes( $content );
	$clean_content = strip_tags( $clean_content );
	$word_count    = str_word_count( $clean_content );
	$time          = ceil( $word_count / 250 );
	return $time;
}
