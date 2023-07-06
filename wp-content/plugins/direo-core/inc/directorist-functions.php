<?php
/**
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

// Listings view as.
function direo_listings_view_as() { ?>
	<div class="view-mode">
		<a class="action-btn ab-grid" href="<?php echo add_query_arg( 'view', 'grid' ); ?>">
			<span class="la la-th-large"></span>
		</a>
		<a class="action-btn ab-list" href="<?php echo add_query_arg( 'view', 'list' ); ?>">
			<span class="la la-th-list"></span>
		</a>
		<a class="action-btn ab-map" href="<?php echo add_query_arg( 'view', 'map' ); ?>">
			<span class="la la-map"></span>
		</a>
	</div>
	<?php
}

add_filter( 'atbdp_listings_view_as', 'direo_listings_view_as', 10, 3 );

// View as of "listing with map view".
function direo_listings_map_view_as() {
	$listing_map_view = class_exists( 'Directorist_Base' ) ? get_directorist_option( 'listing_map_view', 'grid' ) : '';
	$view_as          = isset( $_POST['view_as'] ) ? $_POST['view_as'] : $listing_map_view;
	?>
	<div class="view-mode-2 view-as">
		<a data-view="grid" class="action-btn-2 ab-grid map-view-grid <?php echo 'grid' == $view_as ? esc_html( 'active' ) : ''; ?>">
			<span class="la la-th-large"></span>
		</a>
		<a data-view="list" class="action-btn-2 ab-list map-view-list <?php echo 'list' == $view_as ? esc_html( 'active' ) : ''; ?>">
			<span class="la la-list"></span>
		</a>
	</div>
	<?php
}

add_filter( 'bdmv_view_as', 'direo_listings_map_view_as' );

// Author avatar URL.
function direo_get_avatar_url( $author_id, $size ) {
	$match      = '';
	$get_avatar = get_avatar( $author_id, $size );
	preg_match( "/src='(.*?)'/i", $get_avatar, $matches );
	if ( $matches ) {
		if ( array_key_exists( '1', $matches ) ) {
			$match = ( $matches[1] );
		}
	}
	return $match;
}

// Listing Home Search Button Add.
function direo_search_form_fields( $more = 'yes' ) {
	if ( ! class_exists( 'Directorist_Base' ) ) {
		return;
	}
	$require_text = get_directorist_option( 'require_search_text' ) ? 'required' : '';
	$require_cat  = get_directorist_option( 'require_search_category' ) ? 'required' : '';
	$require_loc  = get_directorist_option( 'require_search_location' ) ? 'required' : '';

	$search_location_address     = get_directorist_option( 'search_location_address', 'address' );
	$search_fields               = get_directorist_option( 'search_tsc_fields', array( 'search_text', 'search_category', 'search_location' ) );
	$search_placeholder          = get_directorist_option( 'search_placeholder', esc_attr_x( 'What are you looking for?', 'placeholder', 'direo-core' ) );
	$search_category_placeholder = get_directorist_option( 'search_category_placeholder', esc_html__( 'Select a category', 'direo-core' ) );
	$search_location_placeholder = get_directorist_option( 'search_location_placeholder', esc_html__( 'Select a location', 'direo-core' ) );
	$search_listing_text         = get_directorist_option( 'search_listing_text', esc_html__( 'Search', 'direo-core' ) );
	$display_more_filter_search  = get_directorist_option( 'search_more_filter', 1 );

	$query_args = array(
		'parent'             => 0,
		'term_id'            => 0,
		'hide_empty'         => 0,
		'orderby'            => 'name',
		'order'              => 'asc',
		'show_count'         => 0,
		'single_only'        => 0,
		'pad_counts'         => true,
		'immediate_category' => 0,
		'active_term_id'     => 0,
		'ancestors'          => array(),
	);

	if ( in_array( 'search_text', $search_fields ) ) {
		?>
		<div class="single_search_field search_query">
			<input class="form-control search_fields" type="text" name="q" <?php echo esc_attr( $require_text ); ?> autocomplete="off" placeholder="<?php echo esc_html( $search_placeholder ); ?>">
		</div>
		<?php
	}

	if ( in_array( 'search_category', $search_fields ) ) {
		?>
		<div class="single_search_field search_category">
			<select <?php echo esc_attr( $require_cat ); ?> name="in_cat" class="search_fields form-control" id="at_biz_dir-category">
				<option value=""><?php echo esc_html( $search_category_placeholder ); ?></option>
				<?php echo search_category_location_filter( $query_args, ATBDP_CATEGORY ); ?>
			</select>
		</div>
		<?php
	}

	if ( in_array( 'search_location', $search_fields ) ) {
		if ( 'listing_location' == $search_location_address ) {
			?>
			<div class="single_search_field search_location">
				<select <?php echo esc_attr( $require_loc ); ?> name="in_loc" class="search_fields form-control" id="at_biz_dir-location">
					<option value=""> <?php echo esc_html( $search_location_placeholder ); ?> </option>
					<?php echo search_category_location_filter( $query_args, ATBDP_LOCATION ); ?>
				</select>
			</div>
			<?php
		} else {
			wp_enqueue_script( 'atbdp-geolocation' );
			$address = ! empty( $_GET['address'] ) ? $_GET['address'] : '';
			?>
			<div class="single_search_field atbdp_map_address_field">
				<div class="atbdp_get_address_field">
					<input type="text" id="address" name="address" autocomplete="off" value="<?php echo esc_attr( $address ); ?>" placeholder="<?php echo esc_html( $search_location_placeholder ); ?>" <?php echo esc_attr( $require_loc ); ?> class="form-control location-name">
					<span class="atbd_get_loc la la-crosshairs"></span>
				</div>
				<?php
				$select_listing_map = get_directorist_option( 'select_listing_map', 'google' );
				if ( 'google' != $select_listing_map ) {
					echo '<div class="address_result"></div>';
				}
				?>
				<input type="hidden" id="cityLat" name="cityLat" value="" />
				<input type="hidden" id="cityLng" name="cityLng" value="" />
			</div>
			<?php
		}
	}
	?>

	<div class="atbd_submit_btn">
		<button type="submit" class="btn_search"> <?php echo esc_attr( $search_listing_text ); ?> </button>
		<?php if ( $more && $display_more_filter_search ) { ?>
			<button class="more-filter"><span class="<?php atbdp_icon_type( true ); ?>-filter"></span></button>
		<?php } ?>
	</div>
	<?php
}

add_action( 'atbdp_search_form_fields', 'direo_search_form_fields' );

// More filter search field.
function direo_more_filter_search_form() {
	if ( ! class_exists( 'Directorist_Base' ) ) {
		return;
	}
	$search_filters             = get_directorist_option( 'search_filters', array( 'search_reset_filters', 'search_apply_filters' ) );
	$reset_filters_text         = get_directorist_option( 'sresult_reset_text', esc_html__( 'Reset Filters', 'direo-core' ) );
	$apply_filters_text         = get_directorist_option( 'sresult_apply_text', esc_html__( 'Apply Filters', 'direo-core' ) );
	$search_more_filters_fields = get_directorist_option( 'search_more_filters_fields', array( 'search_price', 'search_price_range', 'search_rating', 'search_tag', 'search_custom_fields', 'radius_search' ) );

	$tag_label               = get_directorist_option( 'tag_label', esc_html__( 'Tag', 'direo-core' ) );
	$address_label           = get_directorist_option( 'address_label', esc_html__( 'Address', 'direo-core' ) );
	$fax_label               = get_directorist_option( 'fax_label', esc_html__( 'Fax', 'direo-core' ) );
	$email_label             = get_directorist_option( 'email_label', esc_html__( 'Email', 'direo-core' ) );
	$website_label           = get_directorist_option( 'website_label', esc_html__( 'Website', 'direo-core' ) );
	$zip_label               = get_directorist_option( 'zip_label', esc_html__( 'Zip', 'direo-core' ) );
	$currency                = get_directorist_option( 'g_currency', 'USD' );
	$c_symbol                = atbdp_currency_symbol( $currency );
	$search_location_address = get_directorist_option( 'search_location_address', 'address' );
	?>

	<div class="ads_float">
		<div class="ads-advanced">
			<form action="<?php echo ATBDP_Permalink::get_search_result_page_link(); ?>" role="form">
				<?php if ( in_array( 'search_price', $search_more_filters_fields ) || in_array( 'search_price_range', $search_more_filters_fields ) ) { ?>
					<div class="form-group ">
						<label class=""><?php esc_html_e( 'Price Range', 'direo-core' ); ?></label>
						<div class="price_ranges">
							<?php
							if ( in_array( 'search_price', $search_more_filters_fields ) ) {
								$min = isset( $_GET['price'] ) ? $_GET['price'][0] : '';
								$max = isset( $_GET['price'] ) ? $_GET['price'][1] : '';
								?>
								<div class="range_single"><input type="text" name="price[0]" class="form-control" placeholder="<?php esc_html_e( 'Minss Price', 'direo-core' ); ?>" value="<?php echo esc_attr( $min ); ?>"></div>
								<div class="range_single"><input type="text" name="price[1]" class="form-control" placeholder="<?php esc_html_e( 'Max Price', 'direo-core' ); ?>" value="<?php echo esc_attr( $max ); ?>"></div>
								<?php
							}

							if ( in_array( 'search_price_range', $search_more_filters_fields ) ) {
								?>
								<div class="price-frequency">
									<label class="pf-btn">
										<input type="radio" name="price_range" value="bellow_economy" 
										<?php
										if ( isset( $_GET['price_range'] ) && 'bellow_economy' == isset( $_GET['price_range'] ) ) {
											echo esc_html( "checked='checked'" );
										}
										?>
										 >
										<span><?php echo esc_attr( $c_symbol ); ?></span>
									</label>
									<label class="pf-btn">
										<input type="radio" name="price_range" value="economy" 
										<?php
										if ( isset( $_GET['price_range'] ) && 'economy' == isset( $_GET['price_range'] ) ) {
											echo esc_html( "checked='checked'" );
										}
										?>
										 >
										<span><?php echo esc_attr( $c_symbol . $c_symbol ); ?></span>
									</label>
									<label class="pf-btn">
										<input type="radio" name="price_range" value="moderate" 
										<?php
										if ( isset( $_GET['price_range'] ) && 'moderate' == isset( $_GET['price_range'] ) ) {
											echo esc_html( "checked='checked'" );
										}
										?>
										 >
										<span><?php echo esc_attr( $c_symbol . $c_symbol . $c_symbol ); ?></span>
									</label>
									<label class="pf-btn">
										<input type="radio" name="price_range" value="skimming" 
										<?php
										if ( isset( $_GET['price_range'] ) && 'skimming' == isset( $_GET['price_range'] ) ) {
											echo esc_html( "checked='checked'" );
										}
										?>
										 >
										<span><?php echo esc_attr( $c_symbol . $c_symbol . $c_symbol . $c_symbol ); ?></span>
									</label>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}

				if ( in_array( 'search_rating', $search_more_filters_fields ) ) {
					?>
					<div class="form-group">
						<label for="filter-ratings"><?php esc_html_e( 'Filter by Ratings', 'direo-core' ); ?></label>
						<select id="filter-ratings" name='search_by_rating' class="select-basic form-control">
							<option value=""><?php esc_html_e( 'Select Ratings', 'direo-core' ); ?></option>
							<option value="5" 
							<?php
							if ( isset( $_GET['search_by_rating'] ) && '5' == isset( $_GET['search_by_rating'] ) ) {
								echo esc_html( 'selected' );
							}
							?>
							 >
								<?php esc_html_e( '5 Star', 'direo-core' ); ?>
							</option>
							<option value="4" 
							<?php
							if ( isset( $_GET['search_by_rating'] ) && '4' == isset( $_GET['search_by_rating'] ) ) {
								echo esc_html( 'selected' );
							}
							?>
							 >
								<?php esc_html_e( '4 Star & Up', 'direo-core' ); ?>
							</option>
							<option value="3" 
							<?php
							if ( isset( $_GET['search_by_rating'] ) && '3' == isset( $_GET['search_by_rating'] ) ) {
								echo esc_html( 'selected' );
							}
							?>
							 >
								<?php esc_html_e( '3 Star & Up', 'direo-core' ); ?>
							</option>
							<option value="2" 
							<?php
							if ( isset( $_GET['search_by_rating'] ) && '2' == isset( $_GET['search_by_rating'] ) ) {
								echo esc_html( 'selected' );
							}
							?>
							 >
								<?php esc_html_e( '2 Star & Up', 'direo-core' ); ?>
							</option>
							<option value="1" 
							<?php
							if ( isset( $_GET['search_by_rating'] ) && '1' == isset( $_GET['search_by_rating'] ) ) {
								echo esc_html( 'selected' );
							}
							?>
							 >
								<?php esc_html_e( '1 Star & Up', 'direo-core' ); ?>
							</option>
						</select>
					</div>
					<?php
				}

				if ( in_array( 'search_open_now', $search_more_filters_fields ) && in_array( 'directorist-business-hours/bd-business-hour.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					?>
					<div class="form-group">
						<label><?php esc_html_e( 'Open Now', 'direo-core' ); ?></label>
						<div class="check-btn">
							<div class="btn-checkbox">
								<label>
									<input type="checkbox" name="open_now" value="open_now" 
									<?php
									if ( isset( $_GET['open_now'] ) && 'open_now' == isset( $_GET['open_now'] ) ) {
										echo esc_html( "checked='checked'" );
									}
									?>
									 >
									<span><i class="fa fa-clock-o"></i><?php esc_html_e( 'Open Now', 'direo-core' ); ?> </span>
								</label>
							</div>
						</div>
					</div>
					<?php
				}

				if ( 'map_api' == $search_location_address && in_array( 'radius_search', $search_more_filters_fields ) ) {
					$default_radius_distance = get_directorist_option( 'search_default_radius_distance', 0 );
					?>
					<div class="form-group">
						<div class="atbdp-range-slider-wrapper">
							<span><?php _e( 'Radius Search', 'direo-core' ); ?></span>
							<div>
								<div id="atbdp-range-slider"></div>
							</div>
							<p class="atbd-current-value"></p>
						</div>
						<input type="hidden" class="atbdrs-value" name="miles" value="<?php echo ! empty( $default_radius_distance ) ? $default_radius_distance : 0; ?>" />
					</div>
					<?php
				}

				if ( in_array( 'search_tag', $search_more_filters_fields ) ) {
					$terms = get_terms( ATBDP_TAGS );
					if ( $terms ) {
						?>
						<div class="form-group ads-filter-tags">
							<label><?php echo $tag_label ? esc_attr( $tag_label ) : esc_html__( 'Tags', 'direo-core' ); ?></label>
							<div class="bads-custom-checks">
								<?php
								$rand = rand();
								foreach ( $terms as $term ) {
									?>
									<div class="custom-control custom-checkbox checkbox-outline checkbox-outline-primary">
										<input type="checkbox" class="custom-control-input" name="in_tag[]" value="<?php echo $term->term_id; ?>" id="<?php echo $rand . $term->term_id; ?>">
										<span class="check--select"></span>
										<label for="<?php echo $rand . $term->term_id; ?>" class="custom-control-label">
											<?php echo $term->name; ?>
										</label>
									</div>
									<?php
								}
								?>
							</div>
							<a href="#" class="more-or-less sml"> <?php esc_html_e( 'Show More', 'direo-core' ); ?> </a>
						</div>
						<?php
					}
				}

				if ( in_array( 'search_custom_fields', $search_more_filters_fields ) ) {
					?>
					<div id="atbdp-custom-fields-search" class="form-group ads-filter-tags atbdp-custom-fields-search">
						<?php do_action( 'wp_ajax_atbdp_custom_fields_search', isset( $_GET['in_cat'] ) ? $_GET['in_cat'] : 0 ); ?>
					</div>
					<?php
				}

				if ( in_array( 'search_website', $search_more_filters_fields ) || in_array( 'search_email', $search_more_filters_fields ) || in_array( 'search_phone', $search_more_filters_fields ) || in_array( 'search_fax', $search_more_filters_fields ) || in_array( 'search_address', $search_more_filters_fields ) || in_array( 'search_zip_code', $search_more_filters_fields ) ) {
					?>
					<div class="form-group">
						<div class="bottom-inputs">
							<?php if ( in_array( 'search_website', $search_more_filters_fields ) ) { ?>
								<div>
									<input type="text" name="website" placeholder="<?php echo $website_label ? esc_attr( $website_label ) : esc_html__( 'Website', 'direo-core' ); ?>" value="<?php echo isset( $_GET['website'] ) ? $_GET['website'] : ''; ?>" class="form-control">
								</div>
								<?php
							}
							if ( in_array( 'search_email', $search_more_filters_fields ) ) {
								?>
								<div>
									<input type="text" name="email" placeholder="<?php echo $email_label ? esc_attr( $email_label ) : esc_html__( 'Email', 'direo-core' ); ?>" value="<?php echo isset( $_GET['email'] ) ? esc_attr( $_GET['email'] ) : ''; ?>" class="form-control">
								</div>
								<?php
							}
							if ( in_array( 'search_phone', $search_more_filters_fields ) ) {
								?>
								<div>
									<input type="text" name="phone" placeholder="<?php esc_html_e( 'Phone Number', 'direo-core' ); ?>" value="<?php echo isset( $_GET['phone'] ) ? esc_attr( $_GET['phone'] ) : ''; ?>" class="form-control">
								</div>
								<?php
							}
							if ( in_array( 'search_fax', $search_more_filters_fields ) ) {
								?>
								<div>
									<input type="text" name="fax" placeholder="<?php echo $fax_label ? esc_attr( $fax_label ) : esc_html__( 'Fax', 'direo-core' ); ?>" value="<?php echo isset( $_GET['fax'] ) ? esc_attr( $_GET['fax'] ) : ''; ?>" class="form-control">
								</div>
								<?php
							}
							if ( in_array( 'search_address', $search_more_filters_fields ) ) {
								?>
								<div class="atbdp_map_address_field">
									<input type="text" name="address" id="address" value="<?php echo isset( $_GET['address'] ) ? esc_attr( $_GET['address'] ) : ''; ?>" placeholder="<?php echo $address_label ? esc_attr( $address_label ) : esc_html__( 'Address', 'direo-core' ); ?>" class="form-control location-name">
									<div id="address_result"> <ul></ul></div>
									<input type="hidden" id="cityLat" name="cityLat" />
									<input type="hidden" id="cityLng" name="cityLng" />
								</div>
								<?php
							}
							if ( in_array( 'search_zip_code', $search_more_filters_fields ) ) {
								?>
								<div>
									<input type="text" name="zip_code" placeholder="<?php echo $zip_label ? esc_attr( $zip_label ) : esc_html__( 'Zip/Post Code', 'direo-core' ); ?>" value="<?php echo isset( $_GET['zip_code'] ) ? esc_attr( $_GET['zip_code'] ) : ''; ?>" class="form-control">
								</div>
								<?php
							}
							?>
						</div>
					</div>

					<?php
				}
				if ( in_array( 'search_reset_filters', $search_filters ) || in_array( 'search_apply_filters', $search_filters ) ) {
					?>
					<div class="bdas-filter-actions">
						<?php if ( in_array( 'search_reset_filters', $search_filters ) ) { ?>
							<button type="reset" class="btn btn-outline btn-outline-primary btn-sm">
								<?php echo $reset_filters_text ? esc_attr( $reset_filters_text ) : esc_html__( 'Reset Filters', 'direo-core' ); ?>
							</button>
							<?php
						}
						if ( in_array( 'search_apply_filters', $search_filters ) ) {
							?>
							<button type="submit" class="btn btn-primary btn-sm">
								<?php echo $apply_filters_text ? esc_attr( $apply_filters_text ) : esc_html__( 'Apply Filters', 'direo-core' ); ?>
							</button>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</form>
		</div>
	</div>
	<?php
}

// Directorist quick search.
function direo_quick_search() {
	$more = get_directorist_option( 'search_more_filter', 1 );
	ob_start();
	?>
	<div class="atbd_wrapper ads-advaced--wrapper">
		<div class="row">
			<div class="col-lg-10 offset-lg-1 quick-search atbd_wrapper">
				<form action="<?php echo ATBDP_Permalink::get_search_result_page_link(); ?>" role="form" class="breadcrumb_quick_search">
					<div class="atbd_seach_fields_wrapper">
						<div class="atbdp-search-form">
							<?php direo_search_form_fields(); ?>
						</div>
					</div>
					<?php $more ? direo_more_filter_search_form() : ''; ?>
				</form>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'direo_quick_search_form', 'direo_quick_search' );

// Listing Business Hour badge Move.
function direo_listings_review_price() {
	if ( ! class_exists( 'Directorist_Base' ) ) {
		return;
	}

	$price                   = get_post_meta( get_the_ID(), '_price', true );
	$is_disable_price        = get_directorist_option( 'disable_list_price' );
	$display_review          = get_directorist_option( 'enable_review', 1 );
	$display_price           = get_directorist_option( 'display_price', 1 );
	$atbd_listing_pricing    = get_post_meta( get_the_ID(), '_atbd_listing_pricing', true );
	$display_pricing_field   = get_directorist_option( 'display_pricing_field', 1 );
	$price_range             = get_post_meta( get_the_ID(), '_price_range', true );
	$post_id                 = get_the_ID();
	$bdbh                    = get_post_meta( $post_id, '_bdbh', true );
	$enable247hour           = get_post_meta( $post_id, '_enable247hour', true );
	$disable_bz_hour_listing = get_post_meta( $post_id, '_disable_bz_hour_listing', true );
	$business_hours          = ! empty( $bdbh ) ? atbdp_sanitize_array( $bdbh ) : array(); // arrays of days and times if exist
	$plan_hours              = true;
	?>

	<div class="atbd_listing_meta">
		<?php
		if ( $display_review || $display_price && ( $price || $price_range ) ) {

			if ( $display_review ) {
				$average = ATBDP()->review->get_average( get_the_ID() );
				echo sprintf( '<span class="atbd_meta atbd_listing_rating">%s<i class="%s-star"></i></span>', esc_attr( $average ), atbdp_icon_type() );
			}

			$atbd_listing_pricing = $atbd_listing_pricing ? $atbd_listing_pricing : '';

			if ( $display_price && $display_pricing_field ) {
				if ( $price_range && ( 'range' === $atbd_listing_pricing ) ) {
					echo atbdp_display_price_range( $price_range );
				} else {
					echo atbdp_display_price( $price, $is_disable_price, $currency = null, $symbol = null, $c_position = null, $echo = false );
				}
			}
		}
		?>

		<span class="atbd_upper_badge">
			<?php
			if ( is_fee_manager_active() ) {
				$plan_hours = is_plan_allowed_business_hours( get_post_meta( $post_id, '_fm_plans', true ) );
			}
			if ( is_business_hour_active() && $plan_hours && ! $disable_bz_hour_listing ) {
				$open = get_directorist_option( 'open_badge_text', esc_html__( 'Open Now', 'direo-core' ) );
				if ( $enable247hour ) {
					echo sprintf( ' <span class="atbd_badge atbd_badge_open">%s</span>', esc_attr( $open ) );
				} else {
					echo BD_Business_Hour()->show_business_open_close( $business_hours );
				}
			}
			?>
		</span>

	</div>
	<?php
}

add_filter( 'atbdp_listings_review_price', 'direo_listings_review_price' );
add_filter( 'atbdp_listings_list_review_price', 'direo_listings_review_price' );


// Listing Business Featured badge Move.

function direo_upper_badge( $content ) {
	$featured                   = get_post_meta( get_the_ID(), '_featured', true );
	$display_feature_badge_cart = get_directorist_option( 'display_feature_badge_cart', 1 );
	$feature_badge_text         = get_directorist_option( 'feature_badge_text', 'Featured' );
	$display_popular_badge_cart = get_directorist_option( 'display_popular_badge_cart', 1 );
	$popular_badge_text         = get_directorist_option( 'popular_badge_text', 'Popular' );
	$popular_listing_id         = atbdp_popular_listings( get_the_ID() );
	?>

	<span class="atbd_upper_badge">
		<?php
		echo new_badge();
		if ( $featured && ! empty( $display_feature_badge_cart ) ) {
			$featured = sprintf( '<span class="atbd_badge atbd_badge_featured">%s</span>', esc_attr( $feature_badge_text ) );
			echo apply_filters( 'atbdp_featured_badge', $featured );
		}
		if ( $display_popular_badge_cart && ( $popular_listing_id === get_the_ID() ) ) {
			echo sprintf( '<span class="atbd_badge atbd_badge_popular">%s</span>', esc_attr( $popular_badge_text ) );
		}
		?>
	</span>
	<?php
}

add_filter( 'atbdp_upper_badges', 'direo_upper_badge', 20, 1 );

// Sidebar Name.
function direo_right_sidebar_name() {
	return esc_html__( 'Single Listing Widgets', 'direo-core' );
}

add_filter( 'atbdp_right_sidebar_name', 'direo_right_sidebar_name' );


/*Add Single Listing Description Content. */
function direo_before_listing_section() {
	$title = get_directorist_option( 'direo_details_text' );
	?>
	<div class="atbd_content_module atbd_listing_details atbd_listing_gallery">
		<?php if ( $title ) { ?>
			<div class="atbd_content_module__tittle_area">
				<div class="atbd_area_title">
					<h4><span class="la la-image"></span> <?php echo esc_attr( $title ); ?> </h4>
				</div>
			</div>
		<?php } ?>
		<div class="atbdb_content_module_contents"> <?php echo function_exists( 'get_plasma_slider' ) ? get_plasma_slider() : ''; ?> </div>
	</div>
	<?php
}

add_action( 'atbdp_after_single_listing_details_section', 'direo_before_listing_section' );

// Directorist single listing details title.
function direo_single_listings_settings_fields( $settings ) {
	$new_setting = array(
		'type'    => 'textbox',
		'name'    => 'direo_details_text',
		'label'   => esc_html__( 'Section Title of Listing Gallery', 'direo-core' ),
		'default' => esc_html__( 'Gallery', 'direo-core' ),
	);
	array_push( $settings, $new_setting );
	return $settings;
}

add_filter( 'atbdp_single_listings_settings_fields', 'direo_single_listings_settings_fields' );


// Directorist atbdp_search_listing dependency maintain.
function direo_search_listing_jquery_dependency( $search_dependency ) {
	$dependency = array( 'bootstrap' );
	array_push( $search_dependency, $dependency );

	return $dependency;
}

add_filter( 'atbdp_search_listing_jquery_dependency', 'direo_search_listing_jquery_dependency' );


// Directorist atbdp_search_listing dependency maintain.
function my_login_fail() {
	$referrer = $_SERVER['HTTP_REFERER'];
	if ( $referrer && ! strstr( $referrer, 'wp-login' ) && ! strstr( $referrer, 'wp-admin' ) ) {
		wp_redirect( $referrer . '?login=failed' );
		exit;
	}
}

add_action( 'wp_login_failed', 'my_login_fail' );

// Login and Register popup.
$quick_log_reg = get_theme_mod( 'quick_log_reg', true );
if ( $quick_log_reg ) {
	function login_for_booking() {
		$login_url = get_theme_mod( 'login_btn_url', false );
		if ( $login_url ) {
			return sprintf( '<a href="%s" class="access-link login-booking">%s</a>', esc_url( $login_url ), __( 'Logins for Booking', 'direo-core' ) );
		} else {
			return sprintf( '<a href="#" class="access-link login-booking" data-toggle="modal" data-target="#login_modal">%s</a>', __( 'Logins for Booking', 'direo-core' ) );
		}
	}
	add_filter( 'login_for_booking', 'login_for_booking' );

	function direo_listing_form_login_link() {
		$login_url = get_theme_mod( 'login_btn_url', false );
		$login     = get_theme_mod( 'login_btn', 'Sign in' );
		if ( $login_url ) {
			return sprintf( '<a href="%s" class="access-link">%s</a>', esc_url( $login_url ), esc_attr( $login ) );
		} else {
			return sprintf( '<a href="#" class="access-link" data-toggle="modal" data-target="#login_modal">%s</a>', esc_attr( $login ) );
		}
	}

	add_filter( 'atbdp_listing_form_login_link', 'direo_listing_form_login_link' );
	add_filter( 'atbdp_user_dashboard_login_link', 'direo_listing_form_login_link' );
	add_filter( 'atbdp_review_login_link', 'direo_listing_form_login_link' );
	add_filter( 'atbdp_claim_now_login_link', 'direo_listing_form_login_link' );
	add_filter( 'atbdp_login_page_link', 'direo_listing_form_login_link' );

	function direo_listing_form_signup_link() {
		$register_url = get_theme_mod( 'register_btn_url', false );
		$register     = get_theme_mod( 'register_btn', 'Sign Up' );

		if ( $register_url ) {
			return sprintf( '<a href="%s" class="access-link">%s</a>', esc_url( $register_url ), esc_attr( $register ) );
		} else {
			return sprintf( '<a href="#" class="access-link" data-toggle="modal"  data-target="#signup_modal">%s</a>', esc_attr( $register ) );
		}
	}

	add_filter( 'atbdp_listing_form_signup_link', 'direo_listing_form_signup_link' );
	add_filter( 'atbdp_user_dashboard_signup_link', 'direo_listing_form_signup_link' );
	add_filter( 'atbdp_review_signup_link', 'direo_listing_form_signup_link' );
	add_filter( 'atbdp_claim_now_registration_link', 'direo_listing_form_signup_link' );
	add_filter( 'atbdp_signup_page_link', 'direo_listing_form_signup_link' );
}


function replace_in_content( $content, $order_id = 0, $listing_id = 0, $user = null ) {
	if ( ! $listing_id ) {
		$listing_id = (int) get_post_meta( $order_id, '_listing_id', true );
	}
	if ( ! $user ) {
		$post_author_id = get_post_field( 'post_author', $listing_id );
		$user           = get_userdata( $post_author_id );
	} else {
		if ( ! $user instanceof WP_User ) {
			$user = get_userdata( (int) $user );
		}
	}

	$site_name    = get_option( 'blogname' );
	$site_url     = site_url();
	$date_format  = get_option( 'date_format' );
	$time_format  = get_option( 'time_format' );
	$current_time = current_time( 'timestamp' );
	$find_replace = array(
		'==NAME=='      => ! empty( $user->display_name ) ? $user->display_name : '',
		'==USERNAME=='  => ! empty( $user->user_login ) ? $user->user_login : '',
		'==SITE_NAME==' => $site_name,
		'==SITE_LINK==' => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
		'==SITE_URL=='  => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
		'==TODAY=='     => date_i18n( $date_format, $current_time ),
		'==NOW=='       => date_i18n( $date_format . ' ' . $time_format, $current_time ),
	);
	$c            = nl2br( strtr( $content, $find_replace ) );

	return $c;
}

function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
	$user_password = get_user_meta( $user->ID, '_atbdp_generated_password', true );

	$sub                                       = get_directorist_option( 'email_sub_registration_confirmation', __( 'Registration Confirmation!', 'direo-core' ) );
	$body                                      = get_directorist_option(
		'email_tmpl_registration_confirmation',
		__(
			'
Dear User,

Congratulations! Your registration is completed!

This email is sent automatically for information purpose only. Please do not respond to this.
You can login now using the below credentials:

',
			'direo-core'
		)
	);
	$body                                      = replace_in_content( $body, null, null, $user );
	$wp_new_user_notification_email['subject'] = sprintf( '%s', $sub );
	$wp_new_user_notification_email['message'] = preg_replace( '/<br \/>/', '', $body ) . '
                
' . __( 'Username:', 'direo-core' ) . " $user->user_login
" . __( 'Password:', 'direo-core' ) . " $user_password";
	return $wp_new_user_notification_email;
}

function atbdp_wp_mail_from_name() {
	$site_name = get_option( 'blogname' );
	return $site_name;
}

add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );
add_filter( 'wp_mail_from_name', 'atbdp_wp_mail_from_name' );

// All Listing Location and Category image size.
function direo_location_image_size() {
	return array( 545, 270 );
}

add_filter( 'atbdp_location_image_size', 'direo_location_image_size' );

function direo_category_image_size() {
	return array( 350, 280 );
}

add_filter( 'atbdp_category_image_size', 'direo_category_image_size' );

// replace list view container class.
function direo_list_view_container() {
	return esc_html( 'list_view_container' );
}

add_filter( 'list_view_container', 'direo_list_view_container' );

// Remove category container fluid.
function direo_cat_container_fluid() {
	return esc_html( 'row' );
}

add_filter( 'atbdp_cat_container_fluid', 'direo_cat_container_fluid' );

// removed unnecessary hook.
add_filter( 'atbdp_search_home_container_fluid', '__return_false' );
add_filter( 'atbdp_extension_license_active_submenu_permission', '__return_false' );
add_filter( 'atbdp_public_profile_container_fluid', '__return_false' );
add_filter( 'atbdp_public_profile_container_fluid', '__return_false' );
add_filter( 'atbdp_login_message_container_fluid', '__return_false' );
add_filter( 'atbdp_add_listing_container_fluid', '__return_false' );
add_filter( 'atbdp_payment_receipt_container_fluid', '__return_false' );
add_filter( 'atbdp_registration_container_fluid', '__return_false' );
add_filter( 'atbdp_deshboard_container_fluid', '__return_false' );
add_filter( 'atbdp_listings_header_container_fluid', '__return_false' );
add_filter( 'atbdp_listings_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_single_cat_header_container_fluid', '__return_false' );
add_filter( 'atbdp_single_cat_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_single_cat_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_single_loc_header_container_fluid', '__return_false' );
add_filter( 'atbdp_single_loc_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_single_tag_header_container_fluid', '__return_false' );
add_filter( 'atbdp_single_tag_header_container_fluid', '__return_false' );
add_filter( 'atbdp_single_tag_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_search_result_header_container_fluid', '__return_false' );
add_filter( 'atbdp_search_result_grid_container_fluid', '__return_false' );
add_filter( 'atbdp_map_container', '__return_false' );
add_filter( 'atbdp_search_listing_button', '__return_false' );
add_filter( 'atbdp_grid_lower_badges', '__return_false', 20, 1 );
add_filter( 'atbdp_single_lower_badges', '__return_false', 20, 1 );
add_filter( 'atbdp_single_listing_slider', '__return_false' );
add_filter( 'atbdp_header_before_image_slider', '__return_false' );
add_filter( 'atbdp_before_listing_title', '__return_false' );
add_filter( 'atbdp_listing_title', '__return_false' );
add_filter( 'atbdp_single_listing_gallery_section', '__return_false' );
add_filter( 'atbdp_listing_tagline', '__return_false' );
add_filter( 'include_style_settings', '__return_false' );
add_filter( 'wallet_dashboard_content_area_title', '__return_false' );
add_filter( 'atbdp_show_gallery_image_in_plan', '__return_false' );
add_filter( 'atbdp_plan_gallery_compare', '__return_true' );
add_filter( 'atbdp_licence_menu_for_business_hours', '__return_false' );
add_filter( 'atbdp_licence_menu_for_claim_listing', '__return_false' );
add_filter( 'atbdp_licence_menu_for_faqs', '__return_false' );
add_filter( 'atbdp_licence_menu_for_recaptcha', '__return_false' );
add_filter( 'atbdp_licence_menu_for_listings_map', '__return_false' );
add_filter( 'atbdp_licence_menu_for_live_chat', '__return_false' );
add_filter( 'atbdp_licence_menu_for_paypal', '__return_false' );
add_filter( 'atbdp_licence_menu_for_post_your_need', '__return_false' );
add_filter( 'atbdp_licence_menu_for_pricing_plan', '__return_false' );
add_filter( 'atbdp_licence_menu_for_social_login', '__return_false' );
add_filter( 'atbdp_licence_menu_for_stripe', '__return_false' );
add_filter( 'atbdp_licence_menu_for_woo_pricing_plans', '__return_false' );

// removed all unnecessary options.
function direo_remove_gateway_settings( $settings ) {
	unset( $settings['gateway_promotion'] );
	return $settings;
}

add_filter( 'atbdp_gateway_settings_fields', 'direo_remove_gateway_settings' );

function direo_remove_extension_promotion_settings( $settings ) {
	unset( $settings['extension_promotion_set'] );
	return $settings;
}

add_filter( 'atbdp_extension_settings_fields', 'direo_remove_extension_promotion_settings' );


function direo_search_result_settings_fields( $settings ) {
	unset( $settings['search_view_as'] );
	unset( $settings['search_view_as_items'] );
	unset( $settings['search_sort_by'] );
	return $settings;
}

add_filter( 'atbdp_search_result_settings_fields', 'direo_search_result_settings_fields' );

function direo_pages_settings_fields( $settings ) {
	unset( $settings['single_listing_page'] );
	return $settings;
}

add_filter( 'atbdp_pages_settings_fields', 'direo_pages_settings_fields' );

function direo_create_custom_pages( $settings ) {
	unset( $settings['single_listing_page'] );
	return $settings;
}

add_filter( 'atbdp_create_custom_pages', 'direo_create_custom_pages' );

function direo_remove_custom_pages( $settings ) {
	unset( $settings['single_listing_page'] );
	return $settings;
}

add_filter( 'atbdp_pages_settings_fields', 'direo_remove_custom_pages' );

function direo_general_listings_submenus( $settings ) {
	unset( $settings['style_setting'] );
	return $settings;
}

add_filter( 'atbdp_general_listings_submenus', 'direo_general_listings_submenus' );


function direo_listings_settings_fields( $settings ) {
	unset( $settings['listings_display_filter'] );
	return $settings;
}

add_filter( 'atbdp_search_settings_fields', 'direo_listings_settings_fields' );

function direo_search_settings_fields( $settings ) {
	unset( $settings['search_home_background'] );
	return $settings;
}

add_filter( 'atbdp_search_settings_fields', 'direo_search_settings_fields' );

function direo_atbdp_settings_menus( $settings ) {
	unset( $settings['style_settings_menu'] );
	return $settings;
}

add_filter( 'atbdp_settings_menus', 'direo_atbdp_settings_menus' );


function single_listing_template( $settings ) {
	unset( $settings['single_listing_template'] );
	unset( $settings['enable_single_location_taxonomy'] );
	return $settings;
}

add_filter( 'atbdp_single_listings_settings_fields', 'single_listing_template' );

// improved Listing cart bottom area.
function direo_listing_cat() {
	$cats             = get_the_terms( get_the_ID(), ATBDP_CATEGORY );
	$display_category = get_directorist_option( 'display_category', 1 );
	if ( $display_category ) {
		if ( $cats ) {
			?>
			<div class="atbd_content_left">
				<div class="atbd_listting_category">
					<?php
					$cat_icon      = '';
					$category_icon = ! empty( $cats ) ? get_cat_icon( $cats[0]->term_id ) : atbdp_icon_type() . '-tags';
					$icon_type     = substr( $category_icon, 0, 2 );
					$icon          = 'la' === $icon_type ? $icon_type . ' ' . $category_icon : 'fa ' . $category_icon;
					if ( 'no' != $icon_type ) {
						$cat_icon = sprintf( '<span class="%s"></span>', esc_attr( $icon ) );
					}
					echo sprintf( '<a href="%s">%s %s</a>', ATBDP_Permalink::atbdp_get_category_page( $cats[0] ), $cat_icon, $cats[0]->name );

					$totalTerm = count( $cats );
					if ( $totalTerm > 1 ) {
						?>
						<div class="atbd_cat_popup">
							<?php echo sprintf( '<span>%s%s</span>', esc_html( '+' ), esc_attr( $totalTerm - 1 ) ); ?>
							<div class="atbd_cat_popup_wrapper">
								<?php
								$output = array();
								foreach ( array_slice( $cats, 1 ) as $cat ) {
									$link  = ATBDP_Permalink::atbdp_get_category_page( $cat );
									$space = str_repeat( ' ', 1 );

									$category_icon = ! empty( $cats ) ? get_cat_icon( $cat->term_id ) : atbdp_icon_type() . '-tags';
									$icon_type     = substr( $category_icon, 0, 2 );
									$icon          = 'la' === $icon_type ? $icon_type . ' ' . $category_icon : 'fa ' . $category_icon;

									$output[] = sprintf( "%s<span><i class='%s'></i><a href='%s'>%s<span>,</span></a></span>", esc_attr( $space ), esc_attr( $icon ), esc_url( $link ), esc_attr( $cat->name ) );
								}
								wp_reset_postdata();
								echo join( $output );
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="atbd_content_left">
				<div class="atbd_listting_category">
					<?php echo sprintf( '<a href="#"> <span class="%s-tags"></span>%s</a>', atbdp_icon_type( false ), esc_html__( 'Uncategorized', 'direo-core' ) ); ?>
				</div>
			</div>
			<?php
		}
	}
}

add_filter( 'atbdp_grid_footer_catViewCount', 'direo_listing_cat' );


// dd author section before listing list view title.
function direo_list_view_author() {
	$display_author_image = get_directorist_option( 'display_author_image', 1 );
	if ( $display_author_image ) {
		$author_id    = get_the_author_meta( 'ID' );
		$author       = get_userdata( $author_id );
		$u_pro_pic_id = get_user_meta( $author_id, 'pro_pic', true );
		$u_pro_pic    = wp_get_attachment_image_src( $u_pro_pic_id, 'thumbnail' );
		$avatar_img   = get_avatar( $author_id, 32 );
		$image_alt    = function_exists( 'direo_get_image_alt' ) ? direo_get_image_alt( $u_pro_pic_id ) : '';
		?>

		<a href="<?php echo ATBDP_Permalink::get_user_profile_page_link( $author_id ); ?>" class="atbd_tooltip" aria-label="<?php echo $author->first_name . ' ' . $author->last_name; ?>">
			<?php
			if ( $u_pro_pic ) {
				echo sprintf( '<img class="c_tooltip" src="%s" alt="%s">', esc_url( $u_pro_pic[0] ), $image_alt );
			} else {
				echo wp_kses_post( $avatar_img );
			}
			?>
		</a>

		<?php
	}
}

// Listing grid view and list view footer section.
function direo_listing_grid_footer_content() {
	$display_view_count = get_directorist_option( 'display_view_count', 1 );
	$post_view          = get_post_meta( get_the_Id(), '_atbdp_post_views_count', true );
	$cats               = get_the_terms( get_the_ID(), ATBDP_CATEGORY );
	$display_category   = get_directorist_option( 'display_category', 1 );
	if ( $cats && $display_category ) {
		?>
		<div class="atbd_listing_bottom_content">
			<?php
			direo_listing_cat();
			if ( $display_view_count ) {
				?>
				<ul class="atbd_content_right">
					<li class="atbd_count">
						<span class="<?php echo atbdp_icon_type(); ?>-eye"></span>
						<?php echo $post_view ? esc_attr( $post_view ) : 0; ?>
					</li>
				</ul>
				<?php
			}
			?>
		</div>
		<?php
	}
}

add_action( 'atbdp_listings_grid_cat_view_count', 'direo_listing_grid_footer_content' );

function direo_listing_grid_list_footer_content() {
	$display_view_count = get_directorist_option( 'display_view_count', 1 );
	?>
	<div class="atbd_listing_bottom_content">
		<?php
		direo_listing_cat();
		$post_view = get_post_meta( get_the_Id(), '_atbdp_post_views_count', true );
		if ( $display_view_count ) {
			?>
			<ul class="atbd_content_right">
				<li class="atbd_count">
					<?php
					echo sprintf( '<span class="%s-eye"></span>', atbdp_icon_type() );
					echo $post_view ? esc_attr( $post_view ) : 0;
					?>
				</li>
				<li class="atbd_author"><?php direo_list_view_author(); ?></li>
			</ul>
			<?php
		}
		?>
	</div>
	<?php
}

add_filter( 'atbdp_listings_list_cat_view_count_author', 'direo_listing_grid_list_footer_content' );


// listing with map view copyright section.
function direo_footer_listing_with_map() {
	$footer_style = get_post_meta( get_the_ID(), 'footer_style', true );
	$default      = '©' . date( 'Y' ) . ' Direo. Made with <span class="la la-heart-o"></span> by <a href="#">WpWax</a>';
	$copy_right   = get_theme_mod( 'copy_right', $default );
	echo sprintf( '<div class="listing_map_footer bg-%s">%s</div>', esc_attr( $footer_style ), apply_filters( 'get_the_content', $copy_right ) );
}

add_action( 'bdmv-after-listing', 'direo_footer_listing_with_map' );

// Search Listing found title.
function direo_listing_search_title( $result_title ) {
	$title    = '';
	$query    = ( isset( $_GET['q'] ) && ( '' !== $_GET['q'] ) ) ? ucfirst( $_GET['q'] ) : '';
	$category = ( isset( $_GET['in_cat'] ) && ( '' !== $_GET['in_cat'] ) ) ? ucfirst( $_GET['in_cat'] ) : '';
	$location = ( isset( $_GET['in_loc'] ) && ( '' !== $_GET['in_loc'] ) ) ? ucfirst( $_GET['in_loc'] ) : '';
	$category = get_term_by( 'id', $category, ATBDP_CATEGORY );
	$location = get_term_by( 'id', $location, ATBDP_LOCATION );

	$in_s_string_text = ! empty( $query ) ? sprintf( esc_html__( '%s', 'direo-core' ), $query ) : '';
	$in_cat_text      = ! empty( $category ) ? sprintf( esc_html__( ' %1$s %2$s ', 'direo-core' ), ! empty( $query ) ? '<span>' . esc_html__( 'from', 'direo-core' ) . '</span>' : '', $category->name ) : '';
	$in_loc_text      = ! empty( $location ) ? sprintf( esc_html__( '%1$s %2$s', 'direo-core' ), ! empty( $query ) ? '<span>' . esc_html__( 'in', 'direo-core' ) . '</span>' : '', $location->name ) : '';

	if ( $query || $category || $location ) {
		$title = $in_s_string_text . $in_cat_text . $in_loc_text;
	}

	return sprintf( esc_html__( $result_title, 'direo-core' ) . '%s', wp_kses_post( $title ) );
}

// Listing header title.
function direo_after_filter_button_in_listings_header( $ex_title ) {
	if ( function_exists( 'direo_directorist_pages' ) && direo_directorist_pages( 'search_result_page' ) ) {
		return sprintf( '<div class="listing-header"><h4>%s</h4>%s</div>', direo_listing_search_title( false ), $ex_title );
	} else {
		return sprintf( '<div class="listing-header"><h4>%s</h4>%s</div>', esc_html__( 'All Items', 'direo-core' ), $ex_title );
	}
}

add_action( 'atbdp_total_listings_found_text', 'direo_after_filter_button_in_listings_header' );

// Author avatar size of author page.
function direo_avatar_size() {
	return 120;
}

add_filter( 'atbdp_avatar_size', 'direo_avatar_size' );

// Set dashboard template.
function direo_set_user_dashboard_page( $page_template ) {
	$dashboardPageId = class_exists( 'Directorist_Base' ) ? get_directorist_option( 'user_dashboard' ) : '';
	global $post;
	$page_id = $post ? $post->ID : '';
	switch ( $page_id ) {
		case $dashboardPageId:
			$args = array(
				'ID'           => $page_id,
				'post_content' => '',
			);
			update_post_meta( $page_id, '_wp_page_template', 'template-parts/dashboard.php' );
			wp_update_post( $args );
			$page_template = get_template_directory() . '/template-parts/dashboard.php';
			break;
	}
	return $page_template;
}

add_action( 'page_template', 'direo_set_user_dashboard_page' );

/*Set default listing detail template*/
function direo_single_template( $template ) {
	$template = 'current_theme_template';
	return $template;
}

add_filter( 'atbdp_single_template', 'direo_single_template' );

// Remove directorist elementor widgets
add_filter( 'atbdp_elementor_widgets_activated', '__return_false' );

// added single listing tag icon
function direo_single_listing_tag_icon() {
	return atbdp_icon_type() . '-list-alt';
}

add_filter( 'atbdp_single_listing_tag_icon', 'direo_single_listing_tag_icon' );

// added single listing tags icon
function direo_single_listing_tags_icon() {
	 return atbdp_icon_type() . '-check-square';
}

add_filter( 'atbdp_single_listing_tags_icon', 'direo_single_listing_tags_icon' );

if ( ! function_exists( 'direo_booking_db' ) ) {
	function direo_booking_db() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		update_option( 'direo_booking', true );
		$sql = "
	CREATE TABLE IF NOT EXISTS {$wpdb->prefix}directorist_booking (
		`ID` bigint(20) UNSIGNED  NOT NULL auto_increment,
		`bookings_author` bigint(20) UNSIGNED NOT NULL,
		`owner_id` bigint(20) UNSIGNED NOT NULL,
		`listing_id` bigint(20) UNSIGNED NOT NULL,
		`date_start` datetime DEFAULT NULL,
		`date_end` datetime DEFAULT NULL,
		`comment` text,
		`order_id` bigint(20) UNSIGNED DEFAULT NULL,
		`status` varchar(100) DEFAULT NULL,
		`type` text,
		`created` datetime DEFAULT NULL,
		`expiring` datetime DEFAULT NULL,
		`price` mediumint(8) UNSIGNED DEFAULT NULL,
		PRIMARY KEY  (ID)
	) $collate;
	";

		dbDelta( $sql );
	}
}

function is_create_booking_database() {
	 $booking_database = get_option( 'direo_booking' );
	if ( ! $booking_database ) {
		direo_booking_db();
	}
}

// register_activation_hook(__FILE__, 'direo_booking_db');
add_action( 'plugins_loaded', 'is_create_booking_database' );

function all_listings_wrapper() {
	echo ' all-listings-carousel owl-carousel ';
}

// popular category team.
function search_home_popular_category( $counter ) {
	 $color = 'color-' . $counter;
	echo 'class="' . $color . '"';
}
add_action( 'search_home_popular_category', 'search_home_popular_category' );

function atbdp_popular_category_loop( $counter ) {
	$counter++;
	return $counter = $counter > 6 ? $counter = 1 : $counter;
}
add_filter( 'atbdp_popular_category_loop', 'atbdp_popular_category_loop' );

/* =================After Form Builder ===========================********************************************************************/

// Add review & category in dashboard table.
function directorist_dashboard_listing_th_2(){
	echo '<th class="directorist-table-review">' . __( 'Review', 'direo' ) . '</th>';
	echo '<th class="directorist-table-review">' . __( 'Category', 'direo' ) . '</th>';
}
add_action( 'directorist_dashboard_listing_th_2', 'directorist_dashboard_listing_th_2' );

function directorist_dashboard_listing_td_2() {
	$review = get_directorist_option( 'enable_review', 1 );
	if ( ! $review ) return;
	$reviews_count = ATBDP()->review->db->count( array( 'post_id' => get_the_ID() ) );
	$cats          = get_the_terms( get_the_ID(), ATBDP_CATEGORY );
	$cats          = $cats ? $cats : array();
	?>
	<td class="directorist_dashboard_rating">
		<ul class="rating">
			<?php
			$average   = ATBDP()->review->get_average( get_the_ID() );
			$star      = '<li><span class="la la-star rate_active"></span></li>';
			$half_star = '<li><span class="la la-star-half-o rate_active"></span></li>';
			$none_star = '<li><span class="la la-star-o"></span></li>';

			if ( is_int( $average ) ) {
				for ( $i = 1; $i <= 5; $i++ ) {

					if ( $i <= $average ) {
						echo wp_kses_post( $star );
					} else {
						echo wp_kses_post( $none_star );
					}
				}
			} elseif ( ! is_int( $average ) ) {
				$exp       = explode( '.', $average );
				$float_num = $exp[0];

				for ( $i = 1; $i <= 5; $i++ ) {
					if ( $i <= $average ) {
						echo wp_kses_post( $star );
					} elseif ( ! empty( $average ) && $i > $average && $i <= $float_num + 1 ) {
						echo wp_kses_post( $half_star );
					} else {
						echo wp_kses_post( $none_star );
					}
				}
			}

			$review_title = '';
			if ( $reviews_count ) {
				if ( 1 < $reviews_count ) {
					$review_title = $reviews_count . esc_html__( ' Reviews', 'direo' );
				} else {
					$review_title = $reviews_count . esc_html__( ' Review', 'direo' );
				}
			}
			?>

			<li class="reviews">
				<span class="atbd_count">
					<?php echo sprintf( '(<b>%s</b> %s )', esc_attr( $average . '/5' ), esc_attr( $review_title ) ); ?>
				</span>
			</li>
		</ul>
	</td>

	<td class="directorist_dashboard_category">
		<ul>
			<li>
				<?php
				if ( $cats ) {
					foreach ( $cats as $cat ) {
						$link          = ATBDP_Permalink::atbdp_get_category_page( $cat );
						$space         = str_repeat( ' ', 1 );
						$category_icon = $cats ? get_cat_icon( $cat->term_id ) : atbdp_icon_type() . '-tags';
						$icon_type     = substr( $category_icon, 0, 2 );
						$icon          = 'la' === $icon_type ? $icon_type . ' ' . $category_icon : 'fa ' . $category_icon;
						echo sprintf( '%s<span><i class="%s"></i><a href="%s">%s</a></span>', esc_attr( $space ), esc_attr( $icon ), esc_url( $link ), esc_attr( $cat->name ) );
					}
				}
				?>
			</li>
		</ul>
	</td>
	<?php
}
add_action( 'directorist_dashboard_listing_td_2', 'directorist_dashboard_listing_td_2' );

function atbdp_all_listings_meta_count( $html, $term ) {
	$total = $term->count;
	$str = ( 1 == $total ) ? __( ' Listing', 'direo' ) : __( ' Listings', 'direo' );
	return '<span class="listing-count"> ' . $total . '<span class="listing-label">' . $str . '</span>' . '</span>';
}
add_filter( 'atbdp_all_locations_after_location_name', 'atbdp_all_listings_meta_count', 10, 2 );

function atbdp_all_listings_cat_meta_count( $html, $term ) {
	return $term->count;
}
add_filter( 'atbdp_all_categories_after_category_name', 'atbdp_all_listings_cat_meta_count', 10, 2 );


function directorist_listing_types() {
	$all_types = directory_types();
	$types = [];
	foreach( $all_types as $type) {
		$types[ $type->slug ] = $type->name;
	}
	return $types;
}