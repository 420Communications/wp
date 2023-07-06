<?php

/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

use \Directorist\Directorist_Single_Listing;

/*Listing Post category*/

if ( ! function_exists( 'direo_listing_category' ) ) {
	function direo_listing_category() {
		$categories = is_Directorist() ? get_terms( 'at_biz_dir-category' ) : '';
		$cat        = array();
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$cat[ $category->slug ] = $category->name;
			}
		}

		return $cat;
	}
}

/*  Listing Post Tag ============ */

if ( ! function_exists( 'direo_listing_tags' ) ) {
	function direo_listing_tags() {
		$tags = is_Directorist() ? get_terms( 'at_biz_dir-tags' ) : '';
		$tag  = array();
		if ( $tags ) {
			foreach ( $tags as $s_tag ) {
				$tag[ $s_tag->slug ] = $s_tag->name;
			}
		}

		return $tag;
	}
}

/*Listing Post Locations============ */
if ( ! function_exists( 'direo_listing_locations' ) ) {
	function direo_listing_locations() {
		$locations = is_Directorist() ? get_terms( 'at_biz_dir-location' ) : '';
		$loc       = array();
		if ( ! empty( $locations ) ) {
			foreach ( $locations as $s_loc ) {
				$loc[ $s_loc->slug ] = $s_loc->name;
			}
		}

		return $loc;
	}
}

/*Pagination For Blog*/
if ( ! function_exists( 'direo_pagination' ) ) {

	function direo_pagination( $wp_query = null ) {
		if ( ! $wp_query ) {
			$wp_query = $GLOBALS['wp_query'];
		}

		// Don't print empty markup if there's only one page.

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

		$left_icon  = class_exists( 'Directorist_Base' ) ? directorist_icon( 'las la-long-arrow-alt-left', false ) : '';
		$right_icon = class_exists( 'Directorist_Base' ) ? directorist_icon( 'las la-long-arrow-alt-right', false ) : '';

		$links = paginate_links(
			array(
				'base'      => $pagenum_link,
				'format'    => $format,
				'total'     => $wp_query->max_num_pages,
				'current'   => $paged,
				'mid_size'  => 3,
				'add_args'  => array_map( 'urlencode', $query_args ),
				'prev_text' => $left_icon,
				'next_text' => $right_icon,
			)
		);

		printf( '<div class="m-top-50"><nav class="navigation pagination d-flex justify-content-center" role="navigation"><div class="nav-links">%s</div></nav></div>', wp_kses_post( $links ) );
	}
}

/*Site Logo And Title*/
function direo_site_identity() {
	$logo_id     = get_theme_mod( 'custom_logo' );
	$logo_src    = $logo_id ? wp_get_attachment_image_src( $logo_id, 'full' )[0] : '';
	$logo_id2    = get_theme_mod( 'footer_logo' );
	$logo1       = $logo_id2 ? $logo_id2 : $logo_src;
	$logo2       = $logo_id ? $logo_src : $logo_id2;
	$logo_class  = ! $logo_id ? 'block' : '';
	$logo_class2 = ! $logo_id2 ? 'block' : '';

	if ( $logo_id || $logo_id2 ) { ?>
		<div class="logo-wrapper order-lg-0 order-sm-1">
			<div class="logo logo-top">
				<a class="navbar-brand order-sm-1 order-1" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
					if ( $logo_id ) {
						echo sprintf( '<img class="logo-dark%s" src="%s" alt="%s">', $logo_class2, esc_url( $logo1 ), direo_get_image_alt( $logo_id ) );
					}
					if ( $logo_id2 ) {
						echo sprintf( '<img class="logo-white%s" src="%s" alt="%s">', $logo_class, esc_url( $logo2 ), direo_get_image_alt( $logo_id ) );
					}
					?>
				</a>
			</div>
		</div>
		<?php
	} elseif ( get_bloginfo( 'name' ) ) {
		echo sprintf(
			'<div class="logo-wrapper order-lg-0 order-sm-1 site_title_tag">
                <div class="logo logo-top">
                    <h1 class="m-0"><a id="site_title_color" href="%s">%s</a></h1>',
			home_url( '/' ),
			get_bloginfo( 'name' )
		);
		echo sprintf( '<p id="site_tagline_color" class="m-0">%s</p></div> </div>', get_bloginfo( 'description' ) );
	}
}

/*Meta Info For Blog*/
if ( ! function_exists( 'direo_blog_meta_info' ) ) {
	function direo_blog_meta_info() {
		$blog_style = get_theme_mod( 'blog_style', 'default' );
		?>
		<ul class="post-meta list-unstyled">
			<li><?php echo direo_time_link(); ?></li>

			<?php
			echo 'default' == $blog_style ? sprintf( '<li>%s <a href="%s">%s</a></li>', esc_html__( 'by', 'direo' ), get_author_posts_url( get_the_author_meta( 'ID' ) ), get_the_author_meta( 'display_name' ) ) : '';

			if ( function_exists( 'direo_post_cats' ) ) {
				direo_post_cats();
			}

			if ( 'default' == $blog_style ) {
				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
					echo '<li>';
					comments_popup_link( esc_html__( 'No comments yet', 'direo' ), esc_html__( '1 comment', 'direo' ), esc_html__( '% comments', 'direo' ), 'comments-link', esc_html__( 'Comments are off', 'direo' ) );
					echo '</li>';
				}
			}
			?>

		</ul>
		<?php
	}
}

/*Contact Form Title And Id*/
if ( ! function_exists( 'mp_get_cf7_names' ) ) {
	function mp_get_cf7_names() {
		global $wpdb;
		$cf7_list = $wpdb->get_results(
			"SELECT ID, post_title
				FROM $wpdb->posts
				WHERE post_type = 'wpcf7_contact_form'"
		);
		$cf7_val  = array();
		if ( $cf7_list ) {
			$cf7_val[0] = esc_html__( 'Select a Contact Form', 'direo' );
			foreach ( $cf7_list as $value ) {
				$cf7_val[ $value->ID ] = $value->post_title;
			}
		} else {
			$cf7_val[0] = esc_html__( 'No contact forms found', 'direo' );
		}

		return $cf7_val;
	}
}

/*Remove Contact Form 7 Auto <p> Tag*/
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/*Listing Reviews*/
function direo_listing_review() {
	$enable_review = get_directorist_option( 'enable_review', 1 );
	if ( ! $enable_review ) {
		return;
	}

	global $post;
	$average = ATBDP()->review->get_average( $post->ID );
	echo sprintf( '<span class="atbd_meta atbd_listing_rating">%s %s</span>', wp_kses_post( $average ), directorist_icon( 'fa fa-star' ) );
}

/*Social Shares Buttons*/
if ( ! function_exists( 'direo_social_sharing_buttons' ) ) {
	function direo_social_sharing_buttons( $name ) {
		global $post;
		if ( is_singular( 'at_biz_dir' ) ) {
			$listingURL   = urlencode( get_permalink() );
			$listingTitle = str_replace( ' ', '%20', get_the_title() );

			$facebookURL = "https://www.facebook.com/share.php?u={$listingURL}&title={$listingTitle}";
			$twitterURL  = "http://twitter.com/share?url={$listingURL}";
			$linkedin    = "http://www.linkedin.com/shareArticle?mini=true&url={$listingURL}&title={$listingTitle}";
			if ( 'facebook' == $name ) {
				return esc_url( $facebookURL );
			}
			if ( 'twitter' == $name ) {
				return esc_url( $twitterURL );
			}
			if ( 'linkedin' == $name ) {
				return esc_url( $linkedin );
			}
		}
	}
}

/*Social Shares Buttons Congifaretaion*/

if ( ! function_exists( 'direo_sharing' ) ) {
	function direo_sharing() {
		?>
		<span class="dropdown-toggle" id="social-links" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
			<?php directorist_icon( 'la la-share' ); ?>
			<?PHP esc_html_e( 'Share', 'direo' ); ?>
		</span>

		<div class="atbd_director_social_wrap dropdown-menu" aria-labelledby="social-links">
			<ul class="list-unstyled">
				<li class="facebook">
					<a href="<?php echo direo_social_sharing_buttons( 'facebook' ); ?>" target="_blank">
						<?php directorist_icon( 'fab fa-facebook color-facebook' ); ?><?php esc_html_e( 'Facebook', 'direo' ); ?>
					</a>
				</li>
				<li class="twitter">
					<a href="<?php echo direo_social_sharing_buttons( 'twitter' ); ?>" target="_blank">
						<!-- twitter icon by Icons8 -->
						<?php directorist_icon( 'fab fa-twitter color-twitter' ); ?><?php esc_html_e( 'Twitter', 'direo' ); ?>
					</a>
				</li>
				<li class="linkedin">
					<a href="<?php echo direo_social_sharing_buttons( 'linkedin' ); ?>" target="_blank">
						<!-- linkedin icon by Icons8 -->
						<?php directorist_icon( 'fab fa-linkedin color-linkedin' ); ?><?php esc_html_e( 'LinkedIn', 'direo' ); ?>
					</a>
				</li>
			</ul>
			<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
		</div>
		<?php
	}
}

/*
Convert col decimal format to class
		Replace for King Composer plugin class*/

if ( ! function_exists( 'direo_column_width_class' ) ) {
	function direo_column_width_class( $width ) {
		if ( empty( $width ) ) {
			return 'col-md-12 col-sm-12';
		}

		if ( strpos( $width, '%' ) !== false ) {
			$width = (float) $width;
			if ( $width < 12 ) {
				return 'col-md-1 col-sm-6 col-xs-12';
			} elseif ( $width < 18 ) {
				return 'col-md-2 col-sm-6 col-xs-12';
			} elseif ( $width < 22.5 ) {
				return 'kc_col-of-5 float-none';
			} elseif ( $width < 29.5 ) {
				return 'col-md-3 col-sm-6 col-xs-12';
			} elseif ( $width < 37 ) {
				return 'col-md-4 col-sm-12';
			} elseif ( $width < 46 ) {
				return 'col-md-5 col-sm-12';
			} elseif ( $width < 54.5 ) {
				return 'col-md-6 col-sm-12';
			} elseif ( $width < 63 ) {
				return 'col-md-7 col-sm-12';
			} elseif ( $width < 71.5 ) {
				return 'col-md-8 col-sm-12';
			} elseif ( $width < 79.5 ) {
				return 'col-md-9 col-sm-12';
			} elseif ( $width < 87.5 ) {
				return 'col-md-10 col-sm-12';
			} elseif ( $width < 95.5 ) {
				return 'col-md-11 col-sm-12';
			} else {
				return 'col-md-12 col-sm-12';
			}
		}

		$matches     = explode( '/', $width );
		$width_class = '';
		$n           = 12;
		$m           = 12;

		if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
			$n = $matches[0];
		}

		if ( isset( $matches[1] ) && ! empty( $matches[1] ) ) {
			$m = $matches[1];
		}

		if ( 2.4 == $n ) {
			$width_class = 'kc_col-of-5';
		} else {
			if ( $n > 0 && $m > 0 ) {
				$value = ceil( ( $n / $m ) * 12 );
				if ( $value > 0 && $value <= 12 ) {
					$width_class = 'col-md-' . $value;
				}
			}
		}

		return $width_class;
	}
}

/*direo configuration*/
if ( ! function_exists( 'direo_remove_kc_element' ) ) {
	function direo_remove_kc_element() {
		/*==============auto loader==========*/

		$modules_path = get_template_directory() . '/module';

		foreach ( glob( $modules_path . '/*.php' ) as $module ) {
			load_template( $module, true );
		}

		/*=========Add custom icon pack [Line-awesome].==============*/

		if ( function_exists( 'kc_add_icon' ) ) {
			kc_add_icon( get_template_directory_uri() . '/vendor_assets/css/line-awesome.min.css' );
		}

		/*===========Removing direo default element=========*/

		if ( function_exists( 'kc_remove_map' ) ) {
			kc_remove_map( 'kc_accordion' );
			kc_remove_map( 'kc_button' );
			kc_remove_map( 'kc_call_to_action' );
			kc_remove_map( 'kc_blog_posts' );
			kc_remove_map( 'kc_carousel_post' );
			kc_remove_map( 'kc_testimonial' );
			kc_remove_map( 'kc_title' );
			kc_remove_map( 'kc_contact_form7' );
			kc_remove_map( 'kc_spacing' );
			kc_remove_map( 'kc_icon' );
			kc_remove_map( 'kc_counter_box' );
			kc_remove_map( 'kc_divider' );
			kc_remove_map( 'kc_column_text' );
			kc_remove_map( 'kc_image_gallery' );
			kc_remove_map( 'kc_flip_box' );
			kc_remove_map( 'kc_google_maps' );
			kc_remove_map( 'kc_pricing' );
			kc_remove_map( 'kc_box' );
			kc_remove_map( 'kc_progress_bars' );
			kc_remove_map( 'kc_video_play' );
			kc_remove_map( 'kc_pie_chart' );
			kc_remove_map( 'kc_twitter_feed' );
			kc_remove_map( 'kc_instagram_feed' );
			kc_remove_map( 'kc_fb_recent_post' );
			kc_remove_map( 'kc_team' );
			kc_remove_map( 'kc_carousel_images' );
			kc_remove_map( 'kc_post_type_list' );
			kc_remove_map( 'kc_coundown_timer' );
			kc_remove_map( 'kc_box_alert' );
			kc_remove_map( 'kc_feature_box' );
			kc_remove_map( 'kc_dropcaps' );
			kc_remove_map( 'kc_image_fadein' );
			kc_remove_map( 'kc_creative_button' );
			kc_remove_map( 'kc_tooltip' );
			kc_remove_map( 'kc_multi_icons' );
			kc_remove_map( 'kc_nested' );
			kc_remove_map( 'kc_image_hover_effects' );
			kc_remove_map( 'kc_tabs' );
		}
	}

	add_action( 'init', 'direo_remove_kc_element', 999 );
}

/*
Woocommerce Ajaxify cart
===================== */

function direo_tiny_cart() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}
	ob_start();
	?>
	<li>
		<div class="nav_right_module cart_module">
			<div class="cart__icon">
				<?php directorist_icon( 'la la-shopping-cart' ); ?>
				<span class="cart_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			</div>
			<div class="cart__items shadow-lg-2">
				<?php
				if ( ! empty( WC()->cart->get_cart_contents_count() ) ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>

							<div class="items">
								<div class="item_thumb">
									<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 42, 48 ) ), $cart_item, $cart_item_key ); ?>
								</div>
								<div class="item_info">
									<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), esc_attr( $_product->get_name() ) ), $cart_item, $cart_item_key ) ); ?>
									<span class="color-primary">
										<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
									</span>
								</div>
								<?php
								echo apply_filters(
									'woocommerce_cart_item_remove_link item_remove',
									sprintf(
										'<a href="%s" class="item_remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">%s</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'direo' ),
										esc_attr( $product_id ),
										esc_attr( $cart_item_key ),
										esc_attr( $_product->get_sku() ),
										directorist_icon( 'la la-close' )
									),
									$cart_item_key
								);
								?>
							</div>
							<?php
						}
					}
					?>

					<div class="cart_info text-md-right">
						<p><?php esc_html_e( 'Subtotal: ', 'direo' ); ?>
							<span class="color-primary"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
						</p>
						<?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?>
					</div>

					<?php
				} else {
					?>
					<div class="cart_info text-md-right">
						<p class="text-center">
							<b><?php esc_html_e( 'No products in the cart.', 'direo' ); ?></b>
						</p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</li>
	<?php

	return ob_get_clean();
}

if ( ! function_exists( 'direo_tiny_cart_filter' ) ) {
	function direo_tiny_cart_filter( $fragments ) {
		$fragments['.nav_right_module.cart_module'] = direo_tiny_cart();

		return $fragments;
	}

	add_filter( 'woocommerce_add_to_cart_fragments', 'direo_tiny_cart_filter' );
}

function direo_woo_shopping_cart() {
	if ( class_exists( 'woocommerce' ) ) {
		if ( is_shop() || is_product() || is_cart() || is_checkout() || is_product_taxonomy() || is_account_page() ) {
			return true;
		}
	}
}

// WordPress default year.
if ( ! function_exists( 'direo_time_link' ) ) {
	function direo_time_link() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		$time_string = sprintf(
			$time_string,
			get_the_date( DATE_W3C ),
			get_the_date(),
			get_the_modified_date( DATE_W3C ),
			get_the_modified_date()
		);

		return sprintf( '<a href="%s" rel="bookmark">%s</a>', esc_url( get_permalink() ), $time_string );
	}
}

/*
	Direo all image alt text
===================== */

if ( ! function_exists( 'direo_get_image_alt' ) ) {
	function direo_get_image_alt( $id = null ) {
		if ( is_object( $id ) || is_array( $id ) ) :

			if ( isset( $id['attachment_id'] ) ) :
				$post = get_post( $id['attachment_id'] );
				if ( is_object( $post ) ) :
					if ( $post->post_excerpt ) :
						return esc_attr( $post->post_excerpt );
					else :
						return esc_attr( $post->post_title );
					endif;
				endif;
			else :
				return false;
			endif;

		elseif ( $id > 0 ) :

			$post = get_post( $id );
			if ( is_object( $post ) ) :
				if ( $post->post_excerpt ) :
					return esc_attr( $post->post_excerpt );
				else :
					return esc_attr( $post->post_title );
				endif;
			endif;

		endif;
	}
}

/*Login and Register Button*/
if ( ! function_exists( 'direo_ajax_login_init' ) ) {
	function direo_ajax_login_init() {
		wp_enqueue_script( 'ajax-login-script', get_theme_file_uri( 'theme_assets/js/ajax-login-register-script.js' ), 'jquery', null, true );
		$display_password = ( is_Directorist() ) ? get_directorist_option( 'display_password_reg', 1 ) : '';
		$confirmation     = empty( $display_password ) ? __( ' Go to your inbox or spam/junk and get your password.', 'direo' ) : __( ' Congratulations! Registration completed.', 'direo' );
		wp_localize_script(
			'ajax-login-script',
			'direo_ajax_login_object',
			array(
				'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
				'redirecturl'               => ( is_Directorist() ) ? ATBDP_Permalink::get_login_redirection_page_link() : home_url( '/' ),
				'loadingmessage'            => esc_html__( 'Sending user info, please wait...', 'direo' ),
				'registration_confirmation' => esc_html( $confirmation ),
				'login_failed'              => esc_html__( 'Sorry! Login failed.', 'direo' ),
			)
		);

		add_action( 'wp_ajax_nopriv_direo_ajaxlogin', 'direo_ajax_login' );
		add_action( 'wp_ajax_nopriv_direo_recovery_password', 'direo_recovery_password' );
	}
}

if ( function_exists( 'direo_ajax_login_init' ) && ! is_user_logged_in() ) {
	add_action( 'init', 'direo_ajax_login_init' );
}

if ( ! function_exists( 'direo_recovery_password' ) ) {
	function direo_recovery_password() {
		global $wpdb;
		$error   = '';
		$success = '';
		$email   = trim( $_POST['user_login'] );
		if ( empty( $email ) ) {
			$error = esc_html__( 'Enter a username or e-mail address..', 'direo' );
		} elseif ( ! is_email( $email ) ) {
			$error = esc_html__( 'Invalid username or e-mail address.', 'direo' );
		} elseif ( ! email_exists( $email ) ) {
			$error = esc_html__( 'There is no user registered with that email address.', 'direo' );
		} else {
			$random_password = wp_generate_password( 12, false );
			$user            = get_user_by( 'email', $email );
			$update_user     = update_user_meta( $user->ID, '_atbdp_recovery_key', $random_password );

			// if  update user return true then lets send user an email containing the new password
			if ( $update_user ) {
				$subject = esc_html__( '	Password Reset Request', 'direo' );
				// $message = esc_html__('Your new password is: ', 'direo') . $random_password;

				$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				$message   = __( 'Someone has requested a password reset for the following account:', 'direo' ) . '<br>';
				/* translators: %s: site name */
				$message .= sprintf( __( 'Site Name: %s', 'direo' ), $site_name ) . '<br>';
				/* translators: %s: user login */
				$message .= sprintf( __( 'User: %s', 'direo' ), $user->user_login ) . '<br>';
				$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'direo' ) . '<br>';
				$message .= __( 'To reset your password, visit the following address:', 'direo' ) . '<br>';
				$link     = array(
					'key'  => $random_password,
					'user' => $email,
				);
				$message .= '<a href="' . esc_url( add_query_arg( $link, ATBDP_Permalink::get_login_page_url() ) ) . '">' . esc_url( add_query_arg( $link, ATBDP_Permalink::get_login_page_url() ) ) . '</a>';

				$message = atbdp_email_html( $subject, $message );

				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$mail      = wp_mail( $email, $subject, $message, $headers );
				if ( $mail ) {
					$success = __( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox.', 'direo' );
				} else {
					$error = __( 'Password updated! But something went wrong sending email.', 'direo' );
				}
			} else {
				$error = esc_html__( 'Oops something went wrong updaing your account.', 'direo' );
			}
		}

		if ( ! empty( $error ) ) {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => $error,
				)
			);
		}

		if ( ! empty( $success ) ) {
			echo json_encode(
				array(
					'loggedin' => true,
					'message'  => $success,
				)
			);
		}

		die();
	}
}

if ( ! function_exists( 'direo_ajax_login' ) ) {
	function direo_ajax_login() {
		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-login-nonce', 'security' );

		$username       = $_POST['username'];
		$user_password  = $_POST['password'];
		$keep_signed_in = ! empty( $_POST['rememberme'] ) ? true : false;
		$user           = wp_authenticate( $username, $user_password );
		if ( is_wp_error( $user ) ) {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => __(
						'Wrong username or password.',
						'direo'
					),
				)
			);
		} else {
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID, $keep_signed_in );
			echo json_encode(
				array(
					'loggedin' => true,
					'message'  => __(
						'Login successful, redirecting...',
						'direo'
					),
				)
			);
		}
		exit();
	}
}

/*Direo Page header image*/
if ( ! function_exists( 'direo_header_background' ) ) {
	function direo_header_background() {
		$post_thumbnail = '';
		$opacity        = get_theme_mod( 'bread_c_opacity', '8' );
		$bg_color       = get_theme_mod( 'header_bg_color' );

		$header_img_id = get_post_meta( get_the_ID(), 'second_featured_img', true );
		$header_img    = wp_get_attachment_image_src( $header_img_id, array( 1920, 500 ) );
		if ( class_exists( 'woocommerce' ) && is_shop() ) {
			$post_thumbnail = get_the_post_thumbnail_url( get_option( 'woocommerce_shop_page_id' ) );
		} else {
			$post_thumbnail = get_the_post_thumbnail_url();
		}
		$header_bg = get_theme_mod( 'bread_c_image', get_template_directory_uri() . '/img/breadcrumb1.jpg' );
		$home_page = get_the_post_thumbnail( get_option( 'page_for_posts' ) );

		if ( is_home() || is_archive() ) {
			if ( ! empty( $home_page ) ) {
				$section_bg = $home_page;
			} else {
				$section_bg = ! empty( $header_bg ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $header_bg ), get_the_title() ) : '';
			}
		} else {
			if ( ! empty( $post_thumbnail ) && ! is_single() ) {
				$section_bg = ! empty( $post_thumbnail ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $post_thumbnail ), get_the_title() ) : '';
			} elseif ( ! empty( $header_img ) ) {
				$section_bg = ! empty( $header_bg ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $header_img[0] ), get_the_title() ) : '';
			} else {
				$section_bg = ! empty( $header_bg ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $header_bg ), get_the_title() ) : '';
			}
		}

		echo $section_bg ? sprintf( '<div class="bg_image_holder">%s</div>', $section_bg ) : '';

		if ( $section_bg ) {
			?>
			<style>
				.overlay.overlay--dark:before {
					background: rgba(47, 38, 57, 0.<?php echo esc_attr( $opacity ); ?>);
				}
			</style>
			<?php
		} else {
			?>
			<style>
				.overlay.overlay--dark:before {
					background: <?php echo $bg_color; ?>;
				}
			</style>
			<?php
		}
	}
}

/*Direo single listing header image*/
function direo_single_listing_header_background() {
	$header_img_id = get_post_meta( get_the_ID(), 'second_featured_img', true );
	$image_id      = get_post_meta( get_the_ID(), '_listing_prv_img', true );
	$header_bg     = get_theme_mod( 'bread_c_image', get_template_directory_uri() . '/img/breadcrumb1.jpg' );
	$header_img    = wp_get_attachment_image_src( $header_img_id ) ? wp_get_attachment_image_src( $header_img_id, 'full' ) : '';
	$preview_image = wp_get_attachment_image_src( $image_id ) ? wp_get_attachment_image_src( $image_id, 'full' ) : '';

	$opacity  = get_theme_mod( 'bread_c_opacity', '8' );
	$bg_color = get_theme_mod( 'header_bg_color' );

	if ( ! empty( $header_img_id ) ) {
		$section_bg = ! empty( $header_img ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $header_img[0] ), get_the_title() ) : '';
	} elseif ( ! empty( $image_id ) ) {
		$section_bg = ! empty( $preview_image ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $preview_image[0] ), get_the_title() ) : '';
	} else {
		$section_bg = ! empty( $header_bg ) ? sprintf( '<img src="%s" alt="%s">', esc_url( $header_bg ), get_the_title() ) : '';
	}

	echo wp_kses_post( '<div class="bg_image_holder">' . $section_bg . '</div>' );

	if ( $section_bg ) {
		?>
		<style>
			.listing-details-wrapper:before {
				background: rgba(47, 38, 57, 0.<?php echo esc_attr( $opacity ); ?>);
			}
		</style>
		<?php
	} else {
		?>
		<style>
			.listing-details-wrapper:before {
				background: <?php echo esc_attr( $bg_color ); ?>;
			}
		</style>
		<?php
	}
}

function direo_menu_style() {
	$listing           = class_exists( 'Directorist_Base' ) ? Directorist_Single_Listing::instance() : '';
	$dashboardFileName = basename( get_page_template() );
	$style             = get_post_meta( direo_page_id(), 'menu_style', true );

	if ( ( is_singular('at_biz_dir') && $listing->single_page_enabled() ) || ( changed_header_footer() || ( 'dashboard.php' == $dashboardFileName ) ) && ( 'menu1' == $style || empty( $style ) ) ) {
		$style = 'menu2';
	} elseif ( is_single() && ( 'menu1' == $style || empty( $style ) ) ) {
		$style = get_theme_mod( 'menu_style', 'menu1' );
	}

	return $style;
}


/*Directorist page id check */
function direo_directorist_pages( $page_id ) {
	return is_Directorist() && ( is_page() && ( get_the_ID() === get_directorist_option( $page_id ) ) ) ? true : false;
}

/*direo Page ID*/
function direo_page_id() {
	$id = '';
	if ( class_exists( 'woocommerce' ) && is_shop() || class_exists( 'woocommerce' ) && is_product_taxonomy() ) {
		$id = wc_get_page_id( 'shop' );
	} elseif ( class_exists( 'woocommerce' ) && is_cart() ) {
		$id = wc_get_page_id( 'cart' );
	} elseif ( class_exists( 'woocommerce' ) && is_checkout() ) {
		$id = wc_get_page_id( 'checkout' );
	} elseif ( class_exists( 'woocommerce' ) && is_account_page() ) {
		$id = wc_get_page_id( 'myaccount' );
	} elseif ( class_exists( 'woocommerce' ) && is_home() || is_archive() ) {
		$id = get_option( 'page_for_posts' );
	} else {
		$id = get_the_ID();
	}
	return $id;
}

/*Count Popular Post*/
if ( ! function_exists( 'setPostViews' ) ) {
	function setPostViews( $postID ) {
		$countKey = 'post_views_count';
		$count    = get_post_meta( $postID, $countKey, true );
		if ( '' == $count ) {
			$count = 0;
			delete_post_meta( $postID, $countKey );
			add_post_meta( $postID, $countKey, '0' );
		} else {
			$count++;
			update_post_meta( $postID, $countKey, $count );
		}
	}
}

/*Check elementor is using*/
function is_elements() {
	global $post;
	$elementor_using = '';
	if ( in_array( 'elementor/elementor.php', (array) get_option( 'active_plugins' ) ) ) {
		$elementor_using = Elementor\Plugin::$instance->documents->get( $post->ID )->is_built_with_elementor();
	}

	$builder_meta = get_post_meta( get_the_ID(), 'kc_data', true );
	$kc_using     = ( $builder_meta ) ? $builder_meta['mode'] : '';
	if ( 'kc' == $kc_using ) {
		return true;
	} else {
		return $elementor_using;
	}
}

/*Pagination*/
function direo_page_pagination() {
	wp_link_pages(
		array(
			'before'   => '<div class="m-top-50"><nav class="navigation pagination d-flex justify-content-center" role="navigation"><div class="nav-links">',
			'after'    => '</div></nav></div>',
			'pagelink' => '<span class="page-numbers">%</span>',
		)
	);
}

/**
 * @since 1.10.3
 * @return void || HTML
 */
function direo_dashboard_notification() {
	if ( isset( $_GET['renew'] ) && ( 'token_expired' === $_GET['renew'] ) ) {
		?>
		<div class="alert alert-danger">
			<?php directorist_icon( 'la la-times-circle' ); ?>
			<?php _e( 'Link appears to be invalid.', 'direo' ); ?>
		</div>
		<?php
	}
	if ( isset( $_GET['renew'] ) && ( 'success' === $_GET['renew'] ) ) {
		?>
		<div class="alert alert-success">
			<?php directorist_icon( 'la la-check-circle' ); ?>
			<?php _e( 'Renewed successfully.', 'direo' ); ?>
		</div>
		<?php
	}
}

/* Checked listing with map view element */
function changed_header_footer() {
	$id      = preg_match( '/(listing-listings_with_map)/', get_post_field( 'post_content', get_the_ID() ) );
	$checked = ( 1 === $id ) ? true : false;
	if ( is_404() || is_search() ) {
		return false;
	} elseif ( $checked ) {
		return true;
	} else {
		return false;
	}
}

function direo_body_class( $class ) {
	$class[] = changed_header_footer() ? 'atbdp_listings_map_page_loading' : '';
	return $class;
}
add_filter( 'body_class', 'direo_body_class' );

function is_Directorist() {
	return class_exists( 'Directorist_Base' ) ? true : false;
}

/*skip setup widget*/
add_filter( 'atbdp_setup_wizard', '__return_false' );
/* Stopped auto page creation */
add_filter( 'atbdp_create_required_pages', '__return_false' );


function elementor_register_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
}

add_action( 'elementor/theme/register_locations', 'elementor_register_locations' );

function direo_get_page_title() {
	if ( class_exists( 'woocommerce' ) && is_shop()) {
		$title = get_the_title( wc_get_page_id( 'shop' ) );
	}
	elseif ( is_search() ) {
		$title = esc_html__( 'Search Results for : ', 'direo' ) . get_search_query();
	}
	elseif ( is_404() ) {
		$title = esc_html__( 'Page not Found', 'direo' );
	}
	elseif ( is_home() ) {
		if ( get_option( 'page_for_posts' ) ) {
			$title = get_the_title( get_option( 'page_for_posts' ) );
		}
		else {
			$title = apply_filters( 'aztheme_blog_title', esc_html__( 'All Posts', 'direo' ) );
		}
	}
	elseif ( is_archive() ) {
		$title = get_the_archive_title();
	}
	else {
		$title = get_the_title();
	}

	return apply_filters( 'aztheme_page_title', $title );
}

function direo_lwm_get_the_title( $post_id, $js_data = '' ) {
	$data = array();

	if ( $js_data && ! empty( $js_data ) ) {
		parse_str( $js_data, $data );
	} else if ( $_GET ) {
		$data = $_GET;
	} else if ( $_POST ) {
		$data = $_POST;
	}

	$string  = ( isset( $data['q'] ) && ! empty( $data['q'] ) ) ? $data['q'] : '';
	$address = ( isset( $data['address'] ) && ! empty( $data['address'] ) ) ? $data['address'] : '';
	$in_cat  = ( isset( $data['in_cat'] ) && ! empty( $data['in_cat'] ) ) ? $data['in_cat'] : '';

	if ( ! $data ) {
		return get_the_title( $post_id );
	} else if ( ! empty( $address ) ) {

		if ( ! empty( $string ) ) {
			return sprintf( __( '%s <span>in</span> %s', 'direo' ), $string, $address );
		} else {
			return $address;
		}
	} else if ( ! empty( $in_cat ) ) {
		$term = get_term( $in_cat );

		if ( ! empty( $string ) ) {
			return sprintf( __( '%s <span>at</span> %s', 'direo' ), $string, $term->name );
		} else {
			return $term->name;
		}
	} else if ( ! empty( $string ) ) {
		return sprintf( __( 'Search results <span>for</span> %s', 'direo' ), $string );
	} else {
		return get_the_title( $post_id );
	}
}

function get_direo_single_listing_badge_data( $listing ) {

	$badges = $listing->header_data['options']['content_settings']['listing_title'];

	return array(
		'new_badge'      => isset( $badges['display_new_badge'] ) ? $badges['display_new_badge'] : '',
		'popular_badge'  => isset( $badges['display_popular_badge'] ) ? $badges['display_popular_badge'] : '',
		'featured_badge' => isset( $badges['display_featured_badge'] ) ? $badges['display_featured_badge'] : '',
	);
}

function direo_get_svg( $filename ) {
	$dir      = 'img/svg';
	$filename = $filename . '.svg';
	$file     = direo_get_file_path( $filename, $dir );
	$svg      = file_get_contents( $file );
	$svg      = trim( $svg );

	return $svg;
}